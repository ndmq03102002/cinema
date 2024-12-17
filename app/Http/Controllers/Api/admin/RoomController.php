<?php

namespace App\Http\Controllers\Api\admin;

use App\Enums\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CinemaRequest;
use App\Models\Cinema;
use App\Models\Room;
use App\Models\Seat;
use App\Repositories\admin\Room\RoomInterface;
use App\Repositories\admin\Seat\SeatInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public $roomInterface;
    public $seatInterface;
    public function __construct
    (
        RoomInterface $roomInterface,
        SeatInterface $seatInterface
    )
    {
        $this->roomInterface = $roomInterface;
        $this->seatInterface = $seatInterface;
    }

    public function roomIndex(){
        if (Auth::user()->role_id == 1) {
            $check = 1;
            // Lấy danh sách rạp và eager load danh sách phòng, sắp xếp phòng theo tên
            $cinemas = Cinema::with(['room' => function ($query) {
                $query->orderBy('name', 'asc');
            }])->get();
        
            // Tạo một mảng mới để chứa danh sách phòng đã sắp xếp
            $rooms = [];
            foreach ($cinemas as $cinema) {
                // Kiểm tra xem $cinema->room có phải là một Collection hay không
                    $rooms = array_merge($rooms, $cinema->room->all());
            }
        } else if (Auth::user()->role_id == 2) {
            $check = 0;
            $cinema_id = Auth::user()->cinema_id;
            $rooms = Room::where('cinema_id', $cinema_id)->orderBy('name', 'asc')->get();
        }
        
        return view('admin.room.index',compact('rooms'));
    }
    // public function getRoomsByCinema($cinema_id)
    // {
    //     // Lấy danh sách phòng thuộc rạp
    //     $rooms = Room::where('cinema_id', $cinema_id)->get(['id', 'name']);
    //     if ($rooms->isEmpty()) {
    //         return response()->json([], 200); // Không có phòng
    //     }
    //     return response()->json($rooms, 200); // Trả về JSON danh sách phòng
    // }


    public function getRooms(Request $request) {
        $cinemaId = $request->input('cinema_id');
        $rooms = Room::where('cinema_id', $cinemaId)->pluck('name', 'id');
        return response()->json($rooms);
    }
    public function roomCreate(){
        $config['method'] = 'create';
        if(Auth::user()->role_id == 1){
            $check = 1;
            $cinema = Cinema::pluck('name','id');
        }else if(Auth::user()->role_id == 2){
            $check = 0;
            $cinema_id = Auth::user()->cinema_id;
            $cinema = Cinema::find($cinema_id);
        }
        
        return view('admin.room.create',compact('config','cinema','check'));
    }
    public function roomStore(Request $request)
{
    try {
        DB::beginTransaction();

        // Kiểm tra nếu phòng đã tồn tại
        if($this->roomInterface->getRoomCheck($request)){
            return redirect()
                ->back()
                ->with('error', 'Phòng đã tồn tại.');
        }

        // Tạo phòng mới
        $roomId = $this->roomInterface->createRoom($request);

        // Lấy dữ liệu ghế từ seat_list trong request
        $seatList = json_decode($request->input('seat_list'), true); // Chuyển đổi từ chuỗi JSON sang mảng

        // Kiểm tra nếu seat_list không rỗng và là mảng hợp lệ
        if ($seatList && is_array($seatList)) {
            foreach ($seatList as $data) {
                // Gọi phương thức creatSeat để tạo ghế
                $this->seatInterface->creatSeat($data, $roomId);
            }
        }

        // Commit transaction nếu không có lỗi
        DB::commit();

        return redirect()
            ->route('room.create')
            ->with('success', trans('messages.success.success'));

    } catch (\Throwable $th) {
        // Rollback nếu có lỗi
        DB::rollBack();

        return redirect()
            ->route('room.create')
            ->with('error', $th->getMessage());
    }
}


    public function roomEdit($id)
    {
        $config['method'] = 'edit';
        if(Auth::user()->role_id == 1){
            $check = 1;
            $cinema = Cinema::pluck('name','id');
        }else if(Auth::user()->role_id == 2){
            $check = 0;
            $cinema_id = Auth::user()->cinema_id;
            $cinema = Cinema::find($cinema_id);
        }
        
        $room = Room::where('id', $id)->first();
        $seats = Seat::with('seatType')
            ->where('room_id', $room->id)
            ->get();
        $seatMap = json_decode($room->seat_map);
        $seatId = 0;
        $data = [];

        foreach ($seatMap as $item) {
            $rowData = [];
            foreach ($item as $item2) {
                if ($item2 == 1) {  // Chỉ xử lý khi có dữ liệu (giá trị 1)
                    // Kiểm tra xem $seats[$seatId] có tồn tại hay không
                    if (isset($seats[$seatId])) {
                        $seatData = $seats[$seatId];
                        $rowData[] = [
                            'seat_type_id' => $seatData->seatType->id,
                            'seat_code' => $seatData->seat_code,
                        ];
                        $seatId++;
                    }
                }
            }
            // Chỉ thêm rowData nếu có ghế dữ liệu
            if (!empty($rowData)) {
                $data[] = $rowData;
            }
        }

        return view('admin.room.create', compact('config', 'room', 'data', 'cinema', 'check'));
    }

  
    public function roomUpdate($id, Request $request){
        try {
            DB::beginTransaction();
            $data = $request->all();
            $room = Room::find($id);
            
            // Lấy cinema_id của phòng hiện tại để kiểm tra phòng trùng trong cùng chi nhánh
            $cinemaId = $room->cinema_id;

            // Kiểm tra nếu phòng đã tồn tại trong cùng chi nhánh (cinema_id), trừ phòng hiện tại
            $existingRoom = Room::where('name', $request->input('name'))
                                ->where('cinema_id', $cinemaId)  // Kiểm tra trong cùng chi nhánh
                                ->where('id', '!=', $id)         // Trừ phòng hiện tại
                                ->first();

            if ($existingRoom) {
                // Nếu có phòng trùng tên, trả về thông báo lỗi
                return redirect()
                    ->route('room.edit', ['id' => $id])
                    ->with('error', 'Tên phòng đã tồn tại trong chi nhánh này.');
            }

             // Cập nhật thông tin phòng
            $room->cinema_id = $request->input('cinema_id');
            $room->name = $request->input('name');
            $room->seat_map = $request->input('seat_map');
            $room->save();

            // Lấy dữ liệu ghế từ seat_list trong request
            $seatList = json_decode($request->input('seat_list'), true); // Chuyển đổi từ chuỗi JSON sang mảng

            // Kiểm tra nếu seat_list không rỗng và là mảng hợp lệ
            if ($seatList && is_array($seatList)) {
                // Xóa các ghế cũ của phòng trước khi thêm mới
                $room->seat()->delete(); // Xóa tất cả các ghế của phòng hiện tại

                // Thêm các ghế mới vào phòng
                foreach ($seatList as $data) {
                    $room->seat()->create([
                        'seat_type_id' => $data['seat_type_id'],
                        'seat_code' => $data['seat_code']
                    ]);
                }
            }
            DB::commit();
            return redirect()
            ->route('room.index')
            ->with('success', trans('messages.success.success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
            ->route('room.index')
            ->with('error', $th->getMessage());
        }

    }
    public function roomDestroy(string $id){
        
         // Tìm phòng theo ID
         $room = Room::find($id);

         // Xóa tất cả các ghế của phòng
         $room->seat()->delete(); // Xóa tất cả các ghế liên quan đến phòng
 
         $room->movieShowTime()->delete(); // Xóa tất cả các lịch chiếu của phòng
         // Xóa phòng
         $room->delete();
        return redirect()->back()->with('success', 'Xóa room thành công.');
    }
    /**
     * @author son.nk
     * @OA\Post (
     *     path="/api/admin/room/create",
     *     tags={"Admin Quản lý phòng chiếu"},
     *     summary="Tạo mới",
     *     operationId="admin/room/create",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="cinema_id", type="string"),
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="seat_map", type="string"),
     *          @OA\Examples(
     *              summary="Examples",
     *              example = "Examples",
     *              value = {
     *                  "name": "Cinema 1",
     *                  "cinema_id": 1,
     *                  "seat_map": "[[1,1,0],[1,1,1],[1,1,1]]",
     *                  "seat_list": {
     *                          {
     *                               "seat_type_id": 1,
     *                               "seat_code": "A1"
     *                           },
     *                           {
     *                                "seat_type_id": 2,
     *                                "seat_code": "A2"
     *                           },
     *                           {
     *                                 "seat_type_id": 1,
     *                                 "seat_code": "B1"
     *                           },
     *                           {
     *                                  "seat_type_id": 2,
     *                                  "seat_code": "B2"
     *                            },
     *                            {
     *                                  "seat_type_id": 3,
     *                                  "seat_code": "B3"
     *                            },
     *                            {
     *                                   "seat_type_id": 2,
     *                                   "seat_code": "C1"
     *                             },
     *                             {
     *                                  "seat_type_id": 2,
     *                                  "seat_code": "C2"
     *                            },
     *                            {
     *                                   "seat_type_id": 2,
     *                                   "seat_code": "C3"
     *                             },
     *                       }
     *                  },
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
    public function createRoom(Request $request){
        try {
            DB::beginTransaction();
            if($this->roomInterface->getRoomCheck($request)){
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => trans('messages.errors.room.exist'),
                    'data' => []
                ], 200);
            }
            $roomId = $this->roomInterface->createRoom($request);
            $seat = $request->all();
            foreach ($seat['seat_list'] as $data){
                $this->seatInterface->creatSeat($data, $roomId);
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
//    /**
//     * @author son.nk
//     * @OA\Post (
//     *     path="/api/admin/cinema/update/{id}",
//     *     tags={"Admin Quản lý rạp chiếu phim"},
//     *     summary="Cập nhật",
//     *     operationId="admin/cinema/update",
//     *     @OA\Parameter(
//     *          name="id",
//     *          in="path",
//     *          description="ID cinema",
//     *          required=true,
//     *          @OA\Schema(type="integer")
//     *      ),
//     *      @OA\RequestBody(
//     *          @OA\JsonContent(
//     *              type="object",
//     *              @OA\Property(property="name", type="string"),
//     *              @OA\Property(property="address", type="string"),
//     *              @OA\Examples(
//     *                  summary="Examples",
//     *                  example = "Examples",
//     *                  value = {
//     *                     "name": "CinemaEase Hà Đông",
//     *                     "address": "Số 10 - Trần Phú - Hà Đông - Hà Nội",
//     *                  },
//     *              ),
//     *           ),
//     *      ),
//     *     @OA\Response(
//     *         response=200,
//     *         description="Success",
//     *             @OA\JsonContent(
//     *              @OA\Property(property="message", type="string", example="Success."),
//     *          )
//     *     ),
//     * )
//     */
//    public function updateCinema($id, CinemaRequest $request){
//        try {
//            DB::beginTransaction();
//            $data = $request->all();
//            $query = Cinema::find($id);
//            if (!$query) {
//                return response()->json([
//                    'status' => Constant::FALSE_CODE,
//                    'message' => trans('messages.errors.cinema.id_found'),
//                    'data' => []
//                ], Constant::SUCCESS_CODE);
//            }
//            $query->update($data);
//            DB::commit();
//            return response()->json([
//                'status' => Constant::SUCCESS_CODE,
//                'message' => trans('messages.success.success'),
//                'data' => []
//            ], Constant::SUCCESS_CODE);
//
//        } catch (\Throwable $th) {
//            return response()->json([
//                'status' => Constant::FALSE_CODE,
//                'message' => $th->getMessage(),
//                'data' => []
//            ], Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
//    }
//    /**
//     * @author son.nk
//     * @OA\Get (
//     *     path="/api/admin/cinema/get-list",
//     *     tags={"Admin Quản lý rạp chiếu phim"},
//     *     summary="Lấy danh sách",
//     *     operationId="admin/cinema/get-list",
//     *     @OA\Response(
//     *         response=200,
//     *         description="Success",
//     *             @OA\JsonContent(
//     *              @OA\Property(property="message", type="string", example="Success."),
//     *          )
//     *     ),
//     * )
//     */
//    public function getListCinema(){
//        try {
//            $data = Cinema::all();
//            return response()->json([
//                'status' => Constant::SUCCESS_CODE,
//                'message' => trans('messages.success.success'),
//                'data' => $data
//            ], Constant::SUCCESS_CODE);
//
//        } catch (\Throwable $th) {
//            return response()->json([
//                'status' => Constant::FALSE_CODE,
//                'message' => $th->getMessage(),
//                'data' => []
//            ], Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
//    }
//
    /**
     * @OA\Get (
     *     path="/api/admin/room/get/{id}",
     *     tags={"Admin Quản lý phòng chiếu"},
     *     summary="Lấy theo id",
     *     operationId="admin/room/get",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID room",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function getRoom($id){
        try {
            $room = Room::find($id);
            if(!$room){
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Không tồn tại phòng chiếu',
                ],200);
            }
            $data = $this->roomInterface->getRoom($id);
            
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'success.success',
                'data' => $data
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
