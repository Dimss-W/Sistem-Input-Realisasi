<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BastoController;
use App\Http\Controllers\PrognosaController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DmoImportHistoryController;
use App\Http\Controllers\PeriodLockController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\StandbyController;

use App\Http\Controllers\CostKontrakController;
use App\Http\Controllers\VendorInvoiceController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes — Enterprise Input Realisasi & PGN Monitoring System
|--------------------------------------------------------------------------
*/

// Public Standby Kiosk Dashboard (TV Display / Command Center - Zero Scroll & Realtime Polling)
Route::get('/standby', [StandbyController::class, 'index'])->name('standby.index');
Route::get('/api/standby-data', [StandbyController::class, 'getData'])->name('standby.data');

// Auth Guest & Public Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Auth Protected Routes
Route::middleware(['auth'])->group(function () {

    // Root redirect to central Dashboard
    Route::get('/', function () {
        if (auth()->check() && auth()->user()->hasRole('procurement')) {
            return redirect()->route('procurement.dashboard');
        }
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    });

    // Exit Impersonation Mode (Available for any authenticated user currently impersonating)
    Route::match(['get', 'post'], '/admin/stop-impersonate', [AdminController::class, 'stopImpersonate'])->name('admin.stop-impersonate');

    // Main Dashboard & Calendar & Quick Search
    Route::get('/dashboard', [RealisasiController::class, 'dashboard'])->name('dashboard');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/quick-search', [RealisasiController::class, 'quickSearch'])->name('quick-search');

    // -------------------------------------------------------------------------
    // 1. ADMIN PANEL (User Management, Period Locks, Master Data, Impersonate)
    // -------------------------------------------------------------------------
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [AdminController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [AdminController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{id}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::patch('/users/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.users.toggle');
        Route::post('/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.users.reset-password');
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
        Route::get('/impersonate/{id}', [AdminController::class, 'impersonate'])->name('admin.impersonate');

        // Period Locking & Closing
        Route::get('/period-locks', [PeriodLockController::class, 'index'])->name('admin.period-locks.index');
        Route::post('/period-locks/toggle', [PeriodLockController::class, 'toggle'])->name('admin.period-locks.toggle');
    });

    // -------------------------------------------------------------------------
    // 1.1 CENTRALIZED MASTER DATA (Vendors, Clients, Projects & Signers)
    // Accessible by Admin & DMO
    // -------------------------------------------------------------------------
    Route::prefix('admin')->middleware('role:admin,dmo')->group(function () {
        Route::get('/master-data', [MasterDataController::class, 'index'])->name('admin.master-data.index');
        Route::post('/master-data/sync', [MasterDataController::class, 'sync'])->name('admin.master-data.sync');
        Route::post('/master-data/vendors', [MasterDataController::class, 'storeVendor'])->name('admin.master-data.vendors.store');
        Route::put('/master-data/vendors/{id}', [MasterDataController::class, 'updateVendor'])->name('admin.master-data.vendors.update');
        Route::delete('/master-data/vendors/{id}', [MasterDataController::class, 'deleteVendor'])->name('admin.master-data.vendors.delete');
        Route::post('/master-data/clients', [MasterDataController::class, 'storeClient'])->name('admin.master-data.clients.store');
        Route::put('/master-data/clients/{id}', [MasterDataController::class, 'updateClient'])->name('admin.master-data.clients.update');
        Route::delete('/master-data/clients/{id}', [MasterDataController::class, 'deleteClient'])->name('admin.master-data.clients.delete');
        Route::post('/master-data/signers', [MasterDataController::class, 'updateSigners'])->name('admin.master-data.signers.update');

        // Master Proyek & Pagu Kontrak — DIKUNCI KHUSUS ROLE DMO (Admin tidak input proyek)
        Route::post('/master-data/projects', [MasterDataController::class, 'storeProject'])->name('admin.master-data.projects.store')->middleware('role:dmo');
        Route::put('/master-data/projects/{id}', [MasterDataController::class, 'updateProject'])->name('admin.master-data.projects.update')->middleware('role:dmo');
        Route::delete('/master-data/projects/{id}', [MasterDataController::class, 'deleteProject'])->name('admin.master-data.projects.delete')->middleware('role:dmo');
    });

    // -------------------------------------------------------------------------
    // 2. REALISASI BIAYA & EXCEL IMPORT (DMO, Admin, SM, QC, Procurement)
    // -------------------------------------------------------------------------
    Route::middleware('role:dmo,osm_service_manager,osm_qc,procurement,admin')->group(function () {

        // DMO & Admin Excel Import
        Route::middleware('role:dmo,admin')->group(function () {
            Route::get('/realisasi/import', [RealisasiController::class, 'importForm'])->name('realisasi.import.form');
            Route::post('/realisasi/import', [RealisasiController::class, 'import'])->name('realisasi.import');
            Route::get('/realisasi/template', [RealisasiController::class, 'downloadTemplate'])->name('realisasi.template');
            Route::get('/dmo/import-history', [DmoImportHistoryController::class, 'index'])->name('dmo.import.history');
        });

        Route::get('/realisasi/export', [RealisasiController::class, 'export'])->name('realisasi.export');
        Route::get('/api/exchange-rates', [RealisasiController::class, 'getExchangeRates'])->name('api.exchange-rates');

        // CRUD Realisasi
        Route::resource('realisasi', RealisasiController::class);

        // Executive Portfolio Summary for Service Manager, Admin, DMO & Procurement
        Route::get('/service-manager/executive-summary', [RealisasiController::class, 'executiveSummary'])->name('sm.executive-summary')->middleware('role:osm_service_manager,admin,dmo,procurement');

        // ---------------------------------------------------------------------
        // 3. TIGA PILAR MONITORING UTAMA (BASTO, COST KONTRAK, INVOICE VENDOR)
        // ---------------------------------------------------------------------
        // Pilar 1: Monitoring BASTO
        Route::get('/monitoring/basto', [BastoController::class, 'index'])->name('monitoring.basto')->middleware('role:admin,dmo,osm_service_manager,osm_qc,procurement');

        // Pilar 2: Monitoring Cost Kontrak (Cost vs Realisasi)
        Route::get('/monitoring/cost-kontrak', [CostKontrakController::class, 'index'])->name('monitoring.cost-kontrak')->middleware('role:admin,dmo,osm_service_manager,procurement');

        // Pilar 3: Monitoring Invoice Vendor (Prognosa vs Actual)
        Route::get('/monitoring/invoice-vendor', [VendorInvoiceController::class, 'index'])->name('monitoring.invoice-vendor')->middleware('role:admin,dmo,procurement');
        Route::post('/monitoring/invoice-vendor/{id}/quick-update-status', [VendorInvoiceController::class, 'quickUpdateStatus'])->name('monitoring.invoice-vendor.update-status')->middleware('role:admin,dmo');

        // Input & Kelola Detail Invoice Vendor (Admin, Procurement, DMO)
        Route::middleware('role:admin,procurement,dmo')->group(function () {
            Route::post('/invoice/scan', [InvoiceController::class, 'scan'])->name('invoice.scan');
            Route::get('/invoice/export', [InvoiceController::class, 'export'])->name('invoice.export');
            Route::get('/invoice/payment-upload', [InvoiceController::class, 'uploadPaymentForm'])->name('payment.upload');
            Route::post('/invoice/payment-upload', [InvoiceController::class, 'uploadPayment'])->name('payment.upload.process');
            Route::get('/invoice/payment-template', [InvoiceController::class, 'downloadPaymentTemplate'])->name('payment.template');
            Route::post('/invoice/{id}/payment', [InvoiceController::class, 'storePayment'])->name('invoice.payment.store');
            Route::post('/invoice/{id}/followup', [InvoiceController::class, 'updateFollowup'])->name('invoice.followup.update');
            Route::resource('invoice', InvoiceController::class);
        });

        // ---------------------------------------------------------------------
        // 4. PROCUREMENT HUB (PENGADAAN BARANG & JASA END-TO-END)
        // ---------------------------------------------------------------------
        Route::prefix('procurement')->middleware('role:procurement,admin')->group(function () {
            Route::get('/dashboard', [ProcurementController::class, 'dashboard'])->name('procurement.dashboard');
            Route::get('/vendors', [ProcurementController::class, 'vendors'])->name('procurement.vendors');
            Route::get('/orders', [ProcurementController::class, 'orders'])->name('procurement.orders');
            Route::post('/orders', [ProcurementController::class, 'storeOrder'])->name('procurement.orders.store');
            Route::get('/verification', [ProcurementController::class, 'verification'])->name('procurement.verification');
            Route::post('/verification/{id}/complete', [ProcurementController::class, 'completeVerification'])->name('procurement.verification.complete');
        });

        // ---------------------------------------------------------------------
        // 5. BASTO WORKFLOW (DMO, Service Manager, QC, Procurement, Admin)
        // ---------------------------------------------------------------------
        Route::post('/basto/batch-approve', [BastoController::class, 'batchApprove'])->name('basto.batch-approve')->middleware('role:osm_service_manager,admin');
        Route::post('/basto/batch-submit', [BastoController::class, 'batchSubmit'])->name('basto.batch-submit')->middleware('role:dmo,admin');
        Route::get('/basto/{basto}/attachment', [BastoController::class, 'downloadAttachment'])->name('basto.attachment');
        Route::post('/basto/{basto}/reupload-revision', [BastoController::class, 'reuploadRevision'])->name('basto.reupload-revision')->middleware('role:dmo,admin');
        Route::patch('/basto/{basto}/qc-verify', [BastoController::class, 'qcVerify'])->name('basto.qc-verify')->middleware('role:osm_qc,admin');
        Route::patch('/basto/{basto}/submit', [BastoController::class, 'submitForReview'])->name('basto.submit')->middleware('role:dmo,admin');
        Route::patch('/basto/{basto}/approve', [BastoController::class, 'approve'])->name('basto.approve')->middleware('role:osm_service_manager,admin');
        Route::patch('/basto/{basto}/reject', [BastoController::class, 'reject'])->name('basto.reject')->middleware('role:osm_service_manager,admin');
        Route::resource('basto', BastoController::class);

        // ---------------------------------------------------------------------
        // 6. PROGNOSA & BUDGET MONITORING
        // ---------------------------------------------------------------------
        Route::post('/prognosa/update-monthly', [PrognosaController::class, 'updateMonthlyPrognosa'])->name('prognosa.update-monthly')->middleware('role:osm_service_manager,admin');
        Route::post('/prognosa/{id}/reconcile', [PrognosaController::class, 'reconcileOverride'])->name('prognosa.reconcile')->middleware('role:osm_service_manager,admin');
        Route::get('/prognosa', [PrognosaController::class, 'index'])->name('prognosa.index');
        Route::get('/prognosa/{projectId}', [PrognosaController::class, 'show'])->name('prognosa.show');

        // ---------------------------------------------------------------------
        // 7. WORK ORDERS & AUDIT LOG
        // ---------------------------------------------------------------------
        Route::resource('work-order', WorkOrderController::class);

        Route::middleware('role:admin')->group(function () {
            Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.log');
        });
    });
});
