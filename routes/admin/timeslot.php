
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\admin\TimeSlotController;

// auth app

Route::prefix('timeslots')->group(function () {
    Route::get('/', [TimeSlotController::class, 'index'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/', [TimeSlotController::class, 'store'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/{id}', [TimeSlotController::class, 'show'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::put('/{id}', [TimeSlotController::class, 'update'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::delete('/{id}', [TimeSlotController::class, 'destroy'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});