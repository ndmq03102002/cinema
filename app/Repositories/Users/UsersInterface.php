<?php

namespace App\Repositories\Users;

use App\Enums\Constant;
use App\Repositories\RepositoryInterface;

interface UsersInterface
{
    public function getUser($userid);

    public function register($request);

    public function editProfile($data, $userId);
}