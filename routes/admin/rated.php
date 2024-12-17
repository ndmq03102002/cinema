
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\admin\RatedController;

// auth app

Route::prefix('rateds')->group(function () {
    Route::get('/', [RatedController::class, 'index'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/', [RatedController::class, 'store'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/{id}', [RatedController::class, 'show'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::put('/{id}', [RatedController::class, 'update'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::delete('/{id}', [RatedController::class, 'destroy'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});