<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Customers
Route::resource('customers', CustomerController::class)->except(['show']);

// Suppliers
Route::resource('suppliers', SupplierController::class)->except(['show']);

// Products
Route::resource('products', ProductController::class)->except(['show']);

// Service Orders
Route::resource('service-orders', ServiceOrderController::class)->except(['edit', 'update', 'destroy']);
Route::post('service-orders/{serviceOrder}/in-progress', [ServiceOrderController::class, 'markInProgress'])->name('service-orders.in-progress');
Route::post('service-orders/{serviceOrder}/done',        [ServiceOrderController::class, 'markDone'])->name('service-orders.done');
Route::post('service-orders/{serviceOrder}/complete',    [ServiceOrderController::class, 'markCompleted'])->name('service-orders.complete');

// Sales
Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);

// Purchases
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
Route::delete('purchases/{purchase}',       [PurchaseController::class, 'destroy'])->name('purchases.destroy');

// Employees — full CRUD including destroy
Route::resource('employees', EmployeeController::class)->except(['show']);

// Payroll
Route::get('payroll',           [PayrollController::class, 'index'])->name('payroll.index');
Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');

// Expenses
Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store']);

// Ledger
Route::get('ledger/coa',     [LedgerController::class, 'coa'])->name('ledger.coa');
Route::get('ledger/general', [LedgerController::class, 'general'])->name('ledger.general');
Route::get('ledger/journal', [LedgerController::class, 'journal'])->name('ledger.journal');

// Reports
Route::get('reports/income-statement', [ReportController::class, 'incomeStatement'])->name('reports.income-statement');
Route::get('reports/cash-flow',        [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
Route::get('reports/transactions',     [ReportController::class, 'transactions'])->name('reports.transactions');
