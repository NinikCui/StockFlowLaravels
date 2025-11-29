<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Services\BranchDashboardCacheService;

class BranchDashboardController extends Controller
{
    public function index($branchCode, BranchDashboardCacheService $cacheSvc)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::with('warehouses')
            ->where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $warehouseIds = $branch->warehouses->pluck('id');

        // 🔥 SUPER FAST (ambil dari cache)
        $data = $cacheSvc->getBranchDashboard($branch, $warehouseIds);

        return view('branch.dashboard', array_merge([
            'branchName' => $branch->name,
            'branchCode' => $branchCode,
        ], $data));
    }
}
