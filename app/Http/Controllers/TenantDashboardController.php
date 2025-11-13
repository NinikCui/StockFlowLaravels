<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class TenantDashboardController extends Controller
{
    public function index($code)
    {
        $role = session('role');

        if (! $role) {
            return redirect('/login');
        }

        $branch = $role['branch'] ?? null;

        if ($branch === null) {
            return view('company.dashboard', [
                'code' => $code,
                'role' => $role,
                'company' => $role['company'],
            ]);
        }

        return view('branch.dashboard', [
            'code' => $code,
            'role' => $role,
            'branch' => $branch,
            'company' => $role['company'],
        ]);
    }
}
