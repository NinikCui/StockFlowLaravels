<?php

use App\Http\Controllers\Company\PegawaiController;
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

});

require __DIR__ . '/auth.php';