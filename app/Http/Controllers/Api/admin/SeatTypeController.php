<?php

namespace App\Http\Controllers\Api\admin;

use App\Enums\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CinemaRequest;
use App\Models\Cinema;
use App\Models\SeatType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\admin\SeatTypeRequest;

class SeatTypeController extends Controller
{

    public function seattypeIndex(){
        $seattypes = SeatType::all();
        return view('admin.seattype.index',compact('seattypes'));
    }
    public function seattypeCreate(){
        $config['method'] = 'create';
        return view('admin.seattype.create',compact('config'));
    }
    public function seattypeStore(SeatTypeRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            SeatType::create($data);
            DB::commit();
            return redirect()
                ->route('seattype.create')
                ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('seattype.create')
                ->with('error', $th->getMessage());
        }
        
    }

    public function seattypeEdit(string $id){
        $seattype = SeatType::find($id);
        $config['method'] = 'edit';
        return view('admin.seattype.create', compact('config','seattype'));
    }
  
    public function seattypeUpdate($id, SeatTypeRequest $request){
        try {
            DB::beginTransaction();
            $data = $request->all();
            $query = SeatType::find($id);
            $query->update($data);
            DB::commit();
            return redirect()
            ->route('seattype.index')
            ->with('success', trans('messages.success.success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
            ->route('seattype.index')
            ->with('error', $th->getMessage());
        }
    }
    public function seattypeDestroy(string $id){
        SeatType::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa seattype thành công.');
    }
    /**
     * @author son.nk
     * @OA\Post (
     *     path="/api/admin/seat-type/create",
     *     tags={"Admin Quản lý loại ghế"},
     *     summary="Tạo mới",
     *     operationId="admin/seat-type/create",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="description", type="string"),
     *          @OA\Examples(
     *              summary="Examples",
     *              example = "Examples",
     *              value = {
     *                  "name": "Ghế vip",
     *                  "description": "Ghế vip",
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
    public function createSeatType(SeatTypeRequest $request){
        try {
            DB::beginTransaction();
            $data = $request->all();
            SeatType::create($data);
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

    /**
     * @author son.nk
     * @OA\Post (
     *     path="/api/admin/seat-type/update/{id}",
     *     tags={"Admin Quản lý loại ghế"},
     *     summary="Cập nhật",
     *     operationId="admin/seat-type/update",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID seat type",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Examples(
     *                  summary="Examples",
     *                  example = "Examples",
     *                  value = {
     *                     "name": "Ghế bảo trì",
     *                     "address": "Ghế bảo trì",
     *                  },
     *              ),
     *           ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *             @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success."),
     *          )
     *     ),
     * )
     */
    public function updateSeatType($id, SeatTypeRequest $request){
        try {
            DB::beginTransaction();
            $data = $request->all();
            $query = SeatType::find($id);
            if (!$query) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => trans('messages.errors.seat_type.id_found'),
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
            $query->update($data);
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
    /**
     * @author son.nk
     * @OA\Get (
     *     path="/api/admin/seat-type/get-list",
     *     tags={"Admin Quản lý loại ghế"},
     *     summary="Lấy danh sách",
     *     operationId="admin/seat-type/get-list",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *             @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success."),
     *          )
     *     ),
     * )
     */
    public function getListSeatType(){
        try {
            $data = SeatType::all();
            if($data->isEmpty()){
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Không có dữ liệu',
                ],200);

            }
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
     *     path="/api/admin/seat-type/get/{id}",
     *     tags={"Admin Quản lý loại ghế"},
     *     summary="Lấy theo id",
     *     operationId="admin/seat-type/get",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID cinema",
     *          required=true,
     *               @OA\Schema(type="integer")
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
    public function getSeatType($id){
        try {
            $data = SeatType::find($id);
            if (!$data) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => trans('messages.errors.cinema.id_found'),
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
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
