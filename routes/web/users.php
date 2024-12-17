
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\web\AuthController;

// auth app
Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [AuthController::class, 'loginUser'])->withoutMiddleware(['role:user', 'auth:sanctum']);
    Route::post('/register', [AuthController::class, 'registerUser'])->withoutMiddleware(['role:user', 'auth:sanctum']);
    Route::get('/get-user', [AuthController::class, 'getUser']);
    Route::post('/edit-profile', [AuthController::class, 'editProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/re_register', [AuthController::class, 'reRegister'])->withoutMiddleware(['role:user', 'auth:sanctum']);
    Route::post('/email/verify_OTP', [AuthController::class, 'verifyOtp'])->withoutMiddleware(['role:user', 'auth:sanctum']);
});

