<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyDashboardService;

class CompanyDashboardController extends Controller
{
    public function index($companyCode)
    {
        $companyId = session('role.company.id');

        abort_unless($companyId, 403);

        $company = Company::findOrFail($companyId);

        $dashboardService = new CompanyDashboardService($companyId);

        $dashboard = $dashboardService->getDashboardData();

        return view('company.dashboard', [
            'companyCode' => $companyCode,
            'companyName' => $company->name,
            'dashboard' => $dashboard,
        ]);
    }
}
