<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'tenant.path'])->group(function () {

    Route::get('/{code}/dashboard', [TenantDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/{code}/cabang', function () {
        return view('company.cabang.index');
    })->name("cabang.index");

    Route::get('/{code}/pegawai', function () {
        return view('company.pegawai.index');
    })->name("pegawai");

    Route::get('/{code}/pegawai/roles', function () {
        return view('company.pegawai.roles');
    })->name("pegawai.roles");
});
require __DIR__.'/auth.php';
