<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasCorrectPath
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil session setelah login
        $role = session('role');

        if (! $role) {
            return redirect('/login');
        }

        $company = $role['company'] ?? null;
        $branch  = $role['branch'] ?? null;

        if (! $company) {
            abort(403, 'Company not found in session.');
        }

        $companyCode = strtolower($company['code']);
        $branchCode  = $branch ? strtolower($branch['code']) : null;

        $path = ltrim($request->path(), '/'); // "abc/dashboard"

        // =============== CASE: USER PUNYA BRANCH ===============
        if ($branchCode) {

            if (! str_starts_with($path, $branchCode)) {
                return redirect("/{$branchCode}/dashboard");
            }

        } 
        
        // =============== CASE: USER TIDAK PUNYA BRANCH ===============
        else {

            if (! str_starts_with($path, $companyCode)) {
                return redirect("/{$companyCode}/dashboard");
            }

        }

        return $next($request);
    }
}
