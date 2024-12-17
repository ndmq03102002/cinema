<?php

namespace App\Http\Controllers\Api\admin;

use App\Enums\Constant;
use App\Http\Controllers\Controller;
use App\Repositories\admin\MovieShowtime\MovieShowTimeRepository;
use Illuminate\Http\Request;
use App\Models\MovieShowtime;
use App\Models\Movie;
use App\Models\Cinema;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\admin\MovieShowtimeRequest;

class MovieShowTimeController extends Controller
{
    public $movieShowTimeRepository;

    public function __construct
    (
        MovieShowTimeRepository $movieShowTimeRepository
    )
    {
        $this->movieShowTimeRepository = $movieShowTimeRepository;
    }
    public function movieshowtimeIndex()
    {
        if (Auth::user()->role_id == 1) {
            // Admin: Lấy tất cả suất chiếu, sắp xếp theo chi nhánh, phòng và thời gian
            $movieshowtimes = MovieShowtime::with(['movie', 'room.cinema']) // Eager load movie, room và cinema
                ->join('ci_room', 'ci_movie_show_time.room_id', '=', 'ci_room.id') // Join với bảng room
                ->join('ci_cinema', 'ci_room.cinema_id', '=', 'ci_cinema.id') // Join với bảng cinema
                ->orderBy('ci_cinema.name', 'asc') // Sắp xếp theo tên chi nhánh
                ->orderBy('ci_room.name', 'asc') // Sắp xếp theo tên phòng
                ->orderBy('ci_movie_show_time.start_date', 'asc')
                ->orderBy('ci_movie_show_time.start_time', 'asc') // Sắp xếp theo ngày và thời gian bắt đầu
                ->select('ci_movie_show_time.*') // Chỉ select các cột của bảng movie_show_time
                ->get();
        } else {
            // Nhân viên: Chỉ lấy suất chiếu của chi nhánh đó, sắp xếp theo phòng và thời gian
            $cinema_id = Auth::user()->cinema_id;
            $movieshowtimes = MovieShowtime::with(['movie', 'room']) // Eager load movie và room
                ->whereHas('room', function ($query) use ($cinema_id) {
                    $query->where('cinema_id', $cinema_id);
                })
                ->orderBy('room_id', 'asc') // Sắp xếp theo phòng
                ->orderBy('start_date', 'asc')
                ->orderBy('start_time', 'asc') // Sắp xếp theo ngày và thời gian bắt đầu
                ->get();
        }

        $movie = Movie::pluck('name', 'id'); // Cái này có thể không cần thiết trong view index
        $room = Room::pluck('name', 'id'); // Cái này có thể không cần thiết trong view index

        return view('admin.movieshowtime.index', compact('movieshowtimes', 'movie', 'room'));
    }
    public function movieshowtimeCreate(){
        $config['method'] = 'create';
        $movie = Movie::pluck('name','id');
    
        if(Auth::user()->role_id == 1) {
            // Admin: Lấy tất cả chi nhánh và phòng chiếu của chi nhánh đầu tiên (để hiển thị mặc định)
            $cinemas = Cinema::all(); // Lấy tất cả để chọn
            $selectedCinema = $cinemas->first(); // Lấy chi nhánh đầu tiên
            $rooms = Room::where('cinema_id', $selectedCinema->id)->pluck('name','id'); 
        } else {
            // Nhân viên: Chỉ lấy chi nhánh và phòng của nhân viên đó
            $cinema_id = Auth::user()->cinema_id;
            $selectedCinema = Cinema::find($cinema_id); //selectedCinema
            $cinemas = collect([$selectedCinema]); // $cinemas để truyền vào view.
            $rooms = Room::where('cinema_id', $cinema_id)->pluck('name','id');
        }
    
        return view('admin.movieshowtime.create', compact('config', 'rooms', 'movie', 'cinemas', 'selectedCinema'));
    }
    public function movieshowtimeStore(MovieShowtimeRequest $request)
    {
        try {
            DB::beginTransaction();
    
            // Lấy tất cả dữ liệu từ request
            $data = $request->all();
    
            // Kiểm tra xem showtime có bị trùng thời gian không
            $existingShowtime = MovieShowtime::where('room_id', $data['room_id'])
            ->where('start_date', $data['start_date'])
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                    ->where('end_time', '>', $data['start_time']);
            })
            ->exists();

    
            if ($existingShowtime) {
                // Nếu có showtime trùng, hiển thị lỗi và không tạo mới
                return redirect()
                    ->route('movieshowtime.create')
                    ->with('error', 'trùng thời gian');
            }
    
            // Tạo mới MovieShowtime nếu không trùng thời gian
            MovieShowtime::create($data);
    
            DB::commit();
    
            // Redirect về trang tạo showtime và hiển thị thông báo thành công
            return redirect()
                ->route('movieshowtime.create')
                ->with('success', trans('messages.success.success'));
    
        } catch (\Throwable $th) {
            DB::rollBack();
    
            // Nếu có lỗi, redirect lại và hiển thị thông báo lỗi
            return redirect()
                ->route('movieshowtime.create')
                ->with('error', $th->getMessage());
        }
    }
    

    public function movieshowtimeEdit(string $id)
    {
        $config['method'] = 'edit';
        $movieshowtime = MovieShowtime::findOrFail($id); // Tìm MovieShowtime, ném ngoại lệ nếu không tìm thấy
        $movie = Movie::pluck('name', 'id');

        if (Auth::user()->role_id == 1) {
            // Admin: Lấy tất cả chi nhánh và phòng chiếu của chi nhánh hiện tại của suất chiếu
            $cinemas = Cinema::all();
            $selectedCinema = $movieshowtime->room->cinema; // Lấy chi nhánh của phòng chiếu
            $rooms = Room::where('cinema_id', $selectedCinema->id)->pluck('name', 'id');
        } else {
            // Nhân viên: Chỉ lấy chi nhánh và phòng của nhân viên đó
            $cinema_id = Auth::user()->cinema_id;
            $selectedCinema = Cinema::find($cinema_id);
            $cinemas = collect([$selectedCinema]); // Để truyền vào view, giống như create
            $rooms = Room::where('cinema_id', $cinema_id)->pluck('name', 'id');
        }

        // Lấy room_id hiện tại của suất chiếu
        $selectedRoom = $movieshowtime->room_id;

        return view('admin.movieshowtime.create', compact('config', 'movieshowtime', 'movie', 'rooms', 'cinemas', 'selectedCinema', 'selectedRoom'));
    }
  
    public function movieshowtimeUpdate($id, MovieShowtimeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Lấy tất cả dữ liệu từ request
            $data = $request->all();

            // Kiểm tra xem showtime có bị trùng thời gian không (trừ chính bản ghi hiện tại)
            $existingShowtime = MovieShowtime::where('room_id', $data['room_id'])
                ->where('start_date', $data['start_date'])
                ->where(function ($query) use ($data) {
                    $query->where('start_time', '<', $data['end_time'])
                        ->where('end_time', '>', $data['start_time']);
                })
                ->where('id', '<>', $id) // Trừ chính bản ghi hiện tại
                ->exists();

            if ($existingShowtime) {
                // Nếu có showtime trùng, hiển thị lỗi và không cập nhật
                return redirect()
                    ->route('movieshowtime.index')
                    ->with('error', 'Trùng thời gian với showtime khác.');
            }

            // Cập nhật MovieShowtime nếu không trùng thời gian
            $query = MovieShowtime::find($id);
            if ($query) {
                $query->update($data);
            }

            DB::commit();

            // Redirect về trang index và hiển thị thông báo thành công
            return redirect()
                ->route('movieshowtime.index')
                ->with('success', trans('messages.success.success'));

        } catch (\Throwable $th) {
            DB::rollBack();

            // Nếu có lỗi, redirect lại và hiển thị thông báo lỗi
            return redirect()
                ->route('movieshowtime.index')
                ->with('error', $th->getMessage());
        }
    }

    public function movieshowtimeDestroy(string $id){
        MovieShowtime::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa movieshowtime thành công.');
    }
    /**
     * @author son.nk
     * @OA\Post (
     *     path="/api/admin/show-time/create",
     *     tags={"Admin Quản lý suất chiếu"},
     *     summary="Tạo mới",
     *     operationId="admin/shiw-time/create",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="movie_id", type="string"),
     *              @OA\Property(property="room_id", type="string"),
     *              @OA\Property(property="start_time", type="time"),
     *              @OA\Property(property="end_time", type="time"),
     *              @OA\Property(property="start_date", type="date"),
     *          @OA\Examples(
     *              summary="Examples",
     *              example = "Examples",
     *              value = {
     *                      "movie_id" : "1",
     *                      "room_id" : "1",
     *                      "start_time": "13:30:00",
     *                      "end_time": "15:50:00",
     *                      "start_date": "2024-10-20"
     *                  }
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *             @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success."),
     *          )
     *     ),
     * )
     */
    public function createShowTime(Request $request){
        try {
            DB::beginTransaction();
            if($this->movieShowTimeRepository->getShowtimeCheck($request)){
                return response()->json([
                    'status' => Constant::SUCCESS_CODE,
                    'message' => trans('messages.errors.show_time.exist'),
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }else{
                $this->movieShowTimeRepository->createShowTime($request);
            }
            DB::commit();
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => []
            ], Constant::SUCCESS_CODE);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
