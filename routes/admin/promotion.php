
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\admin\PromotionController;

// auth app

Route::prefix('promotions')->group(function () {
    Route::get('/', [PromotionController::class, 'index'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/', [PromotionController::class, 'store'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/{id}', [PromotionController::class, 'show'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::put('/{id}', [PromotionController::class, 'update'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::delete('/{id}', [PromotionController::class, 'destroy'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});