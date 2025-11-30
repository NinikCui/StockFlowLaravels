<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnsureUserHasCorrectPath
{
    public function handle(Request $request, Closure $next)
    {
        $companyCode = session('role.company.code');
        $branchCode = session('role.branch.code');

        // Jika belum login / belum ada role → skip
        if (! $companyCode) {
            return $next($request);
        }

        $segments = $request->segments();

        // Kalau tidak ada segmen (misal '/'), lanjut saja
        if (count($segments) === 0) {
            return $next($request);
        }

        // Abaikan route tertentu
        $ignored = [
            'login', 'logout', 'register',
            'password', 'forgot-password', 'reset-password',
            'auth', 'sanctum', 'api', 'storage',
        ];

        if (in_array(strtolower($segments[0]), $ignored)) {
            return $next($request);
        }

        // Dashboard redirection tidak boleh diintervensi
        if (
            Str::contains($request->path(), 'dashboard/company') ||
            Str::contains($request->path(), 'dashboard/branch')
        ) {
            return $next($request);
        }

        // Request non-GET tidak boleh di-redirect
        if (! $request->isMethod('get')) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Tentukan prefix yang benar
        |--------------------------------------------------------------------------
        | Company  -> "company/{companyCode}"
        | Branch   -> "branch/{branchCode}"
        |--------------------------------------------------------------------------
        */
        $expectedPrefix = $branchCode
            ? 'branch/'.strtolower($branchCode)
            : 'company/'.strtolower($companyCode);

        /*
        |--------------------------------------------------------------------------
        | Ambil prefix dari URL sekarang: "segment1/segment2"
        |--------------------------------------------------------------------------
        */
        $currentPrefix = strtolower(
            ($segments[0] ?? '').'/'.($segments[1] ?? '')
        );

        // Jika prefix sudah benar → lanjut
        if ($currentPrefix === $expectedPrefix) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Jika prefix salah → perbaiki
        |--------------------------------------------------------------------------
        */
        // Buang 2 segmen prefix lama (company/xxxx atau branch/yyyy)
        array_shift($segments);
        array_shift($segments);

        $fixed = $expectedPrefix.'/'.implode('/', $segments);
        $fixed = rtrim($fixed, '/');

        return redirect('/'.$fixed);
    }
}
