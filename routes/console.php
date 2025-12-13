<?php

use App\Jobs\GenerateMenuPromotionRecommendations;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(new GenerateMenuPromotionRecommendations)
    ->dailyAt('05:00')
    ->name('generate-menu-promotion-recommendations');
