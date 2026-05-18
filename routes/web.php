<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountsPayableController;
use App\Http\Controllers\AccountsReceivableController;
use App\Http\Controllers\AdjustingEntryController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialPeriodController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard (all roles)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Sales & Service — Finance, Cashier, Manager
    |----------------------------------------------------------------------
    */
    Route::middleware('role:finance,cashier,manager')->group(function () {
        // Customers
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        // Service Orders
        Route::resource('service-orders', ServiceOrderController::class)->except(['edit', 'update', 'destroy']);
        Route::post('service-orders/{serviceOrder}/in-progress', [ServiceOrderController::class, 'markInProgress'])->name('service-orders.in-progress');
        Route::post('service-orders/{serviceOrder}/done',        [ServiceOrderController::class, 'markDone'])->name('service-orders.done');
        Route::post('service-orders/{serviceOrder}/complete',    [ServiceOrderController::class, 'markCompleted'])->name('service-orders.complete');

        // Sales
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);

        // Accounts Receivable
        Route::get('accounts-receivable', [AccountsReceivableController::class, 'index'])->name('accounts-receivable.index');
        Route::get('accounts-receivable/{invoice}', [AccountsReceivableController::class, 'show'])->name('accounts-receivable.show');
        Route::post('accounts-receivable/{invoice}/payments', [AccountsReceivableController::class, 'storePayment'])->name('accounts-receivable.payments.store');
    });

    /*
    |----------------------------------------------------------------------
    | Inventory — Inventory Staff, Manager, Finance
    |----------------------------------------------------------------------
    */
    Route::middleware('role:inventory,manager,finance')->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('inventory-movements', [InventoryMovementController::class, 'index'])->name('inventory-movements.index');
    });

    /*
    |----------------------------------------------------------------------
    | Purchasing — Finance, Inventory, Manager
    |----------------------------------------------------------------------
    */
    Route::middleware('role:finance,inventory,manager')->group(function () {
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
        Route::post('purchases/{purchase}/approve', [PurchaseController::class, 'approve'])
            ->middleware('role:manager')
            ->name('purchases.approve');
        Route::delete('purchases/{purchase}',       [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Finance & Accounting — Finance, Manager
    |----------------------------------------------------------------------
    */
    Route::middleware('role:finance,manager')->group(function () {
        // Ledger
        Route::get('ledger/coa',     [LedgerController::class, 'coa'])->name('ledger.coa');
        Route::get('ledger/coa/create', [LedgerController::class, 'createAccount'])->name('ledger.coa.create');
        Route::post('ledger/coa', [LedgerController::class, 'storeAccount'])->name('ledger.coa.store');
        Route::patch('ledger/coa/{account}/opening-balance', [LedgerController::class, 'updateOpeningBalance'])->name('ledger.coa.opening-balance');
        Route::get('ledger/general', [LedgerController::class, 'general'])->name('ledger.general');
        Route::get('ledger/journal', [LedgerController::class, 'journal'])->name('ledger.journal');
        Route::post('ledger/journal/{journalEntry}/reverse', [LedgerController::class, 'reverseJournal'])->name('ledger.journal.reverse');
        Route::resource('adjusting-entries', AdjustingEntryController::class)->only(['index', 'create', 'store']);

        // Expenses
        Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store']);

        // Financial Reports
        Route::get('reports/income-statement', [ReportController::class, 'incomeStatement'])->name('reports.income-statement');
        Route::get('reports/cash-flow',        [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
        Route::get('reports/transactions',     [ReportController::class, 'transactions'])->name('reports.transactions');
        Route::get('reports/trial-balance',    [ReportController::class, 'trialBalance'])->name('reports.trial-balance');
        Route::get('reports/balance-sheet',    [ReportController::class, 'balanceSheet'])->name('reports.balance-sheet');

        // Accounts Payable
        Route::get('accounts-payable', [AccountsPayableController::class, 'index'])->name('accounts-payable.index');
        Route::get('accounts-payable/{invoice}', [AccountsPayableController::class, 'show'])->name('accounts-payable.show');
        Route::post('accounts-payable/{invoice}/payments', [AccountsPayableController::class, 'storePayment'])->name('accounts-payable.payments.store');

        // Financial Periods
        Route::get('financial-periods',             [FinancialPeriodController::class, 'index'])->name('financial-periods.index');
        Route::post('financial-periods',            [FinancialPeriodController::class, 'store'])->name('financial-periods.store');
        Route::post('financial-periods/{period}/close', [FinancialPeriodController::class, 'close'])->name('financial-periods.close');
        Route::post('financial-periods/{period}/reopen', [FinancialPeriodController::class, 'reopen'])->name('financial-periods.reopen');
    });

    /*
    |----------------------------------------------------------------------
    | HR & Payroll — HR, Manager
    |----------------------------------------------------------------------
    */
    Route::middleware('role:hr,manager')->group(function () {
        Route::resource('employees', EmployeeController::class)->except(['show']);
        Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('payroll',           [PayrollController::class, 'index'])->name('payroll.index');
        Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
    });

    /*
    |----------------------------------------------------------------------
    | Admin Only — User Management, Audit Logs
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});
