<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
        // ============================================
        // 1️⃣ VALIDASI FORM
        // ============================================
        $data = $request->validate([
            'companyName' => 'required|string|max:255',
            'companyCode' => 'nullable|string|max:20',
            'username'    => 'required|string|max:50|unique:users,username',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'required|string|max:20',
            'password'    => 'required|min:6|confirmed',
        ]);

        try {
            // ============================================
            // Jalankan semua langkah dalam transaksi
            // jika gagal → rollback otomatis
            // ============================================
            $result = DB::transaction(function () use ($data) {

                // =================================================
                // 2️⃣ Generate Company Code (mirip toCompanyCode)
                // =================================================
                if (!empty($data['companyCode'])) {
                    $companyCode = $this->toCompanyCode($data['companyCode']);
                } else {
                    $companyCode = $this->toCompanyCode($data['companyName']);
                }

                // Cek company code sudah ada?
                if (Company::where('code', $companyCode)->exists()) {
                    throw new \Exception("Kode perusahaan sudah dipakai.");
                }

                // =================================================
                // 3️⃣ INSERT COMPANY
                // =================================================
                $company = Company::create([
                    'name' => $data['companyName'],
                    'code' => $companyCode,
                ]);

               

                // =================================================
                // 5️⃣ INSERT ROLE OWNER
                // =================================================
                $role = Role::create([
                    'companies_id' => $company->id,
                    'code' => 'OWNER',
                    'name' => 'Owner',
                    'is_universal' => false,
                ]);

                // =================================================
                // 6️⃣ INSERT USER ADMIN/OWNER
                // =================================================
                $user = User::create([
                    'username' => $data['username'],
                    'email'    => $data['email'],
                    'phone'    => $data['phone'],
                    'password' => Hash::make($data['password']),
                    'roles_id' => $role->id,
                    'is_active' => true,
                    'companies_id' => $company->id,
                ]);

                return [
                    'company' => $company,
                    'role'    => $role,
                    'user'    => $user,
                ];
            });

            Auth::login($result['user']);

            return redirect('/dashboard')
                ->with('status', 'Registrasi perusahaan berhasil!');

        } catch (\Exception $err) {
            return back()->withErrors([
                'global' => $err->getMessage(),
            ])->withInput();
        }
    }

    private function toCompanyCode(string $s): string
    {
        $clean = preg_replace('/[^A-Z0-9]/', '', strtoupper(trim($s)));
        $code = substr($clean, 0, 12);
        return $code ?: 'COMP';
    }
}
