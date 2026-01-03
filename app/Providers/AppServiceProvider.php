<?php

namespace App\Providers;

use App\Support\Access;
use App\View\Components\kpiCard;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Blade::if('canAction', function ($permission) {
            return Access::can($permission);
        });
        Paginator::defaultView('components.pagination.custom');
        Blade::component('owner.kpi-card', kpiCard::class);

        if (file_exists(base_path('routes/breadcrumbs/_load.php'))) {
            require base_path('routes/breadcrumbs/_load.php');
        }
    }
}
