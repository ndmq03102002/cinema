<?php

namespace App\Http\Controllers\Api\admin;

use App\Enums\Constant;
use App\Http\Controllers\Controller;
use App\Repositories\admin\Room\RoomInterface;
use App\Repositories\admin\Seat\SeatInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SeatController extends Controller
{
//    public $seatInterface;
//
//    public $roomInterface;
//
//    public function __construct
//    (
//        RoomInterface $seatInterface,
//        RoomInterface $roomInterface
//    )
//    {
//        $this->seatInterface = $seatInterface;
//        $this->roomInterface = $roomInterface;
//    }
//    /**
//     * @author son.nk
//     * @OA\Post (
//     *     path="/api/admin/seat/create",
//     *     tags={"Admin Quản lý phòng chiếu"},
//     *     summary="Tạo mới list ghế",
//     *     operationId="admin/seat/create",
//     *     @OA\RequestBody(
//     *          @OA\JsonContent(
//     *              type="object",
//     *              @OA\Property(property="room_id", type="string"),
//     *              @OA\Property(property="seat_type_id", type="string"),
//     *              @OA\Property(property="seat_code", type="string"),
//     *          @OA\Examples(
//     *              summary="Examples",
//     *              example = "Examples",
//     *              value = {
//     *                          {
//     *                              "room_id": "1",
//     *                              "seat_type_id": 1,
//     *                              "seat_code": "A1"
//     *                          },
//     *                          {
//     *                               "room_id": "1",
//     *                               "seat_type_id": 2,
//     *                               "seat_code": "A2"
//     *                          },
//     *                          {
//     *                                "room_id": "1",
//     *                                "seat_type_id": 1,
//     *                                "seat_code": "B1"
//     *                          },
//     *                          {
//     *                                 "room_id": "1",
//     *                                 "seat_type_id": 2,
//     *                                 "seat_code": "B2"
//     *                           },
//     *                           {
//     *                                 "room_id": "1",
//     *                                 "seat_type_id": 3,
//     *                                 "seat_code": "B3"
//     *                           },
//     *                           {
//     *                                  "room_id": "1",
//     *                                  "seat_type_id": 2,
//     *                                  "seat_code": "C1"
//     *                            },
//     *                            {
//     *                                 "room_id": "1",
//     *                                 "seat_type_id": 2,
//     *                                 "seat_code": "C2"
//     *                           },
//     *                           {
//     *                                  "room_id": "1",
//     *                                  "seat_type_id": 2,
//     *                                  "seat_code": "C3"
//     *                            },
//     *                  },
//     *              ),
//     *          )
//     *     ),
//     *     @OA\Response(
//     *         response=200,
//     *         description="Success",
//     *             @OA\JsonContent(
//     *              @OA\Property(property="message", type="string", example="Success."),
//     *          )
//     *     ),
//     * )
//     */
//    public function createSeat(Request $request){
//        try {
//            DB::beginTransaction();
//            $seat = $request->all();
//            foreach ($seat as $data){
//                $this->seatInterface->creatSeat($data);
//            }
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
//
}
