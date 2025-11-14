<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\RoleController;
use App\Http\Controllers\Company\CabangController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'tenant.path'])->group(function () {

    Route::get('/{code}/dashboard', [TenantDashboardController::class, 'index'])
        ->name('dashboard');

    

    Route::get('/{code}/pegawai', function () {
        return view('company.pegawai.index');
    })->name("pegawai");


    Route::prefix('{companyCode}/pegawai')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/tambah', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');

        Route::get('/roles/{code}', [RoleController::class, 'show'])->name(name: 'roles.show');

        Route::get('/roles/{code}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{code}', [RoleController::class, 'update'])->name('roles.update');

        Route::delete('/roles/{code}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::prefix('{companyCode}/cabang')->group(function () {
        Route::get('/', [CabangController::class, 'index'])
            ->name('cabang.index');
        Route::get('/tambah', [CabangController::class, 'create'])->name('cabang.create');
        Route::post('/', [CabangController::class, 'store'])->name('cabang.store');
    });
       
});
require __DIR__.'/auth.php';
