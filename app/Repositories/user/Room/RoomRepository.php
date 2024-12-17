<?php

namespace App\Repositories\user\Room;

use App\Models\MovieShowtime;
use App\Models\Room;
use App\Models\Seat;
use App\Repositories\user\Ticket\TicketRepository;
use App\Repositories\user\WeeklyTicketPrices\WeeklyTicketPricesRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class RoomRepository{

    public $ticketRepository;

    public function __construct
    (
        TicketRepository $ticketRepository
    )
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function getRoom($id)
    {
        $showTime = MovieShowtime::where('id', $id)->first();

        $room = Room::where('id', $showTime->room_id)->first();

        $seats = Seat::with('seatType')
            ->where('room_id', $room->id)
            ->get();
        $seatMap = json_decode($room->seat_map);
        $data = [];
        $pattern = $room->cinema_id . '_' . $id . '_*';
        $keys = Redis::keys($pattern);
        $listSeats = [];
        foreach ($keys as $key) {
            $key = str_replace('laravel_database_', '', $key);
            $seatIdsJson = Redis::get($key);

            $seatIdsArray = json_decode($seatIdsJson, true);

            if (is_array($seatIdsArray)) {
                $listSeats = array_merge($listSeats, $seatIdsArray);
            }
        }
        $listSeatSold = $this->ticketRepository->getSeatSold($id);
        $arrayListSeatSold = [];
        foreach ($listSeatSold as $sold){
            $arrayListSeatSold[] = (int)$sold->seat_id ;
        }
        $seatId = 0;
        $ticketPrice = WeeklyTicketPricesRepository::getTicketPrice($showTime->start_date, $showTime->start_time);
        foreach ($seatMap as $item) {
            $rowData = [];
            foreach ($item as $item2) {
                if ($item2 == 0) {
                    $rowData[] = [
                        'seat_id' => null,
                        'seat_code' => null,
                        'seat_type_id' => null,
                        'seat_name' => null,
                        'status' => null,
                    ];
                } else {
                    $seatData = $seats[$seatId];
                    $seatItem = [
                        'seat_id' => $seatData->id,
                        'seat_code' => $seatData->seat_code,
                        'seat_type_id' => $seatData->seatType->id,
                        'seat_name' => $seatData->seatType->name,
                    ];
                    if (in_array($seatItem['seat_id'], $listSeats)) {
                        $seatItem['status'] = 1;
                    }
                    else if(in_array((int)$seatItem['seat_id'], $arrayListSeatSold)){
                        $seatItem['status'] = 2;
                    }
                    else {
                        $seatItem['status'] = 0;
                    }
                    if($seatData->seatType->id == 1){
                        $seatItem['ticket_price'] = (int)$ticketPrice + 10000;
                    }else{
                        $seatItem['ticket_price'] = (int)$ticketPrice;
                    }
                    $rowData[] = $seatItem;
                    $seatId++;
                }
            }
            $data[] = $rowData;
        }

        return [
            'room_name' => $room->name,
            'seat_list' => $data
        ];
    }
}
