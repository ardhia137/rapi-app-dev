<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\FinanceRecordController;
use App\Http\Controllers\BudgetTargetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MonthlyTargetController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RiwayatPenjualanController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Dashboard Routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Scan Product by Barcode (Dashboard)
    Route::get('/scan/product', [DashboardController::class, 'scanProduct'])->name('scan.product');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users CRUD protected by permissions
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.update')->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.update')->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');

    // Roles management protected by permissions
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->middleware('permission:roles.create')->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('permission:roles.view')->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:roles.update')->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.update')->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('roles.destroy');

    // Permissions management protected by permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:permissions.view')->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->middleware('permission:permissions.create')->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:permissions.create')->name('permissions.store');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->middleware('permission:permissions.view')->name('permissions.show');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->middleware('permission:permissions.update')->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->middleware('permission:permissions.update')->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permissions.delete')->name('permissions.destroy');

    // Customer Routes
    Route::get('/customers', [CustomerController::class, 'index'])->middleware('permission:customers.view')->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->middleware('permission:customers.create')->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->middleware('permission:customers.create')->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->middleware('permission:customers.update')->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->middleware('permission:customers.update')->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:customers.delete')->name('customers.destroy');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->middleware('permission:customers.view')->name('customers.show');


    // Product Routes
    Route::get('/products', [ProductController::class, 'index'])->middleware('permission:products.view')->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->middleware('permission:products.create')->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->middleware('permission:products.create')->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('permission:products.update')->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('permission:products.update')->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:products.delete')->name('products.destroy');
    Route::get('/products/{id}/barcode/download', [ProductController::class, 'downloadBarcode'])->middleware('permission:products.view')->name('products.barcode.download');
    Route::get('/products/{id}', [ProductController::class, 'show'])->middleware('permission:products.view')->name('products.show');



    // Product Batch Routes
    Route::get('/product-batches', [ProductBatchController::class, 'index'])->middleware('permission:product-batches.view')->name('product-batches.index');
    Route::get('/product-batches/create', [ProductBatchController::class, 'create'])->middleware('permission:product-batches.create')->name('product-batches.create');
    Route::post('/product-batches', [ProductBatchController::class, 'store'])->middleware('permission:product-batches.create')->name('product-batches.store');
    Route::get('/product-batches/{productBatch}/edit', [ProductBatchController::class, 'edit'])->middleware('permission:product-batches.update')->name('product-batches.edit');
    Route::put('/product-batches/{productBatch}', [ProductBatchController::class, 'update'])->middleware('permission:product-batches.update')->name('product-batches.update');
    Route::delete('/product-batches/{productBatch}', [ProductBatchController::class, 'destroy'])->middleware('permission:product-batches.delete')->name('product-batches.destroy');
    Route::get('/product-batches/report', [ProductBatchController::class, 'report'])->middleware('permission:product-batches.report')->name('product-batches.report');
 

    // Penjualan Routes
    Route::get('/invoices', [InvoiceController::class, 'index'])->middleware('permission:invoices.view')->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->middleware('permission:invoices.create')->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->middleware('permission:invoices.create')->name('invoices.store');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->middleware('permission:invoices.update')->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->middleware('permission:invoices.update')->name('invoices.update');
    // Setor Penjualan (place BEFORE catch-all invoice routes)
    Route::get('/invoices/setor', [InvoiceController::class, 'indexSetor'])->middleware('permission:invoices.setor')->name('invoices.setor');
    Route::get('/invoices/{invoice}/setor', [InvoiceController::class, 'editSetor'])->middleware('permission:invoices.setor')->name('invoices.setor.edit');
    Route::post('/invoices/{invoice}/setor', [InvoiceController::class, 'updateSetor'])->middleware('permission:invoices.setor-update')->name('invoices.setor.update');

    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->middleware('permission:invoices.delete')->name('invoices.destroy');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->middleware('permission:invoices.view')->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->middleware('permission:invoices.report')->name('invoices.pdf');

    Route::get('/surat-jalan', [SuratJalanController::class, 'index'])->middleware('permission:surat-jalan.view')->name('surat-jalan.index');
    Route::get('/surat-jalan/create', [SuratJalanController::class, 'create'])->middleware('permission:surat-jalan.create')->name('surat-jalan.create');
    Route::post('/surat-jalan', [SuratJalanController::class, 'store'])->middleware('permission:surat-jalan.create')->name('surat-jalan.store');
    Route::get('/surat-jalan/{suratJalan}', [SuratJalanController::class, 'show'])->middleware('permission:surat-jalan.view')->name('surat-jalan.show');
    Route::get('/surat-jalan/{suratJalan}/edit', [SuratJalanController::class, 'edit'])->middleware('permission:surat-jalan.update')->name('surat-jalan.edit');
    Route::put('/surat-jalan/{suratJalan}', [SuratJalanController::class, 'update'])->middleware('permission:surat-jalan.update')->name('surat-jalan.update');
    Route::get('/surat-jalan/{suratJalan}/pdf', [SuratJalanController::class, 'pdf'])->middleware('permission:surat-jalan.report')->name('surat-jalan.pdf');
    Route::delete('/surat-jalan/{suratJalan}', [SuratJalanController::class, 'destroy'])->middleware('permission:surat-jalan.delete')->name('surat-jalan.destroy');

    Route::get('/transactions', [TransactionController::class, 'index'])->middleware('permission:transactions.view')->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->middleware('permission:transactions.create')->name('transactions.store');
    // Budget Targets CRUD protected by permissions
    Route::get('/budget-target', [BudgetTargetController::class, 'index'])->middleware('permission:budget-target.view')->name('budget-target.index');
    Route::get('/budget-target/create', [BudgetTargetController::class, 'create'])->middleware('permission:budget-target.create')->name('budget-target.create');
    Route::post('/budget-target', [BudgetTargetController::class, 'store'])->middleware('permission:budget-target.create')->name('budget-target.store');
    Route::get('/budget-target/{budgetTarget}/edit', [BudgetTargetController::class, 'edit'])->middleware('permission:budget-target.update')->name('budget-target.edit');
    Route::put('/budget-target/{budgetTarget}', [BudgetTargetController::class, 'update'])->middleware('permission:budget-target.update')->name('budget-target.update');
    Route::delete('/budget-target/{budgetTarget}', [BudgetTargetController::class, 'destroy'])->middleware('permission:budget-target.delete')->name('budget-target.destroy');

    // Finance Records CRUD (Input Keuangan) protected by permissions
    // IMPORTANT: Specific routes (preview-pdf, download-pdf, create, history) MUST be BEFORE parameter routes

    // Preview & Download PDF - HARUS DI ATAS
    Route::get('/finance-records/preview-pdf', [FinanceRecordController::class, 'previewPdf'])
        ->name('finance-records.preview-pdf');

    Route::get('/finance-records/download-pdf', [FinanceRecordController::class, 'downloadPdf'])
        ->name('finance-records.download-pdf');

    // Finance History (Riwayat Keuangan - Read Only) - HARUS DI ATAS
    Route::get('/finance-history', [FinanceRecordController::class, 'history'])
        ->middleware('permission:finance.history')
        ->name('finance-records.history');

    // Create - HARUS DI ATAS
    Route::get('/finance-records/create', [FinanceRecordController::class, 'create'])
        ->middleware('permission:finance.input.create')
        ->name('finance-records.create');

    // Index & Store
    Route::get('/finance-records', [FinanceRecordController::class, 'index'])
        ->middleware('permission:finance.input.view')
        ->name('finance-records.index');

    Route::post('/finance-records', [FinanceRecordController::class, 'store'])
        ->middleware('permission:finance.input.create')
        ->name('finance-records.store');

    // Routes with {financeRecord} parameter - HARUS DI BAWAH
    Route::get('/finance-records/{financeRecord}/edit', [FinanceRecordController::class, 'edit'])
        ->middleware('permission:finance.input.update')
        ->name('finance-records.edit');

    Route::put('/finance-records/{financeRecord}', [FinanceRecordController::class, 'update'])
        ->middleware('permission:finance.input.update')
        ->name('finance-records.update');

    Route::delete('/finance-records/{financeRecord}', [FinanceRecordController::class, 'destroy'])
        ->middleware('permission:finance.input.delete')
        ->name('finance-records.destroy');

    // Riwayat Penjualan (Invoices + Surat Jalan)
    Route::get('/riwayat-penjualan', [RiwayatPenjualanController::class, 'index'])->name('riwayat-penjualan.index');

    // Monthly Targets
    Route::get('/monthly-targets', [MonthlyTargetController::class, 'index'])->name('monthly-targets.index')->middleware('permission:monthly-target.view');
    Route::get('/monthly-targets/create', [MonthlyTargetController::class, 'create'])->name('monthly-targets.create')->middleware('permission:monthly-target.create');
    Route::post('/monthly-targets', [MonthlyTargetController::class, 'store'])->name('monthly-targets.store')->middleware('permission:monthly-target.create');
    Route::get('/monthly-targets/{monthlyTarget}', [MonthlyTargetController::class, 'show'])->name('monthly-targets.show')->middleware('permission:monthly-target.view');
    Route::get('/monthly-targets/{monthlyTarget}/edit', [MonthlyTargetController::class, 'edit'])->name('monthly-targets.edit')->middleware('permission:monthly-target.update');
    Route::put('/monthly-targets/{monthlyTarget}', [MonthlyTargetController::class, 'update'])->name('monthly-targets.update')->middleware('permission:monthly-target.update');
    Route::delete('/monthly-targets/{monthlyTarget}', [MonthlyTargetController::class, 'destroy'])->name('monthly-targets.destroy')->middleware('permission:monthly-target.delete');

    Route::get('/riwayat-penjualan/export/pdf', [RiwayatPenjualanController::class, 'pdf'])->name('riwayat-penjualan.pdf');

});

require __DIR__.'/auth.php';
