<?php

use App\Http\Controllers\Branch\BranchDashboardController;
use App\Http\Controllers\Branch\BranchItemController;
use App\Http\Controllers\Branch\BranchMaterialRequestController;
use App\Http\Controllers\Branch\BranchPegawaiController;
use App\Http\Controllers\Branch\BranchProductController;
use App\Http\Controllers\Branch\BranchPurchaseOrderController;
use App\Http\Controllers\Branch\BranchStockController;
use App\Http\Controllers\Branch\BranchSupplierController;
use App\Http\Controllers\Branch\BranchWarehouseController;
use App\Http\Controllers\Company\BomController;
use App\Http\Controllers\Company\CabangController;
use App\Http\Controllers\Company\CategoriesIssuesController;
use App\Http\Controllers\Company\CompanyDashboardController;
use App\Http\Controllers\Company\CompanySettingController;
use App\Http\Controllers\Company\ItemsController;
use App\Http\Controllers\Company\MaterialRequestController;
use App\Http\Controllers\Company\PegawaiController;
use App\Http\Controllers\Company\ProductsController;
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

/*
|--------------------------------------------------------------------------
| AUTH + TENANT SCOPE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant.path'])->group(function () {

    Route::get('/{code}/dashboard', [TenantDashboardController::class, 'index'])
        ->name('dashboard');

    Route::prefix('/company/{companyCode}')->group(function () {
        Route::get('/dashboard',
            [CompanyDashboardController::class, 'index']
        )->name('company.dashboard');

        Route::prefix('pegawai')->group(function () {

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

        Route::prefix('roles')->group(function () {

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

        Route::prefix('cabang')->group(function () {

            Route::get('/', [CabangController::class, 'index'])->name('cabang.index');

            Route::get('/tambah', [CabangController::class, 'create'])->name('cabang.create');
            Route::post('/', [CabangController::class, 'store'])->name('cabang.store');

            // EDIT — taruh sebelum {code}
            Route::get('/{code}/edit', [CabangController::class, 'edit'])->name('cabang.edit');
            Route::put('/{code}', [CabangController::class, 'update'])->name('cabang.update');

            Route::delete('/{code}', [CabangController::class, 'destroy'])->name('cabang.destroy');

            // DETAIL — paliiing bawah
            Route::get('/{code}', [CabangController::class, 'detail'])->name('cabang.detail');
        });

        Route::prefix('settings')->group(function () {

            Route::get('/general', [CompanySettingController::class, 'general'])
                ->name('settings.general');

            Route::post('/general', [CompanySettingController::class, 'generalUpdate'])
                ->name('settings.general.update');

            Route::prefix('masalah')->group(function () {
                Route::get('/', [CategoriesIssuesController::class, 'index'])->name('issues.index');

                Route::post('/', [CategoriesIssuesController::class, 'store'])->name('issues.store');

                Route::put('/{id}', [CategoriesIssuesController::class, 'update'])->name('issues.update');

                Route::delete('/{id}', [CategoriesIssuesController::class, 'destroy'])->name('issues.destroy');
            });
        });

        Route::prefix('items')->group(function () {

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

        Route::prefix('supplier')->group(function () {

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

        Route::prefix('gudang')->group(function () {

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

        Route::prefix('purchase-order')->group(function () {
            Route::get('/ajax/suppliers/{branchId}',
                [PurchaseOrderController::class, 'ajaxSuppliers']
            )->name('ajax.suppliers');

            Route::get('/ajax/suppliers/items/{supplierId}',
                [PurchaseOrderController::class, 'ajaxSupplierItems']
            )->name('ajax.supplier.items');

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

        Route::prefix('request-cabang')->group(function () {

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

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductsController::class, 'index'])
                ->name('products.index');

            Route::get('/create', [ProductsController::class, 'create'])
                ->name('products.create');

            Route::post('/store', [ProductsController::class, 'store'])
                ->name('products.store');

            Route::get('/{product}/edit', [ProductsController::class, 'edit'])
                ->name('products.edit');

            Route::put('/{product}/update', [ProductsController::class, 'update'])
                ->name('products.update');

            Route::delete('/{product}/delete', [ProductsController::class, 'destroy'])
                ->name('products.destroy');
            Route::get('/{product}/detail', [ProductsController::class, 'show'])
                ->name('products.show');
            // BOM ROUTES
            Route::get('/{product}/bom', [BomController::class, 'index'])
                ->name('products.bom.index');

            Route::post('/{product}/bom/store', [BomController::class, 'store'])
                ->name('products.bom.store');

            Route::put('/{product}/bom/{bom}/update', [BomController::class, 'update'])
                ->name('products.bom.update');

            Route::delete('/{product}/bom/{bom}/delete', [BomController::class, 'destroy'])
                ->name('products.bom.destroy');
        });

    });
    Route::prefix('/branch/{branchCode}')->group(function () {

        Route::get('/dashboard',
            [BranchDashboardController::class, 'index']
        )->name('branch.dashboard');

        Route::prefix('stock')->group(function () {

            Route::get('/', [BranchStockController::class, 'index'])
                ->name('branch.stock.index');
            Route::get('/create', [BranchStockController::class, 'createStockIn'])
                ->name('branch.stock.create');

            Route::post('/store', [BranchStockController::class, 'storeStockIn'])
                ->name('branch.stock.store');

            Route::post('/adjust',
                [BranchStockController::class, 'adjustStore'])
                ->name('branch.stock.adjust.store');

            Route::get('/{stock}/history', [BranchStockController::class, 'itemHistory'])
                ->name('branch.stock.history');

        });
        Route::prefix('penyimpanan')->group(function () {

            Route::get('/', [BranchWarehouseController::class, 'warehousesIndex'])
                ->name('branch.warehouse.index');

            Route::get('/create', [BranchWarehouseController::class, 'create'])
                ->name('branch.warehouse.create');

            // store action
            Route::post('/store', [BranchWarehouseController::class, 'store'])
                ->name('branch.warehouse.store');

            Route::get('/{warehouse}/edit', [BranchWarehouseController::class, 'edit'])
                ->name('branch.warehouse.edit');
            Route::post('/{warehouse}/update', [BranchWarehouseController::class, 'update'])
                ->name('branch.warehouse.update');
            // DELETE
            Route::delete('/{warehouse}/delete', [BranchWarehouseController::class, 'destroy'])
                ->name('branch.warehouse.destroy');
        });
        Route::prefix('purchase-order')->name('branch.po.')->group(function () {

            Route::get('/', [BranchPurchaseOrderController::class, 'index'])
                ->name('index');

            Route::get('/create', [BranchPurchaseOrderController::class, 'create'])
                ->name('create');

            Route::post('/store', [BranchPurchaseOrderController::class, 'store'])
                ->name('store');

            Route::get('/ajax/supplier-items/{supplierId}',
                [BranchPurchaseOrderController::class, 'ajaxSupplierItems']
            )->name('ajax.supplier.items');

            // SHOW DETAIL
            Route::get('/{id}', [BranchPurchaseOrderController::class, 'show'])
                ->name('show'); // EDIT PO (hanya untuk DRAFT)
            Route::get('/{id}/edit', [BranchPurchaseOrderController::class, 'edit'])
                ->name('edit');

            // UPDATE PO
            Route::put('/{id}', [BranchPurchaseOrderController::class, 'update'])
                ->name('update');

            // APPROVE (status → APPROVED)
            Route::post('/{id}/approve', [BranchPurchaseOrderController::class, 'approve'])
                ->name('approve');

            // CANCEL (status → CANCELLED)
            Route::post('/{id}/cancel', [BranchPurchaseOrderController::class, 'cancel'])
                ->name('cancel');

            // RECEIVE FORM
            Route::get('/{id}/receive', [BranchPurchaseOrderController::class, 'showReceiveForm'])
                ->name('receive.show');

            // PROCESS RECEIVE
            Route::post('/{id}/receive', [BranchPurchaseOrderController::class, 'processReceive'])
                ->name('receive.process');

            // DELETE PO
            Route::delete('/{id}', [BranchPurchaseOrderController::class, 'destroy'])
                ->name('destroy');

            // UPDATE STATUS (PATCH)
            Route::patch('/{id}/status', [BranchPurchaseOrderController::class, 'updateStatus'])
                ->name('updateStatus');

        });
        Route::prefix('supplier')->name('branch.supplier.')->group(function () {

            Route::get('/', [BranchSupplierController::class, 'index'])
                ->name('index');

            Route::get('/create', [BranchSupplierController::class, 'create'])
                ->name('create');

            Route::post('/store', [BranchSupplierController::class, 'store'])
                ->name('store');

            Route::get('/{id}/edit', [BranchSupplierController::class, 'edit'])
                ->name('edit');

            Route::put('/{id}', [BranchSupplierController::class, 'update'])
                ->name('update');

            Route::delete('/{id}/delete', [BranchSupplierController::class, 'destroy'])
                ->name('destroy');

            Route::post('/{supplier}/item', [BranchSupplierController::class, 'itemStore'])
                ->name('item.store');

            Route::put('/{supplier}/item/{itemId}', [BranchSupplierController::class, 'itemUpdate'])
                ->name('item.update');

            Route::delete('/{supplier}/item/{itemId}', [BranchSupplierController::class, 'itemDestroy'])
                ->name('item.delete');

            Route::post('/{id}/score/generate', [BranchSupplierController::class, 'generateScore'])
                ->name('score.generate');

            Route::post('/{id}/score/period', [BranchSupplierController::class, 'generateScoreWithPeriod'])
                ->name('score.period');

            Route::get('/{id}', [BranchSupplierController::class, 'show'])
                ->name('show');

        });

        Route::prefix('request-cabang')->group(function () {

            Route::get('/', [BranchMaterialRequestController::class, 'index'])->name('branch.request.index');

            Route::get('/create', [BranchMaterialRequestController::class, 'create'])->name('branch.request.create');

            Route::post('/', [BranchMaterialRequestController::class, 'store'])->name('branch.request.store');

            Route::get('/items/{branchId}', [BranchMaterialRequestController::class, 'loadItems'])->name('branch.request.load.items');

            Route::get('/{id}', [BranchMaterialRequestController::class, 'show'])->name('branch.request.show');

            // ACTION OLEH PENGIRIM (cabang_from)
            Route::post('/{id}/approve', [BranchMaterialRequestController::class, 'approve'])
                ->name('branch.request.approve');
            Route::post('/{id}/reject', [BranchMaterialRequestController::class, 'reject'])
                ->name('branch.request.reject');

            // HAPUS / EDIT (oleh penerima cabang_to)
            Route::delete('/{id}', [BranchMaterialRequestController::class, 'destroy'])
                ->name('branch.request.destroy');
            Route::get('/{id}/edit', [BranchMaterialRequestController::class, 'edit'])
                ->name('branch.request.edit');
            Route::put('/{id}', [BranchMaterialRequestController::class, 'update'])
                ->name('branch.request.update');
            Route::post('/request/{id}/send',
                [BranchMaterialRequestController::class, 'send'])
                ->name('branch.request.send');
            Route::post('/request/{id}/receive',
                [BranchMaterialRequestController::class, 'receive'])
                ->name('branch.request.receive');
        });
        Route::prefix('item')->group(function () {

            Route::get('/', [BranchItemController::class, 'index'])->name('branch.item.index');
            Route::get('/create', [BranchItemController::class, 'create'])->name('branch.item.create');
            Route::post('/store', [BranchItemController::class, 'store'])->name('branch.item.store');

            Route::get('/{id}/edit', [BranchItemController::class, 'edit'])->name('branch.item.edit');
            Route::put('/{id}/update', [BranchItemController::class, 'update'])->name('branch.item.update');

            Route::delete('/{id}/delete', [BranchItemController::class, 'destroy'])->name('branch.item.destroy');

            Route::get('/{item}', [BranchItemController::class, 'show'])
                ->name('branch.item.show');

            Route::get('/{item}/warehouse/{warehouse}/edit',
                [BranchItemController::class, 'editStock'])
                ->name('branch.item.stock.edit');

            Route::post('/{item}/warehouse/{warehouse}/update',
                [BranchItemController::class, 'updateStock'])
                ->name('branch.item.stock.update');

            Route::get('/{item}/history',
                [BranchItemController::class, 'itemHistoryByItem'])
                ->name('branch.item.history');

        });

        Route::prefix('products')->group(function () {
            Route::get('/', [BranchProductController::class, 'index'])->name('branch.products.index');
            Route::get('/{product}/detail',
                [BranchProductController::class, 'show']
            )->name('branch.products.show');

        });

        Route::prefix('pegawai')->group(function () {

            // LIST PEGAWAI CABANG
            Route::get('/',
                [BranchPegawaiController::class, 'index']
            )->name('branch.pegawai.index');

            // CREATE PAGE
            Route::get('/create',
                [BranchPegawaiController::class, 'create']
            )->name('branch.pegawai.create');

            // STORE
            Route::post('/',
                [BranchPegawaiController::class, 'store']
            )->name('branch.pegawai.store');

            // EDIT PAGE
            Route::get('/{id}/edit',
                [BranchPegawaiController::class, 'edit']
            )->name('branch.pegawai.edit');

            // UPDATE
            Route::put('/{id}',
                [BranchPegawaiController::class, 'update']
            )->name('branch.pegawai.update');

            // DELETE
            Route::delete('/{id}',
                [BranchPegawaiController::class, 'destroy']
            )->name('branch.pegawai.destroy');

        });

    });

});

require __DIR__.'/auth.php';
