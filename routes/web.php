<?php

use App\Http\Controllers\Company\CategoryController;
use App\Http\Controllers\Company\CompanySettingController;
use App\Http\Controllers\Company\ItemsController;
use App\Http\Controllers\Company\PegawaiController;
use App\Http\Controllers\Company\SatuanController;
use App\Http\Controllers\Company\StockController;
use App\Http\Controllers\Company\SupplierController;
use App\Http\Controllers\Company\WarehouseController;
use App\Http\Controllers\TenantDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\RoleController;
use App\Http\Controllers\Company\CabangController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'tenant.path'])->group(function () {

    // DASHBOARD
    Route::get('/{code}/dashboard', [TenantDashboardController::class, 'index'])
        ->name('dashboard');


    Route::prefix('{companyCode}/pegawai')->group(function () {

        Route::get('/', [PegawaiController::class, 'index'])->name('pegawai.index');

        Route::get('/tambah', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/', [PegawaiController::class, 'store'])->name('pegawai.store');

        // AJAX
        Route::get('/roles-json', [RoleController::class, 'rolesJson'])->name('roles.json');

        // EDIT PEGAWAI (lebih spesifik, taruh atas)
        Route::get('/edit/{id}', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');

        //DELTE PEGAWAI
        Route::delete('/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

        // ROLE MANAGEMENT
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/tambah', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');

        // Route detail/edit role — lebih spesifik dulu
        Route::get('/roles/{code}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{code}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{code}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // ROLE SHOW — paling bawah karena paling umum
        Route::get('/roles/{code}', [RoleController::class, 'show'])->name('roles.show');
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
    });

    Route::prefix('{companyCode}/items')->group(function () {

        Route::get('/', [ItemsController::class, 'index'])
            ->name('items.index');

        // Item 
        Route::get('/create', [ItemsController::class, 'createItem'])->name('items.item.create');
        Route::post('/store', [ItemsController::class, 'storeItem'])->name('items.item.store');
        Route::get('/{id}/edit', [ItemsController::class, 'editItem'])->name('items.item.edit');
        Route::put('/{id}', [ItemsController::class, 'updateItem'])->name('items.item.update');
        Route::delete('/{id}', [ItemsController::class, 'deleteItem'])->name('items.item.delete');

        // Category 
        Route::get('/category/create', [ItemsController::class, 'createCategory'])->name('items.category.create');
        Route::post('/category', [ItemsController::class, 'storeCategory'])->name('items.category.store');
        Route::get('/category/{code}/edit', [ItemsController::class, 'editCategory'])->name('items.category.edit');
        Route::put('/category/{code}', [ItemsController::class, 'updateCategory'])->name('items.category.update');
        Route::delete('/category/{code}', [ItemsController::class, 'deleteCategory'])->name('items.category.delete');

        // Satuan 
        Route::get('/satuan/create', [ItemsController::class, 'createSatuan'])->name('items.satuan.create');
        Route::post('/satuan', [ItemsController::class, 'storeSatuan'])->name('items.satuan.store');
        Route::get('/satuan/{code}/edit', [ItemsController::class, 'editSatuan'])->name('items.satuan.edit');
        Route::put('/satuan/{code}', [ItemsController::class, 'updateSatuan'])->name('items.satuan.update');
        Route::delete('/satuan/{code}', [ItemsController::class, 'deleteSatuan'])->name('items.satuan.delete');
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
    });

    Route::prefix('{companyCode}/gudang')->group(function () {

        Route::get('/',             [WarehouseController::class, 'index'])->name('warehouse.index');
        Route::get('/create',       [WarehouseController::class, 'create'])->name('warehouse.create');
        Route::post('/',            [WarehouseController::class, 'store'])->name('warehouse.store');
        Route::get('/{id}/edit',    [WarehouseController::class, 'edit'])->name('warehouse.edit');
        Route::put('/{id}',         [WarehouseController::class, 'update'])->name('warehouse.update');
        Route::delete('/{id}',      [WarehouseController::class, 'destroy'])->name('warehouse.destroy');

        Route::get('/types',           [WarehouseController::class, 'typesIndex'])->name('warehouse.types.index');
        Route::post('/types',          [WarehouseController::class, 'typesStore'])->name('warehouse.types.store');
        Route::delete('/types/{id}',   [WarehouseController::class, 'typesDestroy'])->name('warehouse.types.destroy');


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


            Route::post('/stock/adjust', [StockController::class, 'storeAdjustment']) ->name('stock.adjust.store');


            // --- MOVEMENTS (History) ---
            Route::get('/movements', 
                [StockController::class, 'movements'])->name('stock.movements');

        });

    });
});

require __DIR__ . '/auth.php';