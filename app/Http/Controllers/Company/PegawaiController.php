<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function index(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');

        if (! $companyId) {
            abort(403, 'Session perusahaan tidak valid');
        }

        // ======================
        // DATA PEGAWAI
        // ======================
        $roleIds = Role::where('company_id', $companyId)->pluck('id');

        $pegawai = User::with(['role', 'role.cabangResto'])
            ->whereIn('roles_id', $roleIds)
            ->orderBy('username', 'asc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'username' => $p->username,
                    'phone' => $p->phone,
                    'is_active' => $p->is_active,
                    'role_name' => $p->role->name ?? null,
                    'role_code' => $p->role->code ?? null,
                    'branch_name' => $p->role->cabangResto->name ?? null,
                    'branch_code' => $p->role->cabangResto->code ?? null,
                ];
            });

        // ======================
        // DATA ROLES
        // ======================
        $roles = Role::where('company_id', $companyId)
            ->with('cabangResto')
            ->orderBy('name')
            ->get();

        // daftar cabang untuk filter roles
        $cabangList = CabangResto::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('company.pegawai.index', compact(
            'companyCode',
            'pegawai',
            'roles',
            'cabangList'
        ));
    }

    public function destroy($companyCode, $id)
    {
        $companyId = session('role.company.id');

        if (! $companyId) {
            return redirect()->back()
                ->with('error', 'Session tidak valid.');
        }

        $user = User::where('id', $id)
            ->first();
        $cekRoles = Role::where('company_id', $companyId)->first();

        if (! $cekRoles) {
            return redirect()->back()
                ->with('error', 'Pegawai tidak ditemukan atau tidak milik perusahaan ini.');
        }

        // Delete user
        $user->delete();

        // Redirect ke daftar pegawai
        return redirect()
            ->route('pegawai.index', $companyCode)
            ->with('success', 'Pegawai berhasil dihapus.');
    }

    public function create(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');

        // Ambil semua cabang perusahaan
        $branches = CabangResto::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('company.pegawai.create', [
            'companyCode' => strtolower($companyCode),
            'branches' => $branches,
        ]);
    }

    public function store(Request $req, $companyCode)
    {
        $req->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        User::create([
            'username' => $req->username,
            'email' => $req->email,
            'phone' => $req->phone,
            'password' => bcrypt($req->password),
            'roles_id' => $req->role_id,
            'is_active' => true,
        ]);

        return redirect()->route('pegawai.index', strtolower($companyCode))
            ->with('success', 'Pegawai berhasil ditambahkan');
    }

    public function edit(Request $req, $companyCode, $id)
    {
        $companyId = session('role.company.id');

        $pegawai = User::with(['role', 'role.cabangResto'])
            ->where('id', $id)
            ->firstOrFail();

        // Cabang yang dimiliki company ini
        $branches = CabangResto::where('company_id', $companyId)->get();

        // Role universal OR role cabang pegawai (untuk default tampilan)
        $isUniversal = $pegawai->role->cabang_resto_id == null;

        $roles = Role::where('company_id', $companyId)
            ->when($isUniversal, function ($q) {
                $q->whereNull('cabang_resto_id');
            })
            ->when(! $isUniversal, function ($q) use ($pegawai) {
                $q->where('cabang_resto_id', $pegawai->role->cabang_resto_id);
            })
            ->get();

        return view('company.pegawai.edit', [
            'companyCode' => $companyCode,
            'pegawai' => $pegawai,
            'branches' => $branches,
            'roles' => $roles,
            'isUniversal' => $isUniversal,
        ]);
    }

    public function update(Request $req, $companyCode, $id)
    {
        $req->validate([
            'username' => [
                'required',
                Rule::unique('users', 'username')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'role_id' => 'required',
        ]);

        $pegawai = User::findOrFail($id);

        $pegawai->username = $req->username;
        $pegawai->email = $req->email;
        $pegawai->phone = $req->phone;
        $pegawai->roles_id = $req->role_id;
        $pegawai->is_active = $req->is_active ? 1 : 0;

        // update password jika diisi
        if ($req->password) {
            $req->validate([
                'password' => 'confirmed|min:6',
            ]);
            $pegawai->password = bcrypt($req->password);
        }

        $pegawai->save();

        return redirect()
            ->route('pegawai.index', strtolower($companyCode))
            ->with('success', 'Pegawai berhasil diperbarui');
    }

    public function combined(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');

        if (! $companyId) {
            abort(403, 'Session perusahaan tidak valid');
        }

        $roleIds = Role::where('company_id', $companyId)->pluck('id');

        $pegawai = User::with(['role', 'role.cabangResto'])
            ->whereIn('roles_id', $roleIds)
            ->orderBy('username', 'asc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'username' => $p->username,
                    'phone' => $p->phone,
                    'is_active' => $p->is_active,
                    'role_name' => $p->role->name ?? null,
                    'role_code' => $p->role->code ?? null,
                    'branch_name' => $p->role->cabangResto->name ?? null,
                    'branch_code' => $p->role->cabangResto->code ?? null,
                ];
            });

        $roles = Role::with('cabangResto')
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $cabangList = CabangResto::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('company.pegawai.index', [
            'companyCode' => $companyCode,
            'pegawai' => $pegawai,
            'roles' => $roles,
            'cabangList' => $cabangList,
        ]);
    }
}
