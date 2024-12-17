
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\admin\FoodController;

// auth app

Route::prefix('foods')->group(function () {
    Route::get('/', [FoodController::class, 'index'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/', [FoodController::class, 'store'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/{id}', [FoodController::class, 'show'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::put('/{id}', [FoodController::class, 'update'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::delete('/{id}', [FoodController::class, 'destroy'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});