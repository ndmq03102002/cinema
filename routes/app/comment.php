<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\app\CommentController;

Route::group(['prefix' => '/comment'], function () {
    Route::post('/create', [CommentController::class, 'createComment']);
    Route::get('/get/{id}', [CommentController::class, 'getComment'])->withoutMiddleware(['role:user', 'auth:sanctum']);
    Route::delete('/delete/{id}', [CommentController::class, 'deleteComment']);
});
