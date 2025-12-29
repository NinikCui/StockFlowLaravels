<?php

namespace App\Services;

use App\Models\MenuPromotionRecommendation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MenuPromotionRecommendationService
{
    public function generateForCabang(int $cabangRestoId, int $companyId): void
    {
        $today = Carbon::today();
        MenuPromotionRecommendation::whereDate('date', $today)
            ->where('cabang_resto_id', $cabangRestoId)
            ->delete();

        $rows = DB::table('stocks as s')
            ->join('items as i', 'i.id', '=', 's.item_id')
            ->join('boms as b', 'b.item_id', '=', 'i.id')
            ->join('products as p', 'p.id', '=', 'b.product_id')
            ->join('warehouse as w', 'w.id', '=', 's.warehouse_id')
            ->where('w.cabang_resto_id', $cabangRestoId)
            ->where('i.company_id', $companyId)
            ->where('i.mudah_rusak', true)
            ->where('i.is_main_ingredient', true)
            ->whereNotNull('s.expired_at')
            ->whereRaw('DATEDIFF(s.expired_at, ?) <= 7', [$today])
            ->selectRaw('
                i.id as item_id,
                p.id as product_id,
                SUM(b.qty_per_unit * s.qty) as potential_usage,
                MIN(DATEDIFF(s.expired_at, ?)) as days_to_expired
            ', [$today])
            ->groupBy('i.id', 'p.id')
            ->get();

        foreach ($rows as $row) {
            $riskScore = $this->calculateRiskScore(
                $row->days_to_expired,
                $row->potential_usage
            );

            MenuPromotionRecommendation::create([
                'date' => $today,
                'cabang_resto_id' => $cabangRestoId,
                'item_id' => $row->item_id,
                'product_id' => $row->product_id,
                'days_to_expired' => $row->days_to_expired,
                'potential_usage' => $row->potential_usage,
                'risk_score' => $riskScore,
                'reason' => 'Bahan baku utama mendekati kadaluarsa',
                'status' => 'NEW',
            ]);
        }
    }

    private function calculateRiskScore(int $daysLeft, float $potentialUsage): float
    {
        $expiryFactor = max(0.1, (8 - $daysLeft) / 7);

        $usageFactor = log10($potentialUsage + 1);

        return round($expiryFactor * $usageFactor, 3);
    }
}
