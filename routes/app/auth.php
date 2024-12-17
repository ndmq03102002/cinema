
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\app\AuthController;
use App\Http\Controllers\Api\app\ChangePasswordController;
use App\Http\Controllers\Api\app\ProfileController;
use App\Http\Controllers\Api\app\TicketController;
// auth app
Route::group(['prefix' => '/auth'], function () {
    Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware(['role:user', 'auth:sanctum']);
    Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware(['role:user', 'auth:sanctum']);
});

Route::post('/change-password', [ChangePasswordController::class,'changePassword'])->middleware('auth:sanctum');
Route::post('/forgot-password', [AuthController::class, 'checkForgotPassword'])->withoutMiddleware(['role:user', 'auth:sanctum']);

Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'getProfile']);
Route::middleware('auth:sanctum')->post('/profile', [ProfileController::class, 'updateOrCreate']);

Route::middleware('auth:sanctum')->get('/getTokenUser', [AuthController::class, 'getTokenUser']);
