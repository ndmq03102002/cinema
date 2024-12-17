
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\admin\WeeklyTicketPriceController;

// auth app

Route::prefix('weekly-ticket-prices')->group(function () {
    Route::get('/', [WeeklyTicketPriceController::class, 'index'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::post('/', [WeeklyTicketPriceController::class, 'store'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::get('/{id}', [WeeklyTicketPriceController::class, 'show'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::put('/{id}', [WeeklyTicketPriceController::class, 'update'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
    Route::delete('/{id}', [WeeklyTicketPriceController::class, 'destroy'])->withoutMiddleware(['role:admin', 'auth:sanctum']);
});
