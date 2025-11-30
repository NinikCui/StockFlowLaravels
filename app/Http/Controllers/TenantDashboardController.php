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

        // Jika ROLE COMPANY
        if (empty($role['branch'])) {
            return redirect()->route('company.dashboard', [
                'companyCode' => strtolower($role['company']['code']),
            ]);
        }

        // Jika ROLE BRANCH
        return redirect()->route('branch.dashboard', [
            'branchCode' => strtolower($role['branch']['code']),
        ]);
    }
}
