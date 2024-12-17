
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\app\TicketController;

Route::group(['prefix' => '/ticket'], function () {
    Route::post('/reservation', [TicketController::class, 'createReservation']);
    Route::post('/momo-payment', [TicketController::class, 'momoPayment']);
    Route::get('/handle-momo-payment', [TicketController::class, 'handleMomoPayment']);
    Route::get('/get/{type}', [TicketController::class, 'getTicket']);
    Route::get('/detail/{id}', [TicketController::class, 'detailTicket']);

});

Route::middleware('auth:sanctum')->get('/get-ticket', [TicketController::class, 'getBillDetail']);
Route::middleware('auth:sanctum')->post('/check-bill', [TicketController::class, 'checkBill']);
