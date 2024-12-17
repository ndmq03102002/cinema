
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\admin\MovieController;

// auth app

Route::prefix('movies')->group(function () {
    Route::get('/', [MovieController::class, 'index'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/', [MovieController::class, 'store'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/{id}', [MovieController::class, 'show'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::put('/{id}', [MovieController::class, 'update'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::delete('/{id}', [MovieController::class, 'destroy'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});