<?php

namespace App\Repositories\user\Ticket;

use App\Models\Bill;
use App\Models\Food;
use App\Models\FoodBillJoin;
use App\Models\Movie;
use App\Models\Promotion;
use App\Models\Seat;
use App\Models\Account;
use App\Models\Ticket;
use App\Models\MovieShowtime    ;
use App\Repositories\user\WeeklyTicketPrices\WeeklyTicketPricesRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketConfirmationMail;
use Illuminate\Support\Facades\Auth;

class TicketRepository{

    function model(){
        return Ticket::class;
    }

    public function getDatareservation($key){
        list($cinemaId, $showTimeId, $userId, $time) = explode('_', $key, 4);
        $data = Redis::get($cinemaId . '_' . $showTimeId . '_' . $userId);
        return [
            'cinema_id' => $cinemaId,
            'show_time_id' => $showTimeId,
            'user_id' => $userId,
            'seat_list' => $data
        ];
    }

    // public function createBill($key, $amount)
    // {
    //     $data = self::getDatareservation($key);
    //     list($cinemaId, $showTimeId, $userId , $time) = explode('_', $key, 4);
    //     if($data['seat_list']){
    //         $randomNumber = mt_rand(10000, 99999);  // Tạo 5 số ngẫu nhiên
    //         $currentTime = Carbon::now()->format('YmdHis');  // Lấy ngày tháng năm + giờ phút giây (YmdHis)
    //         $ticketCode = $randomNumber . $currentTime;  // Kết hợp cả hai phần
    //         $billId = Bill::create([
    //             'ticket_code' =>  $ticketCode,
    //             'account_id' => (int)$data['user_id'],
    //             'cinema_id' => $data['cinema_id'],
    //             'total' => $amount,
    //             'status' => '1'
    //         ])->id;
    //         foreach (json_decode($data['seat_list']) as $key){
    //             $ticket = Ticket::create([
    //                 'seat_id' => $key,
    //                 'bill_id' => $billId,
    //                 'movie_showtime_id' => (int)$showTimeId,
    //                 'price' => 1000
    //             ]);
    //         }
    //         return $billId;
    //     }
    //     self::cancelReservation($key);
    //     return false;
    // }




    public function createBill($key, $amount)
    {
        $data = self::getDatareservation($key);
        list($cinemaId, $showTimeId, $userId , $time) = explode('_', $key, 4);
        $food = Redis::get('food_'. $userId . '_' . $cinemaId . '_' . $showTimeId);
        $extraId = Redis::get('extraData_'. $userId . '_' . $cinemaId . '_' . $showTimeId);
        $food = json_decode($food);
//        return $food;
        $userId = Auth::id();
        $promotion = Promotion::find($extraId);
        $promotion->users()->attach($userId);
        $promotion->decrement('quantity');
        if ($data['seat_list']) {
            $randomNumber = mt_rand(10000, 99999);  // Tạo 5 số ngẫu nhiên
            $currentTime = Carbon::now()->timestamp;  // Lấy ngày tháng năm + giờ phút giây (YmdHis)
            $ticketCode = $randomNumber . $currentTime;  // Kết hợp cả hai phần
            if(Auth::user()->role_id != 4){
                $billId = Bill::create([
                    'extra_id' => $extraId,
                    'ticket_code' => $ticketCode,
                    'account_id' => (int)$data['user_id'],
                    'cinema_id' => $data['cinema_id'],
                    'movie_show_time_id' => $showTimeId,
                    'total' => $amount,
                    'staff_check' => Auth::user()->id,
                    'status' => '1'
                ])->id;
            }else{
                // Tạo Bill
                $billId = Bill::create([
                    'ticket_code' => $ticketCode,
                    'account_id' => (int)$data['user_id'],
                    'cinema_id' => $data['cinema_id'],
                    'movie_show_time_id' => $showTimeId,
                    'total' => $amount,
                    'status' => '0'
                ])->id;
            }
            if($billId)
                $foodPrice = Food::find($food->food_id)->first();
                FoodBillJoin::create([
                    'bill_id' => $billId,
                    'food_id' => $food->food_id,
                    'quantity' => $food->food_quantity,
                    'total' => $food->food_quantity * $foodPrice['price']
                ]);

            $user = Auth::user()->id;
            $account = Account::find($user);
            if ($account) {
                $account->update(['cinema_id' => $data['cinema_id']]);
            }
            $seatIds = json_decode($data['seat_list']);
            $seatCodes = [];

            foreach ($seatIds as $seatId) {
                $showTime = MovieShowtime::find($showTimeId)->first();
                $ticketPrice = WeeklyTicketPricesRepository::getTicketPrice($showTime->start_date, $showTime->start_time);
                $seats = Seat::with('seatType')
                    ->where('id', $seatId)
                    ->first();
                if($seats->seatType->id == 1){
                    $price = (int)$ticketPrice + 10000;
                }else{
                    $price = (int)$ticketPrice;
                }
                // Tạo vé cho từng ghế
                Ticket::create([
                    'seat_id' => $seatId,
                    'bill_id' => $billId,
                    'movie_showtime_id' => (int)$showTimeId,
                    'price' => $price
                ]);

                // Lấy mã ghế từ bảng ci_seat
                $seat = Seat::find($seatId);
                if ($seat) {
                    $seatCodes[] = $seat->seat_code; // Thêm mã ghế vào danh sách
                }
            }
            $bill = Bill::find($billId);
            $cinema_name = $bill->cinema->name;
            $total_bill = $bill->total;
            $movieShowtime = MovieShowtime::find($showTimeId);
            $movie_name = $movieShowtime->movie->name;
            $room = $movieShowtime->room->name;
            $start_time = $movieShowtime->start_time;
            $seatListString = implode(', ', $seatCodes);

            $user = Account::find($data['user_id']);
            if ($user && $user->email && $seatListString) {
                // Gửi email cho người dùng
                Mail::to($user->email)->send(new TicketConfirmationMail($user->username, $ticketCode, $seatListString, $cinema_name, $movie_name, $room, $start_time, $total_bill));
            }

            return $billId;
        }

        self::cancelReservation($key);
        return false;
    }

    public function getSeatSold($showTimeId)
    {
        $data = Ticket::where('movie_showtime_id', $showTimeId)
            ->get();
        return $data;
    }

    public static function cancelReservation($key)
    {
        list($cinemaId, $showTimeId, $userId, $time) = explode('_', $key, 4);
        $data = Redis::del($cinemaId . '_' . $showTimeId . '_' . $userId);
        return 0;
    }

    public function getBill($memberId, $type){
        $bill = Bill::select('ci_bill.*', 'ci_cinema.name as cinema_name', 'ci_cinema.address as cinema_address')
            ->join('ci_cinema', 'ci_bill.cinema_id', '=', 'ci_cinema.id')
            ->where('ci_bill.account_id', $memberId)->where('ci_bill.updated_at', '>' , Carbon::now()->subMonths(12))->orderBy('ci_bill.created_at', 'DESC')->get();
        $response = [];
        foreach ($bill as $key){
            $ticket = Ticket::select(
                'ci_ticket.price',
                'ci_seat.seat_code',
                'ci_seat_type.name',
                'ci_movie_show_time.start_time',
                'ci_movie_show_time.end_time',
                'ci_movie.name as movie_name',
                'ci_movie.id as movie_id',
                'ci_movie.avatar as avatar',
                'ci_room.name as room',
                'ci_movie_show_time.start_date'
            )
                ->join('ci_seat', 'ci_ticket.seat_id', '=', 'ci_seat.id')
                ->join('ci_seat_type', 'ci_seat_type.id', '=', 'ci_seat.seat_type_id')
                ->join('ci_movie_show_time', 'ci_movie_show_time.id', '=', 'ci_ticket.movie_showtime_id')
                ->join('ci_movie', 'ci_movie_show_time.movie_id', '=', 'ci_movie.id')
                ->join('ci_room', 'ci_room.id', '=', 'ci_movie_show_time.room_id')
                ->where('ci_ticket.bill_id', '=', $key->id);
            if($type == 1){
                $ticket = $ticket->where(function ($query) {
                    $query->where('ci_movie_show_time.start_date', '>', Carbon::now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->where('ci_movie_show_time.start_date', '=', Carbon::now()->toDateString())
                                ->where('ci_movie_show_time.start_time', '>', Carbon::now()->format('H:i'));
                        });
                });
            }else{
                $ticket = $ticket->where(function ($query) {
                    $query->where('ci_movie_show_time.start_date', '<', Carbon::now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->where('ci_movie_show_time.start_date', '=', Carbon::now()->toDateString())
                                ->where('ci_movie_show_time.end_time', '<', Carbon::now()->format('H:i'));
                        });
                });
            }
            $total = $ticket->count();
            $ticket = $ticket->get();
            if($total > 0){
                $movie = Movie::find($ticket[0]['movie_id']);
                $data['bill_id'] = $key->id;
                $data['cinema_name'] = $key->cinema_name;
                $data['cinema_address'] = $key->cinema_address;
                $data['movie_avatar'] = $ticket[0]['avatar'];
                $data['movie_name'] = $ticket[0]['movie_name'];
                $data['movie_genre'] = $movie->movie_genre;
                $data['movie_duration'] = $movie->duration;
                $data['movie_rated'] = $movie->rated->name;
                $data['show_start_date'] = $ticket[0]['start_date'];
                $data['show_start_time'] = $ticket[0]['start_time'];
                $data['show_room'] = $ticket[0]['room'];
                $data['show_ticket_total'] = $total;
                $data['show_seat'] = '';
                foreach ($ticket as $k){
                    $data['show_seat'] = $data['show_seat'] . $k->seat_code . ', ';
                }
                $data['show_seat'] = rtrim($data['show_seat'], ', ');
                $data['total_price'] = $key->total;
                $response[] = $data;
            }
        }
        return $response;
    }

    public function getBillDetail($billId){
        $bill = Bill::select('ci_bill.*', 'ci_cinema.name as cinema_name', 'ci_cinema.address as cinema_address')
            ->join('ci_cinema', 'ci_bill.cinema_id', '=', 'ci_cinema.id')
            ->where('ci_bill.id', $billId)->orderBy('ci_bill.created_at', 'DESC')->first();
        $response['bill'] = $bill;
        foreach ($bill as $key){
            $ticket = Ticket::select(
                'ci_ticket.price',
                'ci_seat.seat_code',
                'ci_seat_type.name',
                'ci_movie_show_time.start_time',
                'ci_movie_show_time.end_time',
                'ci_movie.name as movie_name',
                'ci_movie.id as movie_id',
                'ci_movie.avatar as avatar',
                'ci_room.name as room',
                'ci_movie_show_time.start_date'
            )
                ->join('ci_seat', 'ci_ticket.seat_id', '=', 'ci_seat.id')
                ->join('ci_seat_type', 'ci_seat_type.id', '=', 'ci_seat.seat_type_id')
                ->join('ci_movie_show_time', 'ci_movie_show_time.id', '=', 'ci_ticket.movie_showtime_id')
                ->join('ci_movie', 'ci_movie_show_time.movie_id', '=', 'ci_movie.id')
                ->join('ci_room', 'ci_room.id', '=', 'ci_movie_show_time.room_id')
                ->where('ci_ticket.bill_id', '=', $billId);
            $total = $ticket->count();
            $ticket = $ticket->get();
            $response['movie'] = Movie::find($ticket[0]['movie_id']);
            $response['ticket'] = $ticket;
            $response['voucher'] = Promotion::find($bill['extra_id'])->first();
            $response['foods'] = Food::select('ci_foods.*', 'fbj.quantity', 'fbj.total')
                ->join('ci_food_bill_join as fbj', 'fbj.food_id', '=', 'ci_foods.id')
                ->where('fbj.bill_id', '=', $billId)->get();
        }
        return $response;
    }

}
