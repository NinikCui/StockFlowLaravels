<?php

namespace App\Http\Controllers;

class TenantDashboardController extends Controller
{
    public function index($code)
    {
        $role = session('role');

        if (! $role) {
            return redirect('/login');
        }

        $branch = $role['branch'] ?? null;
        $company = $role['company'];

        // Jika user di level company
        if ($branch === null) {
            return redirect()->route('company.dashboard', [
                'companyCode' => $company['code'],
            ]);
        }

        // Jika user di level branch
        return redirect()->route('branch.dashboard', [
            'branchCode' => $branch['code'],
        ]);
    }
}
