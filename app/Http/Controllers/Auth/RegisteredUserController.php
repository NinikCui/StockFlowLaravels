<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'companyName' => 'required|string|max:255',
            'companyCode' => 'nullable|string|max:20',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        try {

            $result = DB::transaction(function () use ($data) {

                $companyCode = $this->toCompanyCode(
                    $data['companyCode'] ?: $data['companyName']
                );

                if (Company::where('code', $companyCode)->exists()) {
                    throw new \Exception('Kode perusahaan sudah dipakai.');
                }

                $company = Company::create([
                    'name' => $data['companyName'],
                    'code' => $companyCode,
                ]);

                $user = User::create([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($data['password']),
                    'is_active' => true,
                ]);

                $role = Role::create([
                    'company_id' => $company->id,
                    'cabang_resto_id' => null,
                    'code' => 'OWNER',
                    'name' => 'OWNER_'.strtoupper($companyCode),
                    'guard_name' => 'web',
                ]);
                $role->syncPermissions(Permission::all());

                $user->assignRole($role->name);

                return [
                    'company' => $company,
                    'role' => $role,
                    'user' => $user,
                ];
            });

            return redirect('/login')
                ->with('status', 'Registrasi perusahaan berhasil!');

        } catch (\Exception $err) {
            return back()->withErrors([
                'global' => $err->getMessage(),
            ])->withInput();
        }
    }

    /**
     * Convert to Company Code
     * Example: "Resto Maju Jaya" => "RESTO-MAJU-JAYA"
     */
    private function toCompanyCode($name)
    {
        return strtoupper(
            preg_replace('/[^A-Za-z0-9]/', '-', $name)
        );
    }
}
