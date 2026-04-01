<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KCB\KCBUssdChannelApiController;
use App\Http\Controllers\LoanImportController;
use Illuminate\Support\Facades\Route;

Route::prefix('mkcb')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::post('/contextCustomerRegistration', [KCBUssdChannelApiController::class, 'contextCustomerRegistration']);
        Route::post('/contextGetCustomerDetails', [KCBUssdChannelApiController::class, 'contextGetCustomerDetails']);
        Route::post('/contextInitiateLoanApplication', [KCBUssdChannelApiController::class, 'contextInitiateLoanApplication']);
        Route::post('/contextInitiateLoanRepayment', [KCBUssdChannelApiController::class, 'contextInitiateLoanRepayment']);
        Route::post('/disbursementCallback', [KCBUssdChannelApiController::class, 'handleCallback'])->name('loan.disbursement.callback');
    });
});
Route::post('/import-loans', [LoanImportController::class, 'import']);
Route::post('/bulk-commission-recovery', [LoanImportController::class, 'bulkCommissionRecovery']);
Route::post('/delinked-loan-recovery', [LoanImportController::class, 'delinkedLoanRecovery']);
