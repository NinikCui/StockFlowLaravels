<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\MenuPromotionRecommendation;
use App\Services\MenuPromotionRecommendationService;

class MenuPromotionController extends Controller
{
    public function generate($branchCode)
    {
        $companyId = session('role.company.id');

        $cabang = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        app(MenuPromotionRecommendationService::class)
            ->generateForCabang($cabang->id, $companyId);

        return back()->with('success', 'Rekomendasi menu berhasil diperbarui.');
    }

    public function index($branchCode)
    {
        $companyId = session('role.company.id');

        $cabang = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $recommendations = MenuPromotionRecommendation::with(['item', 'product'])
            ->where('cabang_resto_id', $cabang->id)
            ->whereDate('date', today())
            ->orderByDesc('risk_score')
            ->get();

        return view('branch.menu-promotion.index', compact(
            'cabang',
            'recommendations',
            'branchCode'
        ));
    }
}
