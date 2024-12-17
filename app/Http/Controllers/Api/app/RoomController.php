<?php

namespace App\Http\Controllers\Api\app;

use App\Enums\Constant;
use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\MovieShowtime;
use App\Repositories\user\Room\RoomRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
    public $roomRepository;

    public function __construct
    (
        RoomRepository $roomRepository
    )
    {
        $this->roomRepository = $roomRepository;
    }
    /**
     * @OA\Get (
     *     path="/api/app/room/get/{id}",
     *     tags={"App Phòng chiếu"},
     *     summary="Lấy thông tin xuất chiếu",
     *     operationId="app/room/get",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="id xuất chiếu",
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
    public function getShowTime($id){
        try {
            $movieshowtime = MovieShowtime::find($id);
            if (!$movieshowtime) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                     'message' => 'Không tìm thấy dữ liệu',
                    'data' => []
                ],200);
            }
            $data = $this->roomRepository->getRoom($id);
            
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
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
