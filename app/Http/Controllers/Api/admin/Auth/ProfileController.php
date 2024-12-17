<?php

namespace App\Http\Controllers\Api\admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\app\ProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    // Bạn có thể bỏ phần swagger đi nếu không cần thiết trong trường hợp trả về view
    /**
     * Get the user's profile.
     *
     */
    public function getProfile()
    {
        // Lấy profile của người dùng đã đăng nhập
        $profile = Profile::where('account_id', Auth::user()->id)->first();

        // Giả sử bạn có một view để hiển thị profile
        return view('admin.auth.profile', compact('profile'));

    }

    /**
     * Update or create the user's profile.
     *
     */
    public function updateOrCreate(ProfileRequest $request)
    {
        try {
            DB::beginTransaction();

            // Lấy thông tin profile của người dùng
            $profile = Profile::where('account_id', Auth::user()->id)->first();

            // Lưu đường dẫn ảnh cũ nếu có
            $oldAvatar = $profile ? $profile->avatar : null;
            if ($oldAvatar) {
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
                $avatarResult = cloudinary()->upload($request->file('avatar')->getRealPath(), [
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

            // Redirect về trang trước (hoặc trang profile) với thông báo thành công
            return redirect()->back()->with('success', 'Profile updated successfully');
            // Hoặc bạn có thể redirect đến một route cụ thể
            // return redirect()->route('profile.show')->with('success', 'Profile updated successfully');

        } catch (\Throwable $th) {
            DB::rollBack();

            // Redirect về trang trước với thông báo lỗi
            return redirect()->back()->with('error', 'Failed to update profile: ' . $th->getMessage());
            // Hoặc redirect đến một route cụ thể
            // return redirect()->route('profile.show')->with('error', 'Failed to update profile: ' . $th->getMessage());
        }
    }
}