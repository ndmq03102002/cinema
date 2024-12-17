
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\admin\CinemaController;
// auth app
Route::group(['prefix' => '/cinema'], function () {
    Route::post('/create', [CinemaController::class, 'createCinema'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/update/{id}', [CinemaController::class, 'updateCinema'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/get-list', [CinemaController::class, 'getListCinema'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/get/{id}', [CinemaController::class, 'getCinema'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});

