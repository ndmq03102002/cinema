<?php

namespace App\Repositories\user\Account;

use App\Models\Account;
use App\Models\Profile;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AccountRepository implements AccountInterface{

    public function register($request){
        $dataAccount = [
            'email' => $request['email'],
            'username' => $request['username'],
            'status' => Account::ACTIVE,
            'password' => Hash::make($request['password']),
            'role_id' => Role::USER,
        ];
        $account = Account::create(array_merge($dataAccount))->id;
        $dataProfile = [
            'account_id' => $account,
            'name' => $request['name'],
            'phone_number' => $request['phone_number'],
        ];
        $profile = Profile::create(array_merge($dataProfile));
        return $account;
    }

    public function login($request)
    {
        $account = Account::where('username', $request->username)
        ->whereIn('role_id', [Account::USER, Account::STAFF])
        ->first();
        return $account;
    }
    public function logout()
    {
        // TODO: Implement logout() method.
    }
    public function changePassword()
    {
        // TODO: Implement changePassword() method.
    }
    public function getProfile()
    {
        // TODO: Implement getProfile() method.
    }
    public function updateProfile()
    {
        // TODO: Implement updateProfile() method.
    }
}
