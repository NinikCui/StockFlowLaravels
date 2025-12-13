<?php

use App\Http\Controllers\Branch\BranchDashboardController;
use App\Http\Controllers\Branch\BranchItemController;
use App\Http\Controllers\Branch\BranchMaterialRequestController;
use App\Http\Controllers\Branch\BranchPegawaiController;
use App\Http\Controllers\Branch\BranchProductController;
use App\Http\Controllers\Branch\BranchPurchaseOrderController;
use App\Http\Controllers\Branch\BranchRoleController;
use App\Http\Controllers\Branch\BranchStockController;
use App\Http\Controllers\Branch\BranchSupplierController;
use App\Http\Controllers\Branch\BranchWarehouseController;
use App\Http\Controllers\Branch\MenuPromotionController;
use App\Http\Controllers\Branch\PosOrderController;
use App\Http\Controllers\Branch\PosShiftController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.path'])->group(function () {

    Route::prefix('/branch/{branchCode}')->group(function () {

        Route::get('/dashboard',
            [BranchDashboardController::class, 'index']
        )->name('branch.dashboard');
        Route::post(
            '/menu-promo/generate',
            [MenuPromotionController::class, 'generate']
        )->name('branch.menu-promo.generate');
        Route::get(
            '/menu-promotion',
            [MenuPromotionController::class, 'index']
        )->name('branch.menu-promo.index');

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
            Route::delete('/{stock}',
                [BranchStockController::class, 'destroy']
            )->name('branch.stock.delete');

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

            Route::get('/po/{po}/print', [BranchPurchaseOrderController::class, 'print'])
                ->name('print');

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
        Route::prefix('/roles')
            ->name('branch.roles.')
            ->group(function () {

                // INDEX
                Route::get('/', [BranchRoleController::class, 'index'])
                    ->name('index');

                // CREATE
                Route::get('/create', [BranchRoleController::class, 'create'])
                    ->name('create');

                // STORE
                Route::post('/store', [BranchRoleController::class, 'store'])
                    ->name('store');

                // SHOW
                Route::get('/{code}', [BranchRoleController::class, 'show'])
                    ->name('show');

                // EDIT
                Route::get('/{code}/edit', [BranchRoleController::class, 'edit'])
                    ->name('edit');

                // UPDATE
                Route::put('/{code}', [BranchRoleController::class, 'update'])
                    ->name('update');

                // DELETE
                Route::delete('/{code}', [BranchRoleController::class, 'destroy'])
                    ->name('destroy');
            });

        Route::prefix('/pos-shift')
            ->name('branch.pos.shift.')
            ->group(function () {

                Route::get('/', [PosShiftController::class, 'index'])->name('index');

                Route::get('/open', [PosShiftController::class, 'openForm'])->name('openForm');
                Route::post('/open', [PosShiftController::class, 'open'])->name('open');

                Route::get('/{shift}/close', [PosShiftController::class, 'closeForm'])->name('closeForm');
                Route::post('/{shift}/close', [PosShiftController::class, 'close'])->name('close');
            });
        Route::prefix('/pos')
            ->name('branch.pos.')
            ->group(function () {

                // CART PAGE
                Route::get('/order', [PosOrderController::class, 'index'])->name('order.index');

                // ADD ITEM TO CART
                Route::post('/order/add', [PosOrderController::class, 'add'])->name('order.add');

                // REMOVE ITEM
                Route::post('/order/remove', [PosOrderController::class, 'remove'])->name('order.remove');

                // CHECKOUT + PAY
                Route::post('/order/pay', [PosOrderController::class, 'pay'])->name('order.pay');
                Route::post('/order/note',
                    [PosOrderController::class, 'updateNote']
                )->name('order.note');
                Route::post('/order/midtrans/',
                    [PosOrderController::class, 'createMidtransPayment']
                )->name('order.midtrans');

                Route::get('/shift/{shiftId}/history',
                    [PosShiftController::class, 'history'])
                    ->name('shift.history');

            });

    });

});
