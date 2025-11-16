<?php

use App\Http\Controllers\Company\CategoryController;
use App\Http\Controllers\Company\CompanySettingController;
use App\Http\Controllers\Company\ItemController;
use App\Http\Controllers\Company\PegawaiController;
use App\Http\Controllers\Company\SatuanController;
use App\Http\Controllers\Company\SupplierController;
use App\Http\Controllers\ProfileController;
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


    // ================================
    // PEGAWAI
    // ================================
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


    // ================================
    // CABANG RESTO
    // ================================
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

    // ================================
    // COMPANY SETTING
    // ================================
    Route::prefix('{companyCode}/settings')->group(function () {
        
        Route::get('/general', [CompanySettingController::class, 'general'])->name('settings.general');
        Route::post('/general', [CompanySettingController::class, 'generalUpdate'])->name('settings.general.update');
    });

    Route::prefix('{companyCode}/product/categories')->group(function () {

        Route::get('/', [CategoryController::class, 'index'])
            ->name('category.index');

        Route::get('/create', [CategoryController::class, 'create'])
            ->name('category.create');

        Route::post('/', [CategoryController::class, 'store'])
            ->name('category.store');

        Route::get('/{code}/edit', [CategoryController::class, 'edit'])
            ->name('category.edit');

        Route::put('/{code}', [CategoryController::class, 'update'])
            ->name('category.update');

        Route::delete('/{code}', [CategoryController::class, 'destroy'])
            ->name('category.destroy');
    });
    Route::prefix('{companyCode}/product/satuan')->group(function () {

        Route::get('/', [SatuanController::class, 'index'])->name('satuan.index');

        Route::get('/create', [SatuanController::class, 'create'])->name('satuan.create');

        Route::post('/', [SatuanController::class, 'store'])->name('satuan.store');

        Route::get('/{code}/edit', [SatuanController::class, 'edit'])->name('satuan.edit');

        Route::put('/{code}', [SatuanController::class, 'update'])->name('satuan.update');

        Route::delete('/{code}', [SatuanController::class, 'destroy'])->name('satuan.destroy');
    });
        Route::prefix('{companyCode}/product/items')->group(function () {

        Route::get('/', [ItemController::class, 'index'])->name('item.index');

        Route::get('/create', [ItemController::class, 'create'])->name('item.create');

        Route::post('/', [ItemController::class, 'store'])->name('item.store');

        Route::get('/{id}/edit', [ItemController::class, 'edit'])->name('item.edit');

        Route::put('/{id}', [ItemController::class, 'update'])->name('item.update');

        Route::delete('/{id}', [ItemController::class, 'destroy'])->name('item.destroy');
    });

    Route::prefix('{companyCode}/supplier')->group(function () {

        Route::get('/', [SupplierController::class, 'index'])->name('supplier.index');

        Route::get('/create', [SupplierController::class, 'create'])->name('supplier.create');
        Route::post('/', [SupplierController::class, 'store'])->name('supplier.store');

        Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
        Route::put('/{id}', [SupplierController::class, 'update'])->name('supplier.update');

        Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    });
});

require __DIR__ . '/auth.php';