<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasCorrectPath
{
    public function handle(Request $request, Closure $next)
    {
        $companyCode = session('role.company.code');
        $branchCode = session('role.branch.code');

        if (! $companyCode) {
            return $next($request);
        }

        $prefix = strtolower($branchCode ?? $companyCode);
        $segments = $request->segments();

        if (count($segments) === 0) {
            return $next($request);
        }

        $first = strtolower($segments[0]);

        $ignored = [
            'login', 'logout', 'register',
            'password', 'forgot-password', 'reset-password',
            'auth', 'sanctum', 'api', 'storage',
        ];

        if (in_array($first, $ignored)) {
            return $next($request);
        }

        // ===========================================
        // FIX: Jika request bukan GET → jangan redirect
        // ===========================================
        if (! $request->isMethod('get')) {
            return $next($request);
        }

        // Jika prefix sudah benar
        if ($first === $prefix) {
            return $next($request);
        }

        // Perbaiki prefix
        array_shift($segments);
        $fixed = $prefix.'/'.implode('/', $segments);

        return redirect('/'.trim($fixed, '/'));
    }
}
