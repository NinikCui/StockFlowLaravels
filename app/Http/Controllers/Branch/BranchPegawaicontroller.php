<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchPegawaiController extends Controller
{
    public function index($branchCode)
    {
        $companyId = session('role.company.id');

        $companyCode = session('role.company.code');        // 1️⃣ Validasi cabang milik company
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // 2️⃣ Ambil role-role khusus cabang ini
        $roleIds = Role::where('company_id', $companyId)
            ->where('cabang_resto_id', $branch->id)
            ->pluck('id');

        // 3️⃣ Ambil user yang memiliki role di cabang ini
        $employees = User::with('roles')
            ->whereHas('roles', fn ($q) => $q->whereIn('id', $roleIds))
            ->orderBy('username')
            ->get()
            ->map(function ($u) use ($roleIds) {

                // Ambil role yang memang milik cabang
                $role = $u->roles->whereIn('id', $roleIds)->first();

                // Tambahkan atribut dinamis
                $u->role_code = $role?->code ?? '-';
                $u->role_name = $role?->name ?? '-';
                $u->is_active = $u->is_active ? true : false;

                return $u;
            });

        return view('branch.pegawai.index', [
            'companyCode' => $companyCode,
            'branchCode' => $branchCode,
            'employees' => $employees,
        ]);
    }

    public function create($branchCode)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Role cabang saja
        $roles = Role::where('company_id', $companyId)
            ->where('cabang_resto_id', $branch->id)
            ->orderBy('code')
            ->get();

        return view('branch.pegawai.create', compact(
            'branchCode',
            'roles'
        ));
    }

    public function store(Request $req, $branchCode)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $req->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'password' => 'required|min:6|confirmed',
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->where('cabang_resto_id', $branch->id),
            ],
        ]);

        $user = User::create([
            'username' => $req->username,
            'email' => $req->email,
            'phone' => $req->phone,
            'password' => bcrypt($req->password),
            'is_active' => 1,
        ]);

        // Assign role cabang
        $role = Role::find($req->role_id);
        $user->assignRole($role->name);

        return redirect()
            ->route('branch.pegawai.index', $branchCode)
            ->with('success', 'Pegawai cabang berhasil ditambahkan');
    }

    public function edit($branchCode, $id)
    {
        $companyId = session('role.company.id');

        // Validasi cabang
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Pegawai cabang: filter berdasarkan role cabang
        $pegawai = User::with('roles')
            ->whereHas('roles', fn ($q) => $q->where('roles.cabang_resto_id', $branch->id)
            )
            ->findOrFail($id);

        $currentRole = $pegawai->roles->first();

        // Roles khusus cabang ini
        $roles = Role::where('company_id', $companyId)
            ->where('cabang_resto_id', $branch->id)
            ->orderBy('code')
            ->get();

        return view('branch.pegawai.edit', compact(
            'branchCode',
            'pegawai',
            'roles',
            'currentRole'
        ));
    }

    public function update(Request $req, $branchCode, $id)
    {
        $companyId = session('role.company.id');

        $pegawai = User::findOrFail($id);

        $req->validate([
            'username' => ['required', Rule::unique('users')->ignore($pegawai->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($pegawai->id)],
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|in:0,1',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Update basic info
        $pegawai->update([
            'name' => $req->name,
            'username' => $req->username,
            'email' => $req->email,
            'phone' => $req->phone,
            'is_active' => $req->is_active,
        ]);

        // Update password jika ada input
        if (! empty($req->password)) {
            $req->validate([
                'password' => 'confirmed|min:6',
            ]);

            $pegawai->password = bcrypt($req->password);
            $pegawai->save();
        }

        // Sync Role
        $role = Role::findOrFail($req->role_id);
        $pegawai->syncRoles([$role]);

        return redirect()
            ->route('branch.pegawai.index', $branchCode)
            ->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function destroy($branchCode, $id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();

        return back()->with('success', 'Pegawai cabang berhasil dihapus');
    }
}
