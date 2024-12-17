
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\admin\MovieController;

// auth app


Route::group(['prefix' => '/movies'], function () {
    Route::get('/get-list', [MovieController::class, 'getMovies'])->withoutMiddleware(['role:user', 'auth:sanctum']);
    Route::get('/show/{id}', [MovieController::class, 'getMovieDetail'])->withoutMiddleware(['role:user', 'auth:sanctum']);
});
