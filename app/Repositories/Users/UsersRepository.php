<?php

namespace App\Repositories\Users;

use App\Models\Account;
use App\Models\Role;
use App\Repositories\RepositoryAbstract;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class UsersRepository implements UsersInterface{
    public function register($request){
        $data = [
            'phone_number' => $request['phone_number'],
            'email' => $request['email'],
            'confirm' => Account::ACTIVE,
            'password' => Hash::make($request['password']),
            'role_id' => Role::USER,
            'confirmation_code' => rand(100000, 999999),
            'confirmation_code_expired_in' => Carbon::now()->addSecond(3600),
        ];
        $users = Users::create(array_merge($data));
        $userInfoId = UsersInfo::create([
            'full_name' => $request['full_name'],
            'phone_number' => $request['phone_number'],
            'address' => $request['address'],
            'users_id' => $users->id
        ])->id;
        return $users;
    }
    public function verifyOtp(){
        $data = [];
        return $data;
    }
    public function getUser($userId){
        $userInfo = Users::find($userId);
        $avatarFileName = $userInfo->usersInfo->avatar;
        $avatar = UsersInfo::url . UsersInfo::avatarBase;
        if($avatarFileName){
            $avatar = UsersInfo::url . $avatarFileName;
        }
        $data = [
            'email' => $userInfo->email,
            'full_name' => $userInfo->usersInfo->full_name,
            'phone_number' => $userInfo->usersInfo->phone_number,
            'address' => $userInfo->usersInfo->address,
            'avatar' => $avatar
        ];
        return $data;
    }
    public function editProfile($userId, $data){
        if(isset($data['avatar'])){
            $avatar = $data['avatar'];
            $oldAvatar = UsersInfo::where('users_id', '=', $userId)->first();
            if($oldAvatar['avatar'] != null){
                if(file_exists('storage/' . $oldAvatar['avatar'])){
                    unlink('storage/' . $oldAvatar['avatar']);
                }
            }
            $filename = time() . '_' . Str::random(10) . '.' . $avatar->getClientOriginalExtension();
            $filePath = $avatar->move('storage/' . UsersInfo::imagePath , $filename);
            $userInfo = UsersInfo::where('users_id', '=', $userId)->update([
                'full_name' => $data['full_name'],
                'phone_number' => $data['phone_number'],
                'address' => $data['address'],
                'avatar' => UsersInfo::imagePath. '/' . $filename
            ]);
        }
        else{
            $userInfo = UsersInfo::where('users_id', '=', $userId)->update([
                'full_name' => $data['full_name'],
                'phone_number' => $data['phone_number'],
                'address' => $data['address']
            ]);
        }
        return $userInfo;
    }
}
