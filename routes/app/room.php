
<?php

use App\Http\Controllers\Api\app\RoomController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/room'], function () {
    Route::get('/get/{id}', [RoomController::class, 'getShowTime'])->withoutMiddleware(['role:user', 'auth:sanctum']);
});
