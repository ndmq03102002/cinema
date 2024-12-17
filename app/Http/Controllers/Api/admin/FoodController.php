<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Enums\Constant;
use App\Http\Requests\Admin\FoodRequest;

/**
 * @OA\Schema(
 *     schema="Food",
 *     type="object",
 *     required={"id", "name", "price", "description"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Popcorn"),
 *     @OA\Property(property="price", type="integer", example=45000),
 *     @OA\Property(property="description", type="string", example="Delicious buttery popcorn"),
 *     @OA\Property(property="image", type="string", example="popcorn.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-09T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-09T12:00:00Z")
 * )
 */
class FoodController extends Controller
{
    public function foodIndex(){
        $foods = Food::all();
        return view('admin.food.index',compact('foods'));
    }
    public function foodCreate(){
        $config['method'] = 'create';
        return view('admin.food.create',compact('config'));
    }
    public function foodStore(FoodRequest $request)
    {
        try {
            DB::beginTransaction();
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                // Upload hình ảnh lên Cloudinary
                $imageResult = cloudinary()->upload($image->getRealPath(), [
                    'folder' => 'food', // Đặt thư mục là 'food'
                    'upload_preset' => 'food-upload', // Đặt upload preset của bạn
                ]);
                $imageUrl = $imageResult->getSecurePath(); // Lấy URL an toàn của hình ảnh
            }

            // Tạo mới món ăn và lưu thông tin vào cơ sở dữ liệu
            Food::create([
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'description' => $request->input('description'),
                'image' => $imageUrl, // Lưu URL hình ảnh vào trường 'image'
                'status' => $request->input('status')
            ]);
            DB::commit();
            return redirect()
                ->route('food.create')
                ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('food.create')
                ->with('error', $th->getMessage());
        }
        
    }

    public function foodEdit(string $id){
        $food = Food::find($id);
        $config['method'] = 'edit';
        return view('admin.food.create', compact('config','food'));
    }
  
    public function foodUpdate($id, FoodRequest $request){
        try {
            DB::beginTransaction();
            $food = Food::findOrFail($id);

            // Lưu đường dẫn ảnh cũ
            $oldImage = $food->image;
            if ($oldImage) {
                $path = parse_url($oldImage, PHP_URL_PATH);
                $parts = explode('/food/', $path);
                $imagePart = 'food/' . pathinfo($parts[1], PATHINFO_FILENAME); // Lấy phần đường dẫn của ảnh
            }

            // Cập nhật thông tin món ăn
            $food->update($request->only([
                'name',
                'price',
                'description',
                'status'
            ]));

            // Xử lý upload ảnh nếu có
            if ($request->hasFile('image')) {
                // Upload ảnh mới lên Cloudinary
                $imageResult = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'food', // Đặt thư mục là 'food'
                    'upload_preset' => 'food-upload', // Đặt upload preset của bạn
                ]);
                $food->image = $imageResult->getSecurePath(); // Lấy URL an toàn của ảnh

                // Xóa ảnh cũ trên Cloudinary
                if ($oldImage) {
                    cloudinary()->destroy($imagePart); // Xóa ảnh cũ
                }
            } else {
                $food->image = $oldImage; // Giữ lại ảnh cũ nếu không có ảnh mới
            }

            // Lưu thông tin món ăn
            $food->save();
            DB::commit();
            return redirect()
            ->route('food.index')
            ->with('success', trans('messages.success.success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
            ->route('food.index')
            ->with('error', $th->getMessage());
        }
    }
    public function updateAjax(Request $request, $id)
    {
        if ($request->ajax()) {
            $food = Food::find($id);
    
            // Cập nhật trạng thái
            if ($request->has('status')) {
                $food->status = $request->status;
            } 
            $food->save();
    
            return response()->json(['success' => 'Thông tin đã được cập nhật']);
        }
    }
    public function foodDestroy(string $id){
        Food::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa food thành công.');
    }
    /**
     * @OA\Get(
     *     path="/api/admin/foods",
     *     tags={"Admin Foods"},
     *     summary="Get all foods",
     *     operationId="getFoods",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Food")
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
            $foods = Food::all();
            if (isset($foods) && $foods->count() === 0) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Foods not found',
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Foods retrieved successfully',
                'data' => $foods
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
     *     path="/api/admin/foods",
     *     tags={"Admin Foods"},
     *     summary="Create a new food",
     *     operationId="createFood",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "price"},
     *             @OA\Property(property="name", type="string", example="Popcorn"),
     *             @OA\Property(property="price", type="integer", example=45000),
     *             @OA\Property(property="description", type="string", example="Delicious buttery popcorn"),
     *             @OA\Property(property="image", type="string", example="popcorn.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Food created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Food")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name field is required.")
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
    public function store(FoodRequest $request)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem có tệp hình ảnh nào được tải lên không
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                // Upload hình ảnh lên Cloudinary
                $imageResult = cloudinary()->upload($image->getRealPath(), [
                    'folder' => 'food', // Đặt thư mục là 'food'
                    'upload_preset' => 'food-upload', // Đặt upload preset của bạn
                ]);
                $imageUrl = $imageResult->getSecurePath(); // Lấy URL an toàn của hình ảnh
            }

            // Tạo mới món ăn và lưu thông tin vào cơ sở dữ liệu
            $food = Food::create([
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'description' => $request->input('description'),
                'image' => $imageUrl, // Lưu URL hình ảnh vào trường 'image'
                'status' => $request->input('status')
            ]);

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Food created successfully',
                'data' => $food
            ], Constant::SUCCESS_CODE);

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
     *     path="/api/admin/foods/{id}",
     *     tags={"Admin Foods"},
     *     summary="Get a food by ID",
     *     operationId="getFoodById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of food to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Food retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Food")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Food not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Food not found.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $food = Food::findOrFail($id);
        if (!$food) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Food not found',
                'data' => []
            ], Constant::SUCCESS_CODE);
        }
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Food retrieved successfully',
            'data' => $food
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/foods/{id}",
     *     tags={"Admin Foods"},
     *     summary="Update a food",
     *     operationId="updateFood",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of food to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Updated Popcorn"),
     *             @OA\Property(property="price", type="integer", example=50000),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="image", type="string", example="updated-popcorn.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Food updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Food")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name field is required.")
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
    public function update(FoodRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $food = Food::findOrFail($id);
            if (!$food) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Food not found',
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }

            // Lưu đường dẫn ảnh cũ
            $oldImage = $food->image;
            if ($oldImage) {
                $path = parse_url($oldImage, PHP_URL_PATH);
                $parts = explode('/food/', $path);
                $imagePart = 'food/' . pathinfo($parts[1], PATHINFO_FILENAME); // Lấy phần đường dẫn của ảnh
            }

            // Cập nhật thông tin món ăn
            $food->update($request->only([
                'name',
                'price',
                'description',
                'status'
            ]));

            // Xử lý upload ảnh nếu có
            if ($request->hasFile('image')) {
                // Upload ảnh mới lên Cloudinary
                $imageResult = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'food', // Đặt thư mục là 'food'
                    'upload_preset' => 'food-upload', // Đặt upload preset của bạn
                ]);
                $food->image = $imageResult->getSecurePath(); // Lấy URL an toàn của ảnh

                // Xóa ảnh cũ trên Cloudinary
                if ($oldImage) {
                    cloudinary()->destroy($imagePart); // Xóa ảnh cũ
                }
            } else {
                $food->image = $oldImage; // Giữ lại ảnh cũ nếu không có ảnh mới
            }

            // Lưu thông tin món ăn
            $food->save();

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Food updated successfully',
                'data' => $food
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
     *     path="/api/admin/foods/{id}",
     *     tags={"Admin Foods"},
     *     summary="Delete a food",
     *     operationId="deleteFood",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of food to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Food deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Food deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Food not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Food not found.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $food = Food::findOrFail($id);
            if (!$food) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Food not found',
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
            $food->delete();
            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Food deleted successfully',
                'data' => []
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
}
