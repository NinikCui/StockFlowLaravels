<?php

use App\Http\Controllers\Company\CabangController;
use App\Http\Controllers\Company\CategoriesIssuesController;
use App\Http\Controllers\Company\CompanyDashboardController;
use App\Http\Controllers\Company\CompanySettingController;
use App\Http\Controllers\Company\ItemsController;
use App\Http\Controllers\Company\MaterialRequestController;
use App\Http\Controllers\Company\PegawaiController;
use App\Http\Controllers\Company\PurchaseOrderController;
use App\Http\Controllers\Company\RoleController;
use App\Http\Controllers\Company\StockController;
use App\Http\Controllers\Company\SupplierController;
use App\Http\Controllers\Company\WarehouseController;
use App\Http\Controllers\TenantDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'tenant.path'])->group(function () {

    // MAIN ENTRY
    Route::get('/{code}/dashboard', [TenantDashboardController::class, 'index'])
        ->name('dashboard');

    // COMPANY
    Route::get('/{companyCode}/dashboard/company',
        [CompanyDashboardController::class, 'index'])
        ->name('company.dashboard');

    // BRANCH
    Route::get('/{branchCode}/dashboard/branch',
        [BranchDashboardController::class, 'index'])
        ->name('branch.dashboard');

    Route::prefix('{companyCode}/pegawai')->group(function () {

        Route::get('/', [PegawaiController::class, 'index'])->name('pegawai.index');

        Route::get('/tambah', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/', [PegawaiController::class, 'store'])->name('pegawai.store');

        // EDIT harus diletakkan sebelum DELETE
        Route::get('/edit/{id}', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');

        Route::delete('/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

        // AJAX posisi bebas aman
        Route::get('/roles-json', [RoleController::class, 'rolesJson'])->name('roles.json');

        Route::get('/manage', [PegawaiController::class, 'combined'])
            ->name('pegawai.roles.combined');

    });
    Route::prefix('{companyCode}/roles')->group(function () {
        // LIST ROLES
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');

        // CREATE
        Route::get('/tambah', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/store', [RoleController::class, 'store'])->name('roles.store');

        // EDIT ROLE — harus sebelum {code}
        Route::get('/{code}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{code}', [RoleController::class, 'update'])->name('roles.update');

        // DELETE ROLE
        Route::delete('/{code}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // SHOW ROLE — paling bawah karena paling umum, biar tidak override route lainnya
        Route::get('/{code}', [RoleController::class, 'show'])->name('roles.show');
    });

    Route::prefix('{companyCode}/cabang')->group(function () {

        Route::get('/', [CabangController::class, 'index'])->name('cabang.index');

        Route::get('/tambah', [CabangController::class, 'create'])->name('cabang.create');
        Route::post('/', [CabangController::class, 'store'])->name('cabang.store');

        // EDIT — taruh sebelum {code}
        Route::get('/{code}/edit', [CabangController::class, 'edit'])->name('cabang.edit');
        Route::put('/{code}', [CabangController::class, 'update'])->name('cabang.update');

        // DETAIL — paliiing bawah
        Route::get('/{code}', [CabangController::class, 'detail'])->name('cabang.detail');
    });

    Route::prefix('{companyCode}/settings')->group(function () {

        Route::get('/general', [CompanySettingController::class, 'general'])->name('settings.general');
        Route::post('/general', [CompanySettingController::class, 'generalUpdate'])->name('settings.general.update');
        Route::prefix('masalah')->group(function () {
            Route::get('/', [CategoriesIssuesController::class, 'index'])->name('issues.index');

            Route::post('/', [CategoriesIssuesController::class, 'store'])->name('issues.store');

            Route::put('/{id}', [CategoriesIssuesController::class, 'update'])->name('issues.update');

            Route::delete('/{id}', [CategoriesIssuesController::class, 'destroy'])->name('issues.destroy');
        });
    });

    Route::prefix('{companyCode}/items')->group(function () {

        Route::get('/', [ItemsController::class, 'index'])
            ->name('items.index');

        // Item
        Route::get('/create', [ItemsController::class, 'createItem'])->name('item.create');
        Route::post('/store', [ItemsController::class, 'storeItem'])->name('item.store');
        Route::get('/{id}/edit', [ItemsController::class, 'editItem'])->name('item.edit');
        Route::put('/{id}', [ItemsController::class, 'updateItem'])->name('item.update');
        Route::delete('/{id}', [ItemsController::class, 'deleteItem'])->name('item.destroy');

        // Category
        Route::get('/category/create', [ItemsController::class, 'createCategory'])->name('category.create');
        Route::post('/category', [ItemsController::class, 'storeCategory'])->name('category.store');
        Route::get('/category/{code}/edit', [ItemsController::class, 'editCategory'])->name('category.edit');
        Route::put('/category/{code}', [ItemsController::class, 'updateCategory'])->name('category.update');
        Route::delete('/category/{code}', [ItemsController::class, 'deleteCategory'])->name('category.destroy');

        // Satuan
        Route::get('/satuan/create', [ItemsController::class, 'createSatuan'])->name('satuan.create');
        Route::post('/satuan', [ItemsController::class, 'storeSatuan'])->name('satuan.store');
        Route::get('/satuan/{code}/edit', [ItemsController::class, 'editSatuan'])->name('satuan.edit');
        Route::put('/satuan/{code}', [ItemsController::class, 'updateSatuan'])->name('satuan.update');
        Route::delete('/satuan/{code}', [ItemsController::class, 'deleteSatuan'])->name('satuan.destroy');
    });

    Route::prefix('{companyCode}/supplier')->group(function () {

        Route::get('/', [SupplierController::class, 'index'])->name('supplier.index');

        Route::get('/create', [SupplierController::class, 'create'])->name('supplier.create');
        Route::post('/', [SupplierController::class, 'store'])->name('supplier.store');

        Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
        Route::put('/{id}', [SupplierController::class, 'update'])->name('supplier.update');

        Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

        Route::get('/{id}', [SupplierController::class, 'show'])->name('supplier.show');

        Route::prefix('{supplier}')->group(function () {

            Route::post('/items', [SupplierController::class, 'itemStore'])->name('supplier.items.store');

            Route::put('/items/{item}', [SupplierController::class, 'itemUpdate'])->name('supplier.items.update');

            Route::delete('/items/{item}', [SupplierController::class, 'itemDestroy'])->name('supplier.items.destroy');
        });

        Route::post('/{id}/generate-score',
            [SupplierController::class, 'generateScore']
        )->name('supplier.generateScore');
        Route::post('/{id}/generate-score-period',
            [SupplierController::class, 'generateScoreWithPeriod']
        )->name('supplier.generateScorePeriod');

    });

    Route::prefix('{companyCode}/gudang')->group(function () {

        Route::get('/', [WarehouseController::class, 'index'])->name('warehouse.index');
        Route::get('/create', [WarehouseController::class, 'create'])->name('warehouse.create');
        Route::post('/', [WarehouseController::class, 'store'])->name('warehouse.store');
        Route::get('/{id}/edit', [WarehouseController::class, 'edit'])->name('warehouse.edit');
        Route::put('/{id}', [WarehouseController::class, 'update'])->name('warehouse.update');
        Route::delete('/{id}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');

        Route::get('/types', [WarehouseController::class, 'typesIndex'])->name('warehouse.types.index');
        Route::post('/types', [WarehouseController::class, 'typesStore'])->name('warehouse.types.store');
        Route::delete('/types/{id}', [WarehouseController::class, 'typesDestroy'])->name('warehouse.types.destroy');

        // ============================
        // DETAIL GUDANG
        // ============================
        Route::get('/{warehouseId}', [WarehouseController::class, 'show'])->name('warehouse.show');

        Route::prefix('{warehouseId}/stock')->group(function () {

            // --- STOCK IN ---
            Route::get('/create',
                [StockController::class, 'createIn'])->name('stock.in.create');

            Route::post('/in',
                [StockController::class, 'storeIn'])->name('stock.in.store');

            Route::post('/stock/adjust', [StockController::class, 'storeAdjustment'])->name('stock.adjust.store');

            // --- MOVEMENTS (History) ---
            Route::get('/{itemId}/history', [StockController::class, 'itemHistory'])->name('stock.item.history');

        });

    });

    Route::prefix('{companyCode}/purchase-order')->group(function () {

        // LIST PO
        Route::get('/', [PurchaseOrderController::class, 'index'])
            ->name('po.index');

        // FORM CREATE
        Route::get('/create', [PurchaseOrderController::class, 'create'])
            ->name('po.create');

        // STORE
        Route::post('/', [PurchaseOrderController::class, 'store'])
            ->name('po.store');

        // SHOW DETAIL
        Route::get('/{id}', [PurchaseOrderController::class, 'show'])
            ->name('po.show');

        // EDIT PO (hanya untuk DRAFT)
        Route::get('/{id}/edit', [PurchaseOrderController::class, 'edit'])
            ->name('po.edit');

        // UPDATE PO
        Route::put('/{id}', [PurchaseOrderController::class, 'update'])
            ->name('po.update');

        // UBAH STATUS → APPROVED
        Route::post('/{id}/approve', [PurchaseOrderController::class, 'approve'])
            ->name('po.approve');

        // UBAH STATUS → CANCELLED
        Route::post('/{id}/cancel', [PurchaseOrderController::class, 'cancel'])
            ->name('po.cancel');

        // Route Receive Baru
        Route::get('/{po}/receive', [PurchaseOrderController::class, 'showReceiveForm'])
            ->name('po.receive.show');

        Route::post('/{po}/receive', [PurchaseOrderController::class, 'processReceive'])
            ->name('po.receive.process');

        Route::delete('/{id}', [PurchaseOrderController::class, 'destroy'])->name('po.destroy');
        Route::patch('/{id}/status', [PurchaseOrderController::class, 'updateStatus'])->name('po.updateStatus');
    });

    Route::prefix('{companyCode}/request-cabang')->group(function () {

        Route::get('/', [MaterialRequestController::class, 'index'])->name('request.index');

        Route::get('/create', [MaterialRequestController::class, 'create'])->name('request.create');

        Route::post('/', [MaterialRequestController::class, 'store'])->name('request.store');
        Route::post('/anjay', [MaterialRequestController::class, 'store'])->name('request.storeeeee');

        Route::get('/items/{branchId}', [MaterialRequestController::class, 'loadItems'])->name('request.load.items');
        Route::get('/{id}', [MaterialRequestController::class, 'show'])->name('request.show');

        Route::get('/{id}/edit',
            [MaterialRequestController::class, 'edit'])->name('request.edit');
        Route::put('/{id}', [MaterialRequestController::class, 'update'])->name('request.update');
        Route::delete('/{id}', [MaterialRequestController::class, 'destroy'])->name('request.destroy');

        Route::get('/analytics/cabang', [MaterialRequestController::class, 'cabangAnalytics'])->name('analytics.cabang');
    });

});

require __DIR__.'/auth.php';
