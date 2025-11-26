<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    // =========================================
    // INDEX
    // =========================================
    public function index(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');
        if (! $companyId) {
            abort(403, 'Session perusahaan tidak valid');
        }

        // Semua role untuk perusahaan ini
        $roleIds = Role::where('company_id', $companyId)->pluck('id');

        // Ambil user yang punya role-role tersebut
        $pegawai = User::with(['roles', 'roles.cabangResto'])
            ->whereHas('roles', fn ($q) => $q->whereIn('id', $roleIds))
            ->orderBy('username')
            ->get()
            ->map(function ($p) {

                $r = $p->roles->first(); // satu role / user

                return [
                    'id' => $p->id,
                    'username' => $p->username,
                    'phone' => $p->phone,
                    'is_active' => $p->is_active,

                    // ROLE INFO
                    'role_code' => $r?->code ?? '-',
                    'role_name' => $r?->name ?? '-',

                    // CABANG
                    'branch_name' => $r?->cabangResto?->name ?? 'Universal',
                    'branch_code' => $r?->cabangResto?->code,
                ];
            });

        $roles = Role::where('company_id', $companyId)
            ->with('cabangResto')
            ->orderBy('code')
            ->get();

        $cabangList = CabangResto::where('company_id', $companyId)->orderBy('name')->get();

        return view('company.pegawai.index', compact(
            'companyCode',
            'pegawai',
            'roles',
            'cabangList'
        ));
    }

    // =========================================
    // HAPUS PEGAWAI
    // =========================================
    public function destroy($companyCode, $id)
    {
        $user = User::findOrFail($id);

        $user->roles()->detach();
        $user->delete();

        return redirect()
            ->route('pegawai.index', $companyCode)
            ->with('success', 'Pegawai berhasil dihapus.');
    }

    // =========================================
    // CREATE PAGE
    // =========================================
    public function create(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');

        $branches = CabangResto::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('company.pegawai.create', [
            'companyCode' => strtolower($companyCode),
            'branches' => $branches,
        ]);
    }

    // =========================================
    // STORE
    // =========================================
    public function store(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');

        $req->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {

            $result = \DB::transaction(function () use ($req, $companyId) {

                // ===============================
                // 1️⃣ Buat User
                // ===============================
                $user = User::create([
                    'username' => $req->username,
                    'email' => $req->email,
                    'phone' => $req->phone,
                    'password' => \Hash::make($req->password),
                    'is_active' => 1,
                ]);

                // ===============================
                // 2️⃣ Ambil Role yang dipilih user
                // ===============================
                $role = Role::where('company_id', $companyId)
                    ->where('id', $req->role_id)
                    ->firstOrFail();

                // ===============================
                // 3️⃣ Assign Role → HARUS PAKAI NAME !!
                // ===============================
                $user->assignRole($role->name);

                return $user;
            });

            return redirect()
                ->route('pegawai.index', strtolower($companyCode))
                ->with('success', 'Pegawai berhasil ditambahkan');

        } catch (\Exception $e) {

            return back()->withErrors([
                'global' => $e->getMessage(),
            ])->withInput();
        }
    }

    // =========================================
    // EDIT PAGE
    // =========================================
    public function edit(Request $req, $companyCode, $id)
    {
        $companyId = session('role.company.id');

        $pegawai = User::with(['roles', 'roles.cabangResto'])->findOrFail($id);
        $currentRole = $pegawai->roles->first();
        $currentRoleId = $currentRole?->id;
        $branches = CabangResto::where('company_id', $companyId)->orderBy('name')->get();

        $isUniversal = $currentRole?->cabang_resto_id === null;

        $roles = Role::where('company_id', $companyId)
            ->when($isUniversal, fn ($q) => $q->whereNull('cabang_resto_id'))
            ->when(! $isUniversal, fn ($q) => $q->where('cabang_resto_id', $currentRole->cabang_resto_id))
            ->orderBy('code')
            ->get();

        return view('company.pegawai.edit', [
            'companyCode' => $companyCode,
            'pegawai' => $pegawai,
            'roles' => $roles,
            'branches' => $branches,
            'isUniversal' => $isUniversal,
            'currentRoleId' => $currentRoleId,
        ]);
    }

    // =========================================
    // UPDATE PEGAWAI
    // =========================================
    public function update(Request $req, $companyCode, $id)
    {
        $req->validate([
            'username' => ['required', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'role_id' => 'required|exists:roles,id',
        ]);

        $pegawai = User::findOrFail($id);

        $pegawai->update([
            'username' => $req->username,
            'email' => $req->email,
            'phone' => $req->phone,
            'is_active' => $req->is_active ? 1 : 0,
        ]);

        if ($req->password) {
            $req->validate(['password' => 'confirmed|min:6']);
            $pegawai->password = bcrypt($req->password);
            $pegawai->save();
        }

        // 🔥 FIX UTAMA — syncRoles HARUS pakai model, bukan ID
        $currentRoleId = $currentRole->id ?? null;
        $currentRoleId = $currentRole->id ?? null;

        return redirect()
            ->route('pegawai.index', strtolower($companyCode))
            ->with('success', 'Pegawai berhasil diperbarui');
    }

    // Alias
    public function combined(Request $req, $companyCode)
    {
        return $this->index($req, $companyCode);
    }
}
