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
    public function index(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');
        if (! $companyId) {
            abort(403, 'Session perusahaan tidak valid');
        }

        // ================================
        // AMBIL SEMUA ROLE PERUSAHAAN
        // ================================
        $roleIds = Role::where('company_id', $companyId)->pluck('id')->toArray();

        // ================================
        // AMBIL PEGAWAI DENGAN ROLE TERSEBUT
        // ================================
        $pegawai = User::with(['roles', 'roles.cabangResto'])
            ->whereHas('roles', function ($q) use ($roleIds) {
                $q->whereIn('id', $roleIds);
            })
            ->orderBy('username', 'asc')
            ->get()
            ->map(function ($p) {
                $role = $p->roles->first(); // ambil role aktif

                return [
                    'id' => $p->id,
                    'username' => $p->username,
                    'phone' => $p->phone,
                    'is_active' => $p->is_active,
                    'role_name' => $role?->name,
                    'role_code' => $role?->code,
                    'branch_name' => $role?->cabangResto?->name,
                    'branch_code' => $role?->cabangResto?->code,
                ];
            });

        // ================================
        // LIST SEMUA ROLE
        // ================================
        $roles = Role::with('cabangResto')
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $cabangList = CabangResto::where('company_id', $companyId)->orderBy('name')->get();

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
            return back()->with('error', 'Session tidak valid.');
        }

        $user = User::findOrFail($id);

        // Hapus role dulu (pivot)
        $user->roles()->detach();

        // Hapus user
        $user->delete();

        return redirect()
            ->route('pegawai.index', $companyCode)
            ->with('success', 'Pegawai berhasil dihapus.');
    }

    public function create(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');

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
        $companyId = session('role.company.id');

        $req->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'username' => $req->username,
            'email' => $req->email,
            'phone' => $req->phone,
            'password' => bcrypt($req->password),
            'is_active' => true,
        ]);

        // ========== SPATIE ROLE ==========
        $user->assignRole($req->role_id);

        return redirect()->route('pegawai.index', strtolower($companyCode))
            ->with('success', 'Pegawai berhasil ditambahkan');
    }

    public function edit(Request $req, $companyCode, $id)
    {
        $companyId = session('role.company.id');

        $pegawai = User::with(['roles', 'roles.cabangResto'])->findOrFail($id);
        $role = $pegawai->roles->first();

        $branches = CabangResto::where('company_id', $companyId)->get();

        $isUniversal = $role?->cabang_resto_id == null;

        $roles = Role::where('company_id', $companyId)
            ->when($isUniversal, fn ($q) => $q->whereNull('cabang_resto_id'))
            ->when(! $isUniversal, fn ($q) => $q->where('cabang_resto_id', $role->cabang_resto_id))
            ->get();

        return view('company.pegawai.edit', [
            'companyCode' => $companyCode,
            'pegawai' => $pegawai,
            'roles' => $roles,
            'branches' => $branches,
            'isUniversal' => $isUniversal,
        ]);
    }

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

        // Update password jika diisi
        if ($req->password) {
            $req->validate(['password' => 'confirmed|min:6']);
            $pegawai->password = bcrypt($req->password);
            $pegawai->save();
        }

        // ========== UPDATE ROLE (SYNC) ==========
        $pegawai->syncRoles([$req->role_id]);

        return redirect()
            ->route('pegawai.index', strtolower($companyCode))
            ->with('success', 'Pegawai berhasil diperbarui');
    }

    public function combined(Request $req, $companyCode)
    {
        return $this->index($req, $companyCode);
    }
}
