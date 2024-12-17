
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\admin\SeatTypeController;
// auth app
Route::group(['prefix' => '/seat-type'], function () {
    Route::post('/create', [SeatTypeController::class, 'createSeatType'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/update/{id}', [SeatTypeController::class, 'updateSeatType'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/get-list', [SeatTypeController::class, 'getListSeatType'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/get/{id}', [SeatTypeController::class, 'getSeatType'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});

