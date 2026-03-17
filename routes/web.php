<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AtAGlanceReportController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BorrowersReportController;
use App\Http\Controllers\BusinessRuleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChartOfAccountsController;
use App\Http\Controllers\CreditLimitsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\dashboard\DashboardController;
use App\Http\Controllers\ExclusionParameterController;
use App\Http\Controllers\FeesReportController;
use App\Http\Controllers\FinancialReportsController;
use App\Http\Controllers\FLoatTopUpController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\LoanProductFeesController;
use App\Http\Controllers\LoanProductPenaltiesController;
use App\Http\Controllers\LoanProductTermController;
use App\Http\Controllers\LoanReportsController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\Reports\LoanApplicationReportController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SmsCampaignController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SmsTemplatesController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnforceTwoFactor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('refresh-csrf-token', [LoginController::class, 'refreshCsrfToken'])->name('refresh-csrf-token');

Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('forgot-password.index');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forgot-password.send-email');
Route::get('password/reset', [ResetPasswordController::class, 'showResetForm'])->name('password.showResetForm');
Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::middleware('auth')->group(function () {
    Route::post('/users/verify-2fa', [UserController::class, 'verify2faCode'])->name('users.verify-2fa');
    Route::get('/verify-2fa', [UserController::class, 'show2faLoginScreen'])->name('verify-2fa');
});

Route::middleware(['auth', 'force_password_change', EnforceTwoFactor::class])->group(function () {
    // Dashboard
    Route::get('/finance/export/excel', [DashboardController::class, 'exportFinanceExcel'])->name('finance.export.excel');
    Route::get('/finance/export/pdf', [DashboardController::class, 'exportFinancePdf'])->name('finance.export.pdf');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/finance-dashboard', [DashboardController::class, 'financeDashboard'])->name('dashboard.finance-dashboard');
    Route::get('/notifications/mark-as-read/{notification}', [DashboardController::class, 'markAsRead'])->name('notifications.markAsRead');
    // Users
    Route::middleware('permission:view users')->get('/users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('permission:create users')->get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::middleware('permission:create users')->post('/users', [UserController::class, 'store'])->name('users.store');
    Route::middleware('permission:update users')->get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::middleware('permission:update users')->get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->withTrashed();
    Route::middleware('permission:update users')->put('/users/{user}', [UserController::class, 'update'])->name('users.update')->withTrashed();
    Route::middleware('permission:delete users')->delete('/users/{user}', [UserController::class, 'delete'])->name('users.delete');
    Route::middleware('permission:update users')->put('/users/{user}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::middleware('permission:update users')->put('/users/{user}/enable-2fa', [UserController::class, 'enable2fa'])->name('users.enable-2fa');
    Route::middleware('permission:update users')->put('/users/{user}/confirm-2fa-code', [UserController::class, 'confirm2faCode'])->name('users.confirm-2fa-code');
    Route::middleware('permission:update users')->put('users/deactivate/{user}', [UserController::class, 'deactivate'])->name('user.deactivate')->withTrashed();
    Route::middleware('permission:update users')->put('users/activate/{user}', [UserController::class, 'activate'])->name('user.activate')->withTrashed();
    Route::middleware('permission:update users')->put('users/delete/{user}', [UserController::class, 'delete'])->name('user.delete');
    Route::middleware('permission:update users')->put('users/restore/{user}', [UserController::class, 'restore'])->name('user.restore')->withTrashed();

    Route::middleware('permission:view partners')->get('partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::middleware('permission:create partners')->get('partners/create', [PartnerController::class, 'create'])->name('partner.create');
    Route::middleware('permission:update partners')->get('partners/{partner}', [PartnerController::class, 'edit'])->name('partner.edit');
    Route::middleware('permission:delete partners')->delete('partners/{partner}', [PartnerController::class, 'delete'])->name('partner.destroy');
    Route::middleware('permission:update partners')->get('partners/{partner}/show', [PartnerController::class, 'show'])->name('partner.show');
    Route::middleware('permission:update partners')->put('partners/{partner}/ova', [PartnerController::class, 'saveOvaConfigs'])->name('partner.ova.create');
    Route::middleware('permission:update partners')->put('partners/{partner}/api', [PartnerController::class, 'saveApiConfigs'])->name('partner.api.create');
    Route::middleware('permission:delete partners')->put('partners/{partner}/api/delete', [PartnerController::class, 'deleteApiConfigs'])->name('partner.api.delete');
    Route::middleware('permission:view partner-settings')->get('settings', \App\Http\Controllers\ManagePartnerSettings::class)->name('settings.index');

    // Decision Engine
    Route::middleware('permission:view exclusion-parameters')->get('/exclusion-parameters', [ExclusionParameterController::class, 'index'])->name('exclusion-parameters.index');
    Route::middleware('permission:create exclusion-parameters')->get('/exclusion-parameters/create', [ExclusionParameterController::class, 'create'])->name('exclusion-parameter.create');
    Route::middleware('permission:create exclusion-parameters')->post('/exclusion-parameters', [ExclusionParameterController::class, 'store'])->name('exclusion-parameter.store');
    Route::middleware('permission:view exclusion-parameters')->get('/exclusion-parameters/{exclusionParameter}', [ExclusionParameterController::class, 'edit'])->name('exclusion-parameter.edit');
    Route::middleware('permission:update exclusion-parameters')->put('/exclusion-parameters/{exclusionParameter}', [ExclusionParameterController::class, 'update'])->name('exclusion-parameter.update');
    Route::middleware('permission:delete exclusion-parameters')->delete('/exclusion-parameters/{exclusionParameter}', [ExclusionParameterController::class, 'delete'])->name('exclusion-parameter.destroy');

    // Business Rules
    Route::middleware('permission:view business-rules')->get('business-rules', [BusinessRuleController::class, 'index'])->name('business-rules.index');
    Route::middleware('permission:create business-rules')->get('business-rules/create', [BusinessRuleController::class, 'create'])->name('business-rule.create');
    Route::middleware('permission:create business-rules')->post('business-rules', [BusinessRuleController::class, 'store'])->name('business-rule.store');
    Route::middleware('permission:view business-rules')->get('business-rules/{businessRule}', [BusinessRuleController::class, 'edit'])->name('business-rule.edit');
    Route::middleware('permission:update business-rules')->put('business-rules/{businessRule}', [BusinessRuleController::class, 'update'])->name('business-rule.update');
    Route::middleware('permission:delete business-rules')->delete('business-rules/{businessRule}', [BusinessRuleController::class, 'delete'])->name('business-rule.destroy');

    // Customers
    Route::middleware('permission:view customers')->get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::middleware('permission:create customers')->get('customers/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::middleware('permission:view customers')->get('customers/{customer}/edit', [CustomerController::class, 'bulkUploadUI'])->name('customer.edit');
    Route::middleware('permission:view customers')->get('customers/{customer}', [CustomerController::class, 'show'])->name('customer.show');
    Route::middleware('permission:delete customers')->delete('customers/{customer}', [CustomerController::class, 'delete'])->name('customer.destroy');
    Route::middleware('permission:view customers')->get('/customers/upload/ui', [CustomerController::class, 'bulkUploadUI'])->name('customer.upload.ui');
    Route::middleware('permission:create customers')->post('/customer/upload', [CustomerController::class, 'saveBulkUploadUI'])->name('customer.upload.submit');
    Route::middleware('permission:view customers')->get('/loan-customers/search', [CustomerController::class, 'findLoanCustomers'])->name('loan-customers.search');
    Route::middleware('permission:view customers')->get('/reports/customers/blacklisted', [CustomerController::class, 'blackListedReport'])->name('reports.customers.black-listed-report');
    Route::middleware('permission:create customers')->post('/customers/blacklist', [CustomerController::class, 'blacklistCustomer'])->name('customer.blacklist');
    Route::middleware('permission:create customers')->post('/customers/unblacklist', [CustomerController::class, 'unblacklistCustomer'])->name('customer.unblacklist');

    // Audit Trail
    Route::middleware('permission:view audit-trail')->get('audit-trail', [AuditTrailController::class, 'index'])->name('audit-trail.index');

    // Loan Products
    Route::middleware('permission:view loan-products')->get('loan-products', [LoanProductController::class, 'index'])->name('loan-products.index');
    Route::middleware('permission:create loan-products')->get('loan-products/create', [LoanProductController::class, 'create'])->name('loan-products.create');
    Route::middleware('permission:create loan-products')->post('loan-products', [LoanProductController::class, 'store'])->name('loan-products.store');
    Route::middleware('permission:view loan-products')->get('loan-products/{loanProduct}/edit', [LoanProductController::class, 'edit'])->name('loan-products.edit');
    Route::middleware('permission:update loan-products')->put('loan-products/{loanProduct}', [LoanProductController::class, 'update'])->name('loan-products.update');
    Route::middleware('permission:view loan-products')->get('loan-products/{loanProduct}', [LoanProductController::class, 'show'])->name('loan-products.show');
    Route::middleware('permission:delete loan-products')->delete('loan-products/{loanProduct}', [LoanProductController::class, 'delete'])->name('loan-products.destroy');

    Route::middleware('permission:create loan-products')->get('loan-product-fees/create', [LoanProductFeesController::class, 'create'])->name('loan-product-fee.create');
    Route::middleware('permission:create loan-products')->post('loan-product-fees', [LoanProductFeesController::class, 'store'])->name('loan-product-fee.store');
    Route::middleware('permission:view loan-products')->get('loan-product-fees/{fee}', [LoanProductFeesController::class, 'edit'])->name('loan-product-fee.edit');
    Route::middleware('permission:update loan-products')->put('loan-product-fees/{fee}', [LoanProductFeesController::class, 'update'])->name('loan-product-fee.update');
    Route::middleware('permission:delete loan-products')->delete('loan-product-fees/{fee}', [LoanProductFeesController::class, 'delete'])->name('loan-product-fee.destroy');

    // Transactions
    Route::middleware('permission:view transactions')->get('transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Chart of Accounts

    Route::middleware('permission:view chart-of-accounts')->get('/chart-of-accounts', [ChartOfAccountsController::class, 'index'])->name('chart-of-accounts.index');
    Route::middleware('permission:view chart-of-accounts')->get('/chart-of-accounts/{account}', [ChartOfAccountsController::class, 'show'])->name('chart-of-accounts.show');
    Route::middleware('permission:view chart-of-accounts')->put('/chart-of-accounts/{accountId}', [ChartOfAccountsController::class, 'update'])->name('chart-of-accounts.update');
    Route::middleware('permission:create chart-of-accounts')->post('/chart-of-accounts', [ChartOfAccountsController::class, 'store'])->name('chart-of-accounts.store');

    // Loan Product Penalties
    Route::middleware('permission:view loan-products')->get('loan-product-penalty', [LoanProductPenaltiesController::class, 'index'])->name('loan-product-penalty.index');
    Route::middleware('permission:create loan-products')->get('loan-product-penalty/create', [LoanProductPenaltiesController::class, 'create'])->name('loan-product-penalty.create');
    Route::middleware('permission:create loan-products')->post('loan-product-penalty', [LoanProductPenaltiesController::class, 'store'])->name('loan-product-penalty.store');
    Route::middleware('permission:view loan-products')->get('loan-product-penalty/{penalty}', [LoanProductPenaltiesController::class, 'edit'])->name('loan-product-penalty.edit');
    Route::middleware('permission:update loan-products')->put('loan-product-penalty/{penalty}', [LoanProductPenaltiesController::class, 'update'])->name('loan-product-penalty.update');
    Route::middleware('permission:delete loan-products')->delete('loan-product-penalty/{penalty}', [LoanProductPenaltiesController::class, 'delete'])->name('loan-product-penalty.destroy');

    // Loan Accounts
    Route::middleware('permission:view loan-accounts')->get('/loan-accounts', [LoanController::class, 'index'])->name('loan-accounts.index');
    Route::middleware('permission:view loan-accounts')->put('/loan-accounts/{loan}/write-off', [LoanController::class, 'writeOff'])->name('loan-accounts.writeOff');
    Route::middleware('permission:view loan-accounts')->get('/loan-accounts/{loan}', [LoanController::class, 'show'])->name('loan-accounts.show');
    Route::middleware('permission:view loan-accounts')->get('/loan-accounts/{loan}/ledger', [LoanController::class, 'ledger'])->name('loan-accounts.ledger');
    Route::middleware('permission:view loan-accounts')->get('/loan-accounts/{loan}/payment-velocity', [LoanController::class, 'paymentVelocity'])->name('loan-accounts.paymentVelocity');
    Route::middleware('permission:update loan-accounts')->put('/loan-accounts/{loan}', [LoanController::class, 'update'])->name('loan-accounts.update');

    // Loan Applications
    Route::middleware('permission:view loan-applications')->get('loan-applications', [LoanApplicationController::class, 'index'])->name('loan-applications.index');
    Route::middleware('permission:view loan-applications')->get('loan-application/{application}', [LoanApplicationController::class, 'show'])->name('loan-applications.show');
    Route::middleware('permission:view loan-applications')->post('loan-approval/{loanApplication}', [LoanController::class, 'store'])->name('loan-application.approve');
    Route::middleware('permission:view loan-applications')->get('/loan-application/{loan}/download', [LoanApplicationController::class, 'download'])->name('loan-applications.download');
    Route::middleware('permission:view loan-applications')->get('loan-disbursement/{loan}', [LoanController::class, 'disburse'])->name('loan-application.disburse');
    //	Route::middleware('permission:view loan-applications')->get('loan-repayment/{loan}', [LoanController::class, 'makePayment'])->name('loan-application.pay');
    Route::middleware('permission:view loan-applications')->post('loan-applications/{customer}', [LoanApplicationController::class, 'generateLoanSummary'])->name('loan-applications.summary');
    Route::middleware('permission:create loan-applications')->get('cancel-loan-applications', [LoanApplicationController::class, 'cancel'])->name('loan-application.cancel');

    // Loan Repayment
    Route::middleware('permission:create loan-repayments')->get('loan-repayments/create', [\App\Http\Controllers\Loans\RepaymentController::class, 'create'])->name('loan-repayments.create');

    // Float Management
    Route::middleware('permission:view float-management')->get('/float-management', [FLoatTopUpController::class, 'index'])->name('float-management.index');
    Route::middleware('permission:create float-management')->post('/float-management', [FLoatTopUpController::class, 'store'])->name('float-management.store');
    Route::middleware('permission:update float-management')->put('/float-management/approve/{topup}', [FLoatTopUpController::class, 'approve'])->name('float-management.approve');

    // Loan Product Terms
    Route::middleware('permission:create loan-product-terms')->post('/loan-product-terms', [LoanProductTermController::class, 'store'])->name('loan-product-term.store');
    Route::middleware('permission:update loan-product-terms')->put('/loan-product-terms/{loanProductTerm}', [LoanProductTermController::class, 'update'])->name('loan-product-term.update');
    Route::middleware('permission:delete loan-product-terms')->delete('/loan-product-terms/{loanProductTerm}', [LoanProductTermController::class, 'destroy'])->name('loan-product-term.destroy');

    // SMS Logs
    Route::middleware('permission:view sms-logs')->get('sms-logs', [SmsController::class, 'logs'])->name('sms.logs');
    Route::middleware('permission:view sms-notifications')->get('sms-notifications', [SmsController::class, 'notifications'])->name('sms.notifications');
    Route::middleware('permission:view sms-float-topups')->get('sms-float-topups', [SmsController::class, 'topups'])->name('sms.topups');
    Route::middleware('permission:view sms-float-topups')->get('sms-float-topups/create', [SmsController::class, 'topupCreate'])->name('sms.topup-create');
    Route::middleware('permission:create sms-float-topups')->post('sms-float-topups/store', [SmsController::class, 'topupStore'])->name('sms.topup-store');
    Route::middleware('permission:view sms-float-topups')->get('download/{file}', [SmsController::class, 'download'])->name('download');
    Route::middleware('permission:update sms-float-topups')->post('sms-float-topups/approve/{id}', [SmsController::class, 'approveTopup'])->name('sms.approve-topup');
    Route::middleware('permission:update sms-float-topups')->post('sms-float-topups/reject/{id}', [SmsController::class, 'rejectTopup'])->name('sms.reject-topup');

    // Financial Reports
    Route::middleware('permission:view trial-balance-report')->get('/reports/financial/trial-balance', [FinancialReportsController::class, 'trialBalance'])->name('reports.financial.trial-balance');
    Route::middleware('permission:view balance-sheet-report')->get('/reports/financial/balance-sheet', [FinancialReportsController::class, 'balanceSheet'])->name('reports.financial.balance-sheet');
    Route::middleware('permission:view income-statement-report')->get('/reports/financial/income-statement', [FinancialReportsController::class, 'incomeStatement'])->name('reports.financial.income-statement');
    Route::middleware('permission:view general-ledger-summary')->get('/reports/financial/general-ledger-summary', [FinancialReportsController::class, 'generalLedgerSummary'])->name('reports.financial.general-ledger-summary');
    Route::middleware('permission:view cash-flow-statement')->get('/reports/financial/cash-flow-statement', [FinancialReportsController::class, 'cashFlowStatement'])->name('reports.financial.cash-flow-statement');
    Route::middleware('permission:view income-report')->get('/reports/financial/income-report', [FinancialReportsController::class, 'incomeReport'])->name('reports.financial.income-report');
    Route::middleware('permission:view general-ledger-summary')->get('/reports/financial/general-ledger-statement-break-down', [FinancialReportsController::class, 'generalLedgerBreakDown'])->name('reports.financial.gl-statement-break-down');
    Route::middleware('permission:view sms-logs')->get('/reports/sms-report', [\App\Http\Controllers\SmsReportController::class, 'index'])->name('reports.sms.index');

    // 	->middleware(['auth']);
    Route::middleware('permission:view sms-templates')->group(function () {
        Route::get('/sms-templates', [SmsTemplatesController::class, 'index'])->name('sms-templates.index');
        Route::get('/sms-templates/create', [SmsTemplatesController::class, 'create'])->name('sms-template.create');
        Route::get('/sms-templates/{template}', [SmsTemplatesController::class, 'edit'])->name('sms-template.edit');
        Route::middleware('permission:create sms-templates')->post('/sms-templates/store', [SmsTemplatesController::class, 'store'])->name('sms-template.store');
        Route::middleware('permission:update sms-templates')->put('/sms-templates/{template}', [SmsTemplatesController::class, 'update'])->name('sms-template.update');
        Route::middleware('permission:delete sms-templates')->delete('/sms-templates/{template}', [SmsTemplatesController::class, 'delete'])->name('sms-template.delete');
    });
    // Loan Reports
    Route::middleware('permission:view loan-report')->group(function () {
        Route::get('/reports/loans/disbursement', [LoanReportsController::class, 'disbursement'])->name('reports.loans.disbursement');
        Route::get('/reports/loans/outstanding', [LoanReportsController::class, 'outstanding'])->name('reports.loans.outstanding');
        Route::get('/reports/loans/paidoff', [LoanReportsController::class, 'paidOff'])->name('reports.loans.paidoff');
        Route::get('/reports/loans/overdue', [LoanReportsController::class, 'overdue'])->name('reports.loans.overdue');
        Route::get('/reports/loans/portfolio-at-risk', [LoanReportsController::class, 'portfolio_at_risk'])->name('reports.loans.portfolio_at_risk');
        Route::get('/reports/loans/arrears-ageing-report', [LoanReportsController::class, 'arrearsAgingReport'])->name('reports.loans.arrears_ageing_report');
        Route::get('/reports/loans/pending/disbursement', [LoanReportsController::class, 'pendingDisbursements'])->name('reports.loans.pending.disbursement');
        Route::get('/reports/loan-applications', [LoanApplicationReportController::class, 'index'])->name('reports.loanApplications.index');
        Route::get('/reports/loans/rejected/applications', [LoanReportsController::class, 'rejectedApplications'])->name('reports.loans.rejected.applications');
        Route::get('/reports/loans/repayment', [LoanReportsController::class, 'repaymentReport'])->name('reports.loans.repayment-report');
        Route::get('/reports/loans/loss-provisions', [LoanReportsController::class, 'provisionsReport'])->name('reports.loans.provisions-report');
        Route::get('/reports/loans/written-off', [LoanReportsController::class, 'writtenOffReport'])->name('reports.loans.written-off-report');
        Route::get('/reports/loans/written-off-recovered', [LoanReportsController::class, 'writtenOffRecoveredReport'])->name('reports.loans.written-off-recovered-report');
        Route::get('/due-loans-report', [LoanReportsController::class, 'dueLoansReport'])->name('due-loans-report');
    });

    // SMS Minimum Balance
    Route::middleware('permission:update partners')->put('sms/set-minimum-balance', [SmsController::class, 'setMinimumBalance'])->name('sms.set-minimum-balance');

    // SMS Campaigns
    Route::resource('sms-campaigns', SmsCampaignController::class);
    Route::middleware('permission:view sms-campaigns')->get('/sms-campaigns/customers/{targetGroup}/{partnerId}', [SmsCampaignController::class, 'getCustomers'])->name('sms-campaigns.customers');
    Route::middleware('permission:update partners')->get('sms/create-minimum-balance', [SmsController::class, 'createMinimumBalance'])->name('sms.create-minimum-balance');

    // Roles and Permissions
    Route::middleware('permission:view roles')->group(function () {
        Route::get('roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::get('permissions', [RolePermissionController::class, 'permissions'])->name('roles.permissions');
        Route::get('roles-permissions/create-role', [RolePermissionController::class, 'createRole'])->name('roles-permissions.create-role');
        Route::middleware('permission:create roles')->post('roles-permissions/store-role', [RolePermissionController::class, 'storeRole'])->name('roles-permissions.store-role');
        Route::get('roles-permissions/edit-role/{role}', [RolePermissionController::class, 'editRole'])->name('roles-permissions.edit-role');
        Route::middleware('permission:update roles')->put('roles-permissions/update-role/{role}', [RolePermissionController::class, 'updateRole'])->name('roles-permissions.update-role');
        Route::middleware('permission:delete roles')->delete('roles-permissions/delete-role/{role}', [RolePermissionController::class, 'deleteRole'])->name('roles-permissions.delete-role');
    });

    // Other Reports
    Route::middleware('permission:view fees-report')->get('/reports/others/fees-report', [FeesReportController::class, 'index'])->name('reports.others.fees-report');
    Route::middleware('permission:view borrowers-report')->get('/reports/others/borrowers-report', [BorrowersReportController::class, 'index'])->name('reports.borrowers-report');
    Route::middleware('permission:view daily-report')->get('/reports/others/daily-report', [AtAGlanceReportController::class, 'dailyReport'])->name('reports.others.daily-report');
    Route::middleware('permission:view at-a-glance-report')->get('/reports/others/performance-metrics', [AtAGlanceReportController::class, 'atAGlanceReport'])->name('reports.others.at-a-glance-report');
    Route::middleware('permission:view payment-history-velocity-report')->get('/reports/others/payment-history-velocity', [\App\Http\Controllers\PaymentHistoryVelocityReportController::class, 'index'])->name('reports.others.payment-history-velocity');
    Route::middleware('permission:view daily-report')->get('/reports/others/account-ledger', [LoanReportsController::class, 'accountLedger'])->name('reports.others.account-ledger');
    Route::middleware('permission:view daily-report')->get('/reports/others/external-accounts', [LoanReportsController::class, 'externalAccountsReport'])->name('reports.others.external-accounts');

    Route::middleware('permission:view credit-limits')->get('/credit-limits', [CreditLimitsController::class, 'index'])->name('credit-limits.index');
    Route::middleware('permission:create loan-products')->post('/loan/close', [LoanController::class, 'closeLoan'])->name('loan.close');

    // Regular ticket routes
    Route::get('/ticket-reports', [TicketReportController::class, 'index'])->name('tickets.reports');
    Route::get('/tickets/reports/export/pdf', [TicketReportController::class, 'exportPdf'])->name('tickets.reports.export.pdf');
    Route::get('/tickets/reports/export/excel', [TicketReportController::class, 'exportExcel'])->name('tickets.reports.export.excel');
    Route::resource('tickets', TicketController::class)->except(['edit', 'update', 'destroy']);
    Route::middleware('permission:view ticket-dashboard')->get('ticket-dashboard', [TicketController::class, 'dashboard'])
        ->name('tickets.dashboard');
    Route::post('tickets/{ticket}/comment', [TicketController::class, 'addComment'])
        ->name('tickets.comment');
    Route::resource('agents', AgentController::class)->middleware('permission:delete agents');

    // Admin ticket actions
    Route::middleware('permission:update tickets')->put('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
        ->name('tickets.status');
    Route::middleware('permission:update tickets')->post('tickets/{ticket}/assign', [TicketController::class, 'assign'])
        ->name('tickets.assign');
    Route::resource('categories', CategoryController::class)->except(['show', 'create']);
    Route::resource('labels', LabelController::class)->except(['show', 'create']);
    Route::get('/downloads', [\App\Http\Controllers\DownloadsController::class, 'index'])->name('downloads.index');
    Route::get('/downloads/{notification}/download', [\App\Http\Controllers\DownloadsController::class, 'download'])->name('downloads.download');

    Route::middleware('permission:view logs')->get('/logs', [LogController::class, 'index'])->name('logs.index');
});
