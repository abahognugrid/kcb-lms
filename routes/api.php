<?php

use App\Http\Controllers\KCB\KCBUssdChannelApiController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/contextCustomerRegistration', [KCBUssdChannelApiController::class, 'contextCustomerRegistration']);
Route::post('/v1/contextGetCustomerDetails', [KCBUssdChannelApiController::class, 'contextGetCustomerDetails']);
Route::post('/v1/contextInitiateLoanApplication', [KCBUssdChannelApiController::class, 'contextInitiateLoanApplication']);
Route::post('/v1/contextInitiateLoanRepayment', [KCBUssdChannelApiController::class, 'contextInitiateLoanRepayment']);
Route::post('/v1/disbursementCallback', [KCBUssdChannelApiController::class, 'handleCallback'])->name('loan.disbursement.callback');
