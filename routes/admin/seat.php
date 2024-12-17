
<?php

use App\Http\Controllers\Api\admin\SeatController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/seat'], function () {
    Route::post('/create', [SeatController::class, 'createSeat'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});

