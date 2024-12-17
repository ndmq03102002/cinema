<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Enums\Constant;
use App\Http\Requests\admin\PromotionRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="Promotion",
 *     type="object",
 *     required={"id", "promo_name", "quantity", "start_date", "end_date", "status", "discount"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="promo_name", type="string", example="Black Friday Sale"),
 *     @OA\Property(property="description", type="string", example="Black Friday Sale 2024"),
 *     @OA\Property(property="quantity", type="integer", example=100),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-12-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-12-15"),
 *     @OA\Property(property="status", type="integer", example=1),
 *     @OA\Property(property="discount", type="number", format="integer", example=10000),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-09T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-09T12:00:00Z")
 * )
 */
class PromotionController extends Controller
{
    public function promotionIndex(){
        $promotions = Promotion::all();
        return view('admin.promotion.index',compact('promotions'));
    }
    public function promotionCreate(){
        $config['method'] = 'create';
        return view('admin.promotion.create',compact('config'));
    }
    public function promotionStore(PromotionRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            // Upload ảnh avatar lên Cloudinary
            $avatarUrl = null;
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarResult = cloudinary()->upload($avatar->getRealPath(), [
                    'folder' => 'promotion',
                    'upload_preset' => 'promotion-upload',
                ]);
                $avatarUrl = $avatarResult->getSecurePath(); // Lấy URL an toàn
            }
            $promotion = Promotion::create([
                'promo_name' => $request->input('promo_name'),
                'avatar' => $avatarUrl,
                'description' => $request->input('description'),
                'discount' => $request->input('discount'),
                'quantity' => $request->input('quantity'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'status' => $request->input('status'),
            ]);
            DB::commit();
            return redirect()
                ->route('promotion.create')
                ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('promotion.create')
                ->with('error', $th->getMessage());
        }
        
    }

    public function promotionEdit(string $id){
        $promotion = Promotion::find($id);
        $config['method'] = 'edit';
        return view('admin.promotion.create', compact('config','promotion'));
    }
  
    public function promotionUpdate($id, PromotionRequest $request){
        
        try {
            DB::beginTransaction();
            $promotion = Promotion::find($id);
            // Lưu đường dẫn ảnh cũ
            $oldAvatar = $promotion->avatar;
            if ($oldAvatar) {
                $path = parse_url($oldAvatar, PHP_URL_PATH);
                $parts = explode('/promotion/', $path);
                $avatarPart = 'promotion/' . pathinfo($parts[1], PATHINFO_FILENAME); // 'avatar/khx9uvzvexda7dniu5sa'
            }
           
            // Cập nhật thông tin phim
            $promotion->update($request->only([
                'promo_name',
                'description',
                'discount',
                'quantity',
                'start_date',
                'end_date',
                'status',
            ]));

            // Xử lý upload avatar nếu có
            if ($request->hasFile('avatar')) {
                $avatarResult = cloudinary()->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'promotion',
                    'upload_preset' => 'promotion-upload',
                ]);
                $promotion->avatar = $avatarResult->getSecurePath();

                // Xóa ảnh cũ trên Cloudinary
                if ($oldAvatar) {
                    cloudinary()->destroy($avatarPart); // Xóa ảnh cũ
                }
            } else {
                $promotion->avatar = $oldAvatar;
            }

            // Lưu thông tin phim
            $promotion->save();
            DB::commit();
            return redirect()
            ->route('promotion.index')
            ->with('success', trans('messages.success.success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
            ->route('promotion.index')
            ->with('error', $th->getMessage());
        }
    }
    public function updateAjax(Request $request, $id)
    {
        if ($request->ajax()) {
            $promotion = Promotion::find($id);
    
            // Cập nhật trạng thái
            if ($request->has('status')) {
                $promotion->status = $request->status;
            } 
            $promotion->save();
    
            return response()->json(['success' => 'Thông tin đã được cập nhật']);
        }
    }
    public function promotionDestroy(string $id){
        Promotion::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa promotion thành công.');
    }

    /**
     * @author quynhndmq
     * @OA\Get(
     *     path="/api/admin/promotions",
     *     tags={"Admin Promotions"},
     *     summary="Get all promotions",
     *     operationId="getPromotions",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Promotion")
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
    public function index()
    {
        try {
            $promotions = Promotion::all();
            if ($promotions->isEmpty()) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Không có khuyến mãi nào.',
                    'data' => []
                ], 200);
            }
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => $promotions
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
 * @OA\Post(
 *     path="/api/admin/promotions",
 *     tags={"Admin Promotions"},
 *     summary="Create a new promotion",
 *     operationId="createPromotion",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="promo_name", type="string"),
 *             @OA\Property(property="avatar", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="quantity", type="integer"),
 *             @OA\Property(property="start_date", type="string", format="date"),
 *             @OA\Property(property="end_date", type="string", format="date"),
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="discount", type="integer"),
 *             example={
 *                 "promo_name": "Holiday Discount",
 *                 "avatar": ".jpg",
 *                 "description": "Up to 50% off for the holiday season",
 *                 "quantity": 500,
 *                 "start_date": "2024-12-01",
 *                 "end_date": "2024-12-31",
 *                 "status": 1,
 *                 "discount": 20
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Promotion created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Promotion")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The promo_name field is required.")
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
    public function store(PromotionRequest $request)
    {
        try {
            DB::beginTransaction();

            $promotion = Promotion::create($request->all());

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Promotion created successfully',
                'data' => $promotion
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
/**
 * @OA\Get(
 *     path="/api/admin/promotions/{id}",
 *     tags={"Admin Promotions"},
 *     summary="Get a promotion by ID",
 *     operationId="getPromotionById",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the promotion",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(ref="#/components/schemas/Promotion")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Promotion not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Promotion not found.")
 *         )
 *     )
 * )
 */
    public function show($id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Promotion not found',
                'data' => []
            ], 200);
        }
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Promotion retrieved successfully',
            'data' => $promotion
        ],200);
    }
/**
 * @OA\Put(
 *     path="/api/admin/promotions/{id}",
 *     tags={"Admin Promotions"},
 *     summary="Update a promotion",
 *     operationId="updatePromotion",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the promotion",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="promo_name", type="string"),
 *             @OA\Property(property="avatar", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="quantity", type="integer"),
 *             @OA\Property(property="start_date", type="string", format="date"),
 *             @OA\Property(property="end_date", type="string", format="date"),
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="discount", type="integer")
 *         ),
 *         @OA\Examples(
 *             example="UpdatePromotionExample",
 *             summary="Sample promotion update data",
 *             value={
 *                 "promo_name": "Holiday Discount",
 *                 "avatar": ".jpg,..",
 *                 "description": "Up to 50% off for the holiday season",
 *                 "quantity": 500,
 *                 "start_date": "2024-12-01",
 *                 "end_date": "2024-12-31",
 *                 "status": 1,
 *                 "discount": 20
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Promotion updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Promotion")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Promotion not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Promotion not found.")
 *         )
 *     )
 * )
 */

    public function update(PromotionRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $promotion = Promotion::findOrFail($id);
            if (!$promotion) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Promotion not found',
                    'data' => []
                ], 200);
            }
            $promotion->update($request->all());

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Promotion updated successfully',
                'data' => $promotion
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
/**
 * @OA\Delete(
 *     path="/api/admin/promotions/{id}",
 *     tags={"Admin Promotions"},
 *     summary="Delete a promotion",
 *     operationId="deletePromotion",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the promotion",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Promotion deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Promotion deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Promotion not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Promotion not found.")
 *         )
 *     )
 * )
 */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $promotion = Promotion::findOrFail($id);
            if (!$promotion) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Promotion not found',
                    'data' => []
                ], 200);
            }
            $promotion->delete();

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Promotion deleted successfully',
                'data' => []
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/app/promotions/getList",
     *     tags={"App Promotions"},
     *     summary="Retrieve active promotions with user's usage status",
     *     operationId="getPromotionList",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="promo_name", type="string", example="Black Friday Sale"),
     *                 @OA\Property(property="description", type="string", example="Biggest sale of the year!"),
     *                 @OA\Property(property="discount", type="number", format="float", example=15.5),
     *                 @OA\Property(property="quantity", type="integer", example=100),
     *                 @OA\Property(property="start_date", type="string", format="date-time", example="2024-12-01T00:00:00Z"),
     *                 @OA\Property(property="end_date", type="string", format="date-time", example="2024-12-15T23:59:59Z"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="used_status", type="string", enum={"used", "not used"}, example="not used"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-09T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-09T12:00:00Z")
     *             )
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
    public function getList()
    {
        try {
            $userId = Auth::id(); // Lấy ID của người dùng hiện tại

            // Lấy danh sách khuyến mãi có status = 1
            $promotions = Promotion::where('status', 1)->get();

            // Thêm trường "isUsed" để xác định người dùng đã sử dụng mã hay chưa
            $promotions->transform(function ($promotion) use ($userId) {
                $promotion->isUsed = $promotion->users()->where('account_id', $userId)->exists() 
                    ? true 
                    : false;
                return $promotion;
            });

            if ($promotions->isEmpty()) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'No promotions available.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Promotion list retrieved successfully.',
                'data' => $promotions
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    // public function applyPromotion($id)
    // {
    //     $promotion = Promotion::find($id);

    //     if (!$promotion || $promotion->status != 1) {
    //         return response()->json([
    //             'status' => Constant::FALSE_CODE,
    //             'message' => 'Khuyến mãi không tồn tại hoặc không hợp lệ.',
    //             'data' => []
    //         ], Response::HTTP_BAD_REQUEST);
    //     }

    //     // Kiểm tra ngày áp dụng khuyến mãi
    //     if ($promotion->end_date < now() || $promotion->start_date > now()) {
    //         return response()->json([
    //             'status' => Constant::FALSE_CODE,
    //             'message' => 'Khuyến mãi không có hiệu lực hoặc đã hết hạn.',
    //             'data' => []
    //         ], Response::HTTP_BAD_REQUEST);
    //     }

    //     // Kiểm tra số lượng khuyến mãi còn lại
    //     if ($promotion->quantity <= 0) {
    //         return response()->json([
    //             'status' => Constant::FALSE_CODE,
    //             'message' => 'Khuyến mãi đã hết số lượng sử dụng.',
    //             'data' => []
    //         ], Response::HTTP_BAD_REQUEST);
    //     }

    //     $userId = Auth::id();

    //     // Kiểm tra người dùng đã sử dụng mã khuyến mãi này chưa
    //     if ($promotion->users()->where('account_id', $userId)->exists()) {
    //         return response()->json([
    //             'status' => Constant::FALSE_CODE,
    //             'message' => 'Bạn đã sử dụng chương trình khuyến mãi này.',
    //             'data' => []
    //         ], Response::HTTP_BAD_REQUEST);
    //     }

    //     // Ghi nhận người dùng đã sử dụng mã và giảm số lượng còn lại
    //     $promotion->users()->attach($userId);
    //     $promotion->decrement('quantity');

    //     return response()->json([
    //         'status' => Constant::SUCCESS_CODE,
    //         'message' => 'Khuyến mãi đã được áp dụng thành công.',
    //         'data' => []
    //     ]);
    // }

}
