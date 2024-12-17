<?php

namespace App\Http\Controllers\Api\app;

use App\Http\Controllers\Controller;
use App\Http\Requests\app\ProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use app\Enums\Constant;
use Symfony\Component\HttpFoundation\Response;
class ProfileController extends Controller
{
    /**
     * Get the user's profile.
     *
     * @OA\Get(
     *     path="/api/app/profile",
     *     tags={"App Tài khoản"},
     *     summary="Get the user's profile",
     *     operationId="getProfile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="age", type="integer"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="avatar", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getProfile()
    {
        // Lấy profile của người dùng đã đăng nhập
        $profile = Profile::with('account')->where('account_id', Auth::user()->id)->first();
        
        if (!$profile) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Profile not found',
                'data' => [],
            ], Constant::SUCCESS_CODE);
        }
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Profile retrieved successfully',
            'data' => $profile,
        ]);
    }

    /**
     * Update or create the user's profile.
     *
     * @OA\Post(
     *     path="/api/app/profile",
     *     tags={"App Tài khoản"},
     *     summary="Create or update user's profile",
     *     operationId="updateProfile",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="age", type="integer"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="avatar", type="string", format="uri")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function updateOrCreate(ProfileRequest $request)
    {
        try {
            DB::beginTransaction();

            // Lấy thông tin profile của người dùng
            $profile = Profile::where('account_id', Auth::user()->id)->first();
            
            if (!$profile) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Profile not found',
                    'data' => [],
                ], Constant::SUCCESS_CODE);
            }

            // Lưu đường dẫn ảnh cũ nếu có
            $oldAvatar = $profile ? $profile->avatar : null;
            if($oldAvatar){
                $path = parse_url($oldAvatar, PHP_URL_PATH);
                $parts = explode('/avatar/', $path);
                $avatarPart = 'avatar/' . pathinfo($parts[1], PATHINFO_FILENAME); // 'avatar/khx9uvzvexda7dniu5sa'
            }
            // Cập nhật hoặc tạo mới profile
            $profile = Profile::updateOrCreate(
                ['account_id' => Auth::user()->id],
                $request->only(['name', 'age', 'phone_number'])
            );

            // Xử lý upload avatar nếu có
            if ($request->hasFile('avatar')) {
                $avatarResult =cloudinary()->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'avatar',
                    'upload_preset' => 'avatar-upload',
                ]);
                $profile->avatar = $avatarResult->getSecurePath();

                // Xóa ảnh cũ trên Cloudinary nếu có
                if ($oldAvatar) {
                    cloudinary()->destroy($avatarPart);
                }
            }

            // Lưu thông tin profile
            $profile->save();

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Profile updated successfully',
                'data' => $profile,
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
