<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Warehouse;
use App\Services\BranchDashboardCacheService;

class BranchDashboardController extends Controller
{
    public function index($branchCode, BranchDashboardCacheService $dashboardService)
    {
        $branchId = session('role.branch.id');
        $companyId = session('role.company.id');

        $branch = CabangResto::where('id', $branchId)->firstOrFail();

        $warehouseIds = Warehouse::where('cabang_resto_id', $branchId)
            ->pluck('id');

        // ✅ AMBIL DATA DASHBOARD DARI SERVICE
        $dashboardService = new BranchDashboardCacheService;
        $dashboard = $dashboardService->getDashboardData($branch->id);

        return view('branch.dashboard', [
            'branchCode' => $branchCode,
            'branchName' => $branch->name,
            'dashboard' => $dashboard, // 🔥 INI YANG TADI KURANG
        ]);
    }
}
