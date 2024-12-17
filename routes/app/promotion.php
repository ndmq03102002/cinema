
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\admin\PromotionController;

// auth app


Route::post('applypromotion/{id}', [PromotionController::class, 'applyPromotion'])->withoutMiddleware(['role:user', 'auth:sanctum']);
Route::get('promotions/getList', [PromotionController::class, 'getList']);