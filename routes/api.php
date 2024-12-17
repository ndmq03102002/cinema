<?php

use App\Http\Controllers\Api\app\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// API APP
Route::group(['prefix' => 'app', 'middleware' => ['auth:sanctum']], function () {

    \App\Helpers\RouteHelper::includeRouteFiles(__DIR__ . '/app');
});

// API ADMIN
Route::group(['prefix' => 'admin', 'middleware' => ['role:admin', 'auth:sanctum']], function () {

    \App\Helpers\RouteHelper::includeRouteFiles(__DIR__ . '/admin');
});


