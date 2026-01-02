<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

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
        Log::info('=== LOGIN REQUEST MASUK ===');

        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user()->load('roles');

        Log::info('User setelah Auth:', [
            'id' => $user->id,
            'username' => $user->username,
            'roles' => $user->roles->pluck('name'),
        ]);

        $activeRole = $user->roles->first();

        if (! $activeRole) {
            throw new \Exception('User tidak memiliki role.');
        }

        $activeRole->load(['company', 'cabangResto']);

        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ],

            'role' => [
                'id' => $activeRole->id,
                'code' => $activeRole->code,
                'name' => $activeRole->name,
                'scope' => $activeRole->cabangResto ? 'BRANCH' : 'COMPANY',
                'company' => $activeRole->company ? [
                    'id' => $activeRole->company->id,
                    'codeUrl' => strtolower($activeRole->company->code),
                    'code' => $activeRole->company->code,
                    'name' => $activeRole->company->name,
                ] : null,

                'branch' => $activeRole->cabangResto ? [
                    'id' => $activeRole->cabangResto->id,
                    'code' => $activeRole->cabangResto->code,
                    'name' => $activeRole->cabangResto->name,
                ] : null,

                'permissions' => $permissions,
            ],
        ]);

        Log::info('Session setelah login:', [
            'role' => session('role'),
        ]);

        $role = session('role');
        $company = $role['company'];
        $branch = $role['branch'];

        if ($branch) {
            $url = '/'.'branch/'.strtolower($branch['code']).'/dashboard';
            Log::info('Final Redirect URL (branch):', ['redirect' => $url]);

            return redirect($url);
        }

        $url = '/'.'company/'.strtolower($company['code']).'/dashboard';
        Log::info('Final Redirect URL (company):', ['redirect' => $url]);

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
