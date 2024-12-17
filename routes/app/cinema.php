
<?php

use App\Http\Controllers\Api\app\CinemaController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/cinema'], function () {
    Route::get('/get-list', [CinemaController::class, 'getListCinema'])->withoutMiddleware(['role:user', 'auth:sanctum']);
    Route::get('/location', [CinemaController::class, 'getNearbyCinemas'])->withoutMiddleware(['role:user', 'auth:sanctum']);
});
