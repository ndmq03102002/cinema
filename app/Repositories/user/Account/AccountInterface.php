<?php

namespace App\Repositories\user\Account;

interface AccountInterface
{

    public function register($request);
    public function login($request);
    public function logout();
    public function changePassword();
    public function getProfile();
    public function updateProfile();

}
