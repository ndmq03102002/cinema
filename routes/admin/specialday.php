
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\admin\SpecialDayController;

// auth app

Route::prefix('specialdays')->group(function () {
    Route::get('/', [SpecialDayController::class, 'index'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/', [SpecialDayController::class, 'store'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/{id}', [SpecialDayController::class, 'show'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::put('/{id}', [SpecialDayController::class, 'update'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::delete('/{id}', [SpecialDayController::class, 'destroy'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});