<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasCorrectPath
{
    public function handle(Request $request, Closure $next)
    {
        $companyCode = session('role.company.code');
        $branchCode  = session('role.branch.code');

        // === JIKA SESSION TIDAK ADA → SKIP (BELUM LOGIN) ===
        if (!$companyCode) {
            return $next($request);
        }

        // Tentukan prefix tenancy
        $prefix = strtolower($branchCode ?? $companyCode);

        // Path asli
        $segments = $request->segments(); // contoh: ['test1', 'cabang', 'tambah']

        // === JIKA PATH KOSONG (/) → SKIP ===
        if (count($segments) === 0) {
            return $next($request);
        }

        $first = strtolower($segments[0]);

        // === EXCEPTION: JANGAN GANGGU ROUTE INI ===
        $ignoredPrefixes = [
            'login', 'logout', 'register',
            'password', 'forgot-password', 'reset-password',
            'auth', 'sanctum', 'api', 'storage',
        ];

        if (in_array($first, $ignoredPrefixes)) {
            return $next($request);
        }

        // === JIKA SUDAH BENAR PREFIX-NYA → LANJUT ===
        if ($first === $prefix) {
            return $next($request);
        }

        // === JIKA REQUEST BUKAN GET → JANGAN REDIRECT (PENTING!) ===
        // POST / PUT / PATCH / DELETE → biarkan lewat
        if (!$request->isMethod('get')) {
            return $next($request);
        }

        // === PERBAIKI PREFIX DENGAN TEPAT ===
        array_shift($segments); // buang prefix salah

        $fixed = $prefix . '/' . implode('/', $segments);

        return redirect('/' . trim($fixed, '/'));
    }
}
