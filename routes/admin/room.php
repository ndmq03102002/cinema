
<?php

use App\Http\Controllers\Api\admin\RoomController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/room'], function () {
    Route::post('/create', [RoomController::class, 'createRoom'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/get/{id}', [RoomController::class, 'getRoom'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});

