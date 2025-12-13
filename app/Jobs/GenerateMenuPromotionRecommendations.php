<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\MenuPromotionRecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMenuPromotionRecommendations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(MenuPromotionRecommendationService $service): void
    {

        try {
            $companies = Company::with('cabang')->get();

            foreach ($companies as $company) {
                foreach ($company->cabang as $cabang) {
                    $service->generateForCabang(
                        $cabang->id,
                        $company->id
                    );
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Menu promo job failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // biar queue tahu ini gagal
        }
    }

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }
}
