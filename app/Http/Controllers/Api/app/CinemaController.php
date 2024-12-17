<?php

namespace App\Http\Controllers\Api\app;

use App\Enums\Constant;
use App\Models\Cinema;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CinemaRequest;
use App\Repositories\user\MovieShowTime\MovieShowTimeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CinemaController extends Controller{

    public $movieShowTimeRepository;
    public function __construct(
        MovieShowTimeRepository $movieShowTimeRepository
    )
    {
        $this->movieShowTimeRepository = $movieShowTimeRepository;
    }

    /**
     * @author son.nk
     * @OA\Get (
     *     path="/api/app/cinema/get-list",
     *     tags={"App Rạp chiếu phim"},
     *     summary="Lấy danh sách",
     *     operationId="user/cinema/get-list",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *             @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success."),
     *          )
     *     ),
     * )
     */
    public function getListCinema(){
        try {
            $data = Cinema::all();
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

    /**
     * @author son.nk
     * @OA\Get (
     *     path="/api/app/show-time/get-list",
     *     tags={"App Rạp chiếu phim"},
     *     summary="Lấy danh sách",
     *     operationId="app/show-time/get-list",
     *     @OA\Parameter(
     *           name="movie_id",
     *           in="query",
     *           description="ID của phim",
     *           required=false,
     *           @OA\Schema(type="integer")
     *      ),
     *     @OA\Parameter(
     *           name="cinema_id",
     *           in="query",
     *           description="ID của rạp chiếu phim",
     *           required=true,
     *           @OA\Schema(type="integer")
     *      ),
     *     @OA\Parameter(
     *           name="date",
     *           in="query",
     *           description="Ngày chiếu (yyyy/mm/dd)",
     *           required=true,
     *           @OA\Schema(type="string", format="date")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success.")
     *         )
     *     ),
     * )
     */

    public function getListShowTime(Request $request)
    {
        try {
            $data = $request->all();
            $data['movie_id'] = $data['movie_id'] ?? null;
            $response = $this->movieShowTimeRepository->getShowTime($data['cinema_id'], $data['movie_id'], $data['date']);

            if (!$response) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Không tìm thấy lịch chiếu',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => $response
            ], Constant::SUCCESS_CODE);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @author manhquynh
     * @OA\Get (
     *     path="/api/app/cinema/location",
     *     tags={"App Rạp chiếu phim"},
     *     summary="Lấy danh sách",
     *     operationId="app/cinema/location",
     *     @OA\Parameter(
     *           name="latitude",
     *           in="query",
     *           description="latitude user",
     *           required=true,
     *           @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *           name="longitude",
     *           in="query",
     *           description="longitude user",
     *           required=true,
     *           @OA\Schema(type="string")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success.")
     *         )
     *     ),
     * )
     */
    public function getNearbyCinemas(Request $request)
    {
        $userLatitude = $request->input('latitude');
        $userLongitude = $request->input('longitude');

        // Validate input
        if (!$userLatitude || !$userLongitude) {
            return response()->json([
                'status' => 400,
                'message' => 'Latitude and longitude are required',
                'data' => null
            ], 400);
        }

        if (!is_numeric($userLatitude) || !is_numeric($userLongitude) || $userLatitude < -90 || $userLatitude > 90 || $userLongitude < -180 || $userLongitude > 180) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid latitude or longitude',
                'data' => null
            ], 400);
        }

        $cinemas = Cinema::all();
        $distances = [];

        foreach ($cinemas as $cinema) {
            $distance = $this->haversineDistance(
                (float)$userLatitude,
                (float)$userLongitude,
                (float)$cinema->latitude,
                (float)$cinema->longitude
            );
            $distances[$cinema->id] = $distance;
        }

        asort($distances);

        $sortedCinemas = Cinema::whereIn('id', array_keys($distances))
            ->orderByRaw(sprintf("FIELD(id, %s)", implode(',', array_keys($distances))))
            ->get()
            ->map(function ($cinema) use ($distances) {
                $cinema->distance = round($distances[$cinema->id], 2) . ' km'; // Thêm trường distance, làm tròn đến 2 chữ số thập phân
                return $cinema;
            });

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $sortedCinemas
        ], 200);
    }

    /**
     * Tính khoảng cách giữa 2 điểm có tọa độ (latitude, longitude) sử dụng công thức Haversine.
     *
     * @param float $lat1 Vĩ độ điểm 1
     * @param float $lon1 Kinh độ điểm 1
     * @param float $lat2 Vĩ độ điểm 2
     * @param float $lon2 Kinh độ điểm 2
     * @return float Khoảng cách (km)
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Bán kính Trái Đất theo km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }

}
