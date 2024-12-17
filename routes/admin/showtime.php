
<?php

use App\Http\Controllers\Api\admin\MovieShowTimeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/show-time'], function () {
    Route::post('/create', [MovieShowTimeController::class, 'createShowTime'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});

