<?php

use App\Http\Controllers\Api\app\CinemaController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/show-time'], function () {
    Route::get('/get-list', [CinemaController::class, 'getListShowTime'])->withoutMiddleware(['role:user', 'auth:sanctum']);
});
