<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        Log::info("=== LOGIN REQUEST MASUK ===");

        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user()->load([
            'role.company',
            'role.cabangResto'
        ]);

        Log::info("User setelah Auth:", [
            'id'       => $user->id,
            'username' => $user->username,
            'role_id'  => $user->role->id ?? null,
            'role'     => $user->role->code ?? null,
        ]);

        // Simpan ke session
        session([
            'user' => [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ],
            'role' => [
                'id'    => $user->role->id,
                'code'  => $user->role->code,
                'name'  => $user->role->name,

                'company' => $user->role->company ? [
                    'id'   => $user->role->company->id,
                    'code' => $user->role->company->code,
                    'name' => $user->role->company->name,
                ] : null,

                'branch' => $user->role->cabangResto ? [
                    'id'   => $user->role->cabangResto->id,
                    'code' => $user->role->cabangResto->code,
                    'name' => $user->role->cabangResto->name,
                ] : null,
            ],
        ]);

        Log::info("Session setelah login (role):", [
            'role' => session('role'),
        ]);

        $role    = session('role');
        $company = $role['company'];
        $branch  = $role['branch'] ?? null;

        Log::info("Redirecting user:", [
            'has_branch'   => $branch ? true : false,
            'company_code' => strtolower($company['code']),
            'branch_code'  => $branch ? strtolower($branch['code']) : null,
        ]);

        if ($branch) {
            $url = '/' . strtolower($branch['code']) . '/dashboard';
            Log::info("Final Redirect URL:", ['redirect' => $url]);
            return redirect($url);
        }

        $url = '/' . strtolower($company['code']) . '/dashboard';
        Log::info("Final Redirect URL:", ['redirect' => $url]);

        return redirect($url);
    }


    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
