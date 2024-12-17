
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\app\AuthController;

// auth app
Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [AuthController::class, 'loginAdmin'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/register', [AuthController::class, 'registerAdmin'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/get-admin', [AuthController::class, 'getUser']);
    Route::post('/edit-profile', [AuthController::class, 'editProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/re_register', [AuthController::class, 'reRegister'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/email/verify_OTP', [AuthController::class, 'verifyOtp'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});

