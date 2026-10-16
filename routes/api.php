<?php

use App\Http\Controllers\Api\BillImportController;
use App\Http\Controllers\Api\ReferenceDataController;
use App\Http\Middleware\AuthenticateFinanceApi;
use Illuminate\Support\Facades\Route;

Route::middleware([AuthenticateFinanceApi::class, 'throttle:30,1'])->group(function (): void {
    Route::get('/categories', [ReferenceDataController::class, 'categories']);
    Route::get('/payment-methods', [ReferenceDataController::class, 'paymentMethods']);

    Route::post('/bills/import/preview', [BillImportController::class, 'preview']);
    Route::post('/bills/import', [BillImportController::class, 'store']);
});
