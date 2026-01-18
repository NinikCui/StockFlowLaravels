<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Services\BranchDashboardCacheService;

class BranchDashboardController extends Controller
{
    public function index(string $branchCode, BranchDashboardCacheService $cacheSvc)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::with('warehouses')
            ->where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $warehouseIds = $branch->warehouses->pluck('id');

        $data = $cacheSvc->getBranchDashboard($branch, $warehouseIds);

        return view('branch.dashboard', array_merge([
            'branchName' => $branch->name,
            'branchCode' => $branchCode,
        ], $data));
    }
}
