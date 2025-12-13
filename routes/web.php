<?php

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

});

require __DIR__.'/company.php';
require __DIR__.'/branch.php';
require __DIR__.'/auth.php';
