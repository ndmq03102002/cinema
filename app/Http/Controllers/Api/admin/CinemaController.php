<?php

namespace App\Http\Controllers\Api\admin;

use App\Enums\Constant;
use App\Models\Cinema;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CinemaRequest;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CinemaController extends Controller
{
    public function index()
    {
        $cinemas = Cinema::all();
        return view('admin.cinema.index', compact('cinemas'));
    }

    public function create()
    {
        $config['method'] = 'create';
        return view('admin.cinema.create', compact('config'));
    }

    public function edit( $id)
    {
        $cinema = Cinema::find($id);
        $config['method'] = 'edit';
        return view('admin.cinema.create', compact('config','cinema'));
    }

    public function destroy($id)
    {
        Cinema::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa chi nhánh thành công.');
    }


    /**
     * @author son.nk
     * @OA\Post (
     *     path="/api/admin/cinema/create",
     *     tags={"Admin Quản lý rạp chiếu phim"},
     *     summary="Tạo mới",
     *     operationId="admin/cinema/create",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="avatar", type="string"),
     *              @OA\Property(property="latitude", type="string"),
     *              @OA\Property(property="longitude", type="string"),
     *          @OA\Examples(
     *              summary="Examples",
     *              example = "Examples",
     *              value = {
     *                     "name": "CinemaEase Hà Đông",
     *                     "avatar": "string",
     *                     "address": "Số 10 - Trần Phú - Hà Đông - Hà Nội",
     *                     "latitude": "20.9831660",
     *                     "longitude": "105.7909850",
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
    public function createCinema(CinemaRequest $request){
        try {
            DB::beginTransaction();
            $data = $request->all();
            Cinema::create($data);
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

    public function CinemaStore(CinemaRequest $request)
    {
        try {
            DB::beginTransaction();
            // Upload ảnh avatar lên Cloudinary
            $avatarUrl = null;
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarResult = cloudinary()->upload($avatar->getRealPath(), [
                    'folder' => 'cinema',
                    'upload_preset' => 'cinema-upload',
                ]);
                $avatarUrl = $avatarResult->getSecurePath(); // Lấy URL an toàn
            }
            $cinema = Cinema::create([
                'name' => $request->input('name'),
                'avatar' => $avatarUrl,
                'address' => $request->input('address'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);
            DB::commit();
            return redirect()
                ->route('cinema.store')
                ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('cinema.store')
                ->with('error', $th->getMessage());
        }
        
    }


    /**
     * @author son.nk
     * @OA\Post (
     *     path="/api/admin/cinema/update/{id}",
     *     tags={"Admin Quản lý rạp chiếu phim"},
     *     summary="Cập nhật",
     *     operationId="admin/cinema/update",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID cinema",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="avatar", type="string"),
     *              @OA\Property(property="latitude", type="string"),
     *              @OA\Property(property="longitude", type="string"),
     *              @OA\Examples(
     *                  summary="Examples",
     *                  example = "Examples",
     *                  value = {
     *                     "name": "CinemaEase Hà Đông",
     *                     "avatar": "string",
     *                     "address": "Số 10 - Trần Phú - Hà Đông - Hà Nội",
     *                     "latitude": "20.9831660",
     *                     "longitude": "105.7909850",
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
    public function updateCinema($id, CinemaRequest $request){
        try {
            DB::beginTransaction();
            $data = $request->all();
            $query = Cinema::find($id);
            if (!$query) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => trans('messages.errors.cinema.id_found'),
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
    public function CinemaUpdate($id, CinemaRequest $request){
        try {
            DB::beginTransaction();
            $cinema = Cinema::find($id);
            
            // Lưu đường dẫn ảnh cũ
            $oldAvatar = $cinema->avatar;
            if ($oldAvatar) {
                $path = parse_url($oldAvatar, PHP_URL_PATH);
                $parts = explode('/cinema/', $path);
                $avatarPart = 'cinema/' . pathinfo($parts[1], PATHINFO_FILENAME); // 'avatar/khx9uvzvexda7dniu5sa'
            }
           
            // Cập nhật thông tin phim
            $cinema->update($request->only([
                'name',
                'address',
                'latitude',
                'longitude',
            ]));

            // Xử lý upload avatar nếu có
            if ($request->hasFile('avatar')) {
                $avatarResult = cloudinary()->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'cinema',
                    'upload_preset' => 'cinema-upload',
                ]);
                $cinema->avatar = $avatarResult->getSecurePath();

                // Xóa ảnh cũ trên Cloudinary
                if ($oldAvatar) {
                    cloudinary()->destroy($avatarPart); // Xóa ảnh cũ
                }
            } else {
                $cinema->avatar = $oldAvatar;
            }

            // Lưu thông tin phim
            $cinema->save();
            DB::commit();
            return redirect()
            ->route('cinema.index')
            ->with('success', trans('messages.success.success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
            ->route('cinema.index')
            ->with('error', $th->getMessage());
        }
    }
    /**
     * @author son.nk
     * @OA\Get (
     *     path="/api/admin/cinema/get-list",
     *     tags={"Admin Quản lý rạp chiếu phim"},
     *     summary="Lấy danh sách",
     *     operationId="admin/cinema/get-list",
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
     *     path="/api/admin/cinema/get/{id}",
     *     tags={"Admin Quản lý rạp chiếu phim"},
     *     summary="Lấy theo id",
     *     operationId="admin/cinema/get",
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
    public function getCinema($id){
        try {
            $data = Cinema::find($id);
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
