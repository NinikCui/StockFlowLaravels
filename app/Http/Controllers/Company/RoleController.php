<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // ===========================
    //  UTIL: BUILD PERMISSION GROUPS
    // ===========================
    protected function buildPermissionGroups()
    {
        return Permission::orderBy('name')
            ->get()
            ->map(function ($p) {
                // Fallback resource & action kalau masih NULL di DB
                $resource = $p->resource;
                $action = $p->action;

                if (! $resource || ! $action) {
                    $parts = explode('.', $p->name);
                    $resource = $resource ?: ($parts[0] ?? 'other');
                    $action = $action ?: ($parts[1] ?? 'view');
                }

                return [
                    'id' => $p->id,
                    'resource' => $resource,
                    'action' => $action,
                    'code' => $p->name,       // dipakai di form
                    'label' => $p->name,       // bisa ganti jadi $p->description kalau ada
                ];
            })
            ->groupBy('resource')
            ->toArray();
    }

    // ===========================
    // INDEX
    // ===========================
    public function index(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $query = Role::where('company_id', $company->id);

        // FILTER CABANG
        if ($request->filterCabang && $request->filterCabang !== 'all') {
            if ($request->filterCabang === 'universal') {
                $query->whereNull('cabang_resto_id');
            } else {
                $query->where('cabang_resto_id', $request->filterCabang);
            }
        }

        // SEARCH
        if ($request->q) {
            $q = strtolower($request->q);
            $query->where(function ($x) use ($q) {
                $x->whereRaw('LOWER(name) LIKE ?', ["%$q%"])
                    ->orWhereRaw('LOWER(code) LIKE ?', ["%$q%"]);
            });
        }

        // SORT
        $sortKey = $request->sortKey ?? 'name';
        $sortDir = $request->sortDir ?? 'asc';

        $roles = $query->orderBy($sortKey, $sortDir)->get();

        // List cabang unik
        $cabangList = CabangResto::whereIn(
            'id',
            $roles->pluck('cabang_resto_id')->filter()
        )->get();

        return view('company.roles.index', compact(
            'roles', 'cabangList', 'companyCode', 'sortKey', 'sortDir'
        ));
    }

    // ===========================
    // CREATE
    // ===========================
    public function create($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $cabangList = $company->cabang()->get(['id', 'name', 'code']);

        // permission global (dipakai semua company)
        $permissions = $this->buildPermissionGroups();

        return view('company.roles.create', compact(
            'companyCode',
            'cabangList',
            'permissions'
        ));
    }

    // ===========================
    // STORE
    // ===========================
    public function store(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|max:100|alpha_dash',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string',
            'cabangRestoId' => 'nullable|integer',
            'isUniversal' => 'nullable|boolean',
        ]);

        $isUniversal = $validated['isUniversal'] ?? false;
        $cabangRestoId = $isUniversal ? null : ($validated['cabangRestoId'] ?? null);

        // pastikan code unik per company
        $exists = Role::where('company_id', $company->id)
            ->where('code', strtoupper($validated['code']))
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'code' => 'Kode role sudah digunakan di perusahaan ini.',
            ]);
        }

        DB::transaction(function () use ($company, $validated, $cabangRestoId) {

            // 1. Buat ROLE (Spatie)
            $role = Role::create([
                'company_id' => $company->id,
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'guard_name' => 'web',
                'cabang_resto_id' => $cabangRestoId,
            ]);

            // 2. Siapkan permission (GLOBAL)
            $uniqueCodes = array_unique($validated['permissions']);
            $permissionIds = [];

            foreach ($uniqueCodes as $permName) {
                [$resource, $action] = array_pad(explode('.', $permName), 2, null);

                $perm = Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web'],
                    [
                        'code' => $permName,
                        'resource' => $resource ?: 'other',
                        'action' => $action ?: 'view',
                        'scope' => 'COMPANY', // atau GLOBAL sesuai konsep kamu
                    ]
                );

                $permissionIds[] = $perm->id;
            }

            // 3. Assign memakai pivot Spatie
            $role->syncPermissions($permissionIds);
        });

        return redirect()
            ->route('roles.create', $companyCode)
            ->with('success', 'Role berhasil dibuat.');
    }

    // ===========================
    // SHOW
    // ===========================
    public function show($companyCode, $code)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $role = Role::with(['permissions', 'cabangResto'])
            ->where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        $groupedPermissions = $role->permissions
            ->map(function ($p) {
                $parts = explode('.', $p->name);
                $resource = $p->resource ?: ($parts[0] ?? 'other');
                $action = $p->action ?: ($parts[1] ?? $p->name);

                return [
                    'prefix' => $resource,
                    'resource' => $resource,
                    'action' => $action,
                    'code' => $p->name,
                    'isGranted' => true,
                ];
            })
            ->groupBy('prefix')
            ->sortKeys()
            ->toArray();

        return view('company.roles.show', [
            'companyCode' => $companyCode,
            'role' => $role,
            'permissions' => $groupedPermissions,
        ]);
    }

    // ===========================
    // EDIT
    // ===========================
    public function edit($companyCode, $code)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $role = Role::with(['permissions', 'cabangResto'])
            ->where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        $selectedCodes = $role->permissions->pluck('name')->toArray();

        // semua permission global untuk builder
        $permissions = $this->buildPermissionGroups();

        $cabangList = CabangResto::where('company_id', $company->id)->get();

        return view('company.roles.edit', [
            'companyCode' => $companyCode,
            'role' => $role,
            'permissions' => $permissions,
            'selectedCodes' => $selectedCodes,
            'cabangList' => $cabangList,
        ]);
    }

    // ===========================
    // UPDATE
    // ===========================
    public function update(Request $req, $companyCode, $oldCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $role = Role::where('company_id', $company->id)
            ->where('code', $oldCode)
            ->firstOrFail();

        $data = $req->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'permissionIds' => 'array',
            'permissions' => 'array',
            'cabangRestoId' => 'nullable|integer',
            'isUniversal' => 'nullable|boolean',
        ]);

        $role->code = strtoupper($data['code']);
        $role->name = $data['name'];

        if ($req->isUniversal) {
            $role->cabang_resto_id = null;
        } else {
            $role->cabang_resto_id = $req->cabangRestoId ?: null;
        }

        $role->save();

        // ====== UPDATE PERMISSIONS (kode dari form) ======
        $codes = $data['permissions'] ?? [];

        $permissionIds = [];
        foreach ($codes as $permName) {
            [$resource, $action] = array_pad(explode('.', $permName), 2, null);

            $perm = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web'],
                [
                    'code' => $permName,
                    'resource' => $resource ?: 'other',
                    'action' => $action ?: 'view',
                    'scope' => 'COMPANY',
                ]
            );

            $permissionIds[] = $perm->id;
        }

        $role->syncPermissions($permissionIds);

        return redirect("/$companyCode/pegawai/roles/$role->code")
            ->with('status', 'Role berhasil diperbarui!');
    }

    // ===========================
    // DESTROY
    // ===========================
    public function destroy($companyCode, $code)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $role = Role::where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        if (strtoupper($role->code) === 'OWNER') {
            return back()->with('error', 'Role OWNER tidak boleh dihapus.');
        }

        // detach permission Spatie
        $role->syncPermissions([]);
        $role->delete();

        return redirect()
            ->route('roles.index', $companyCode)
            ->with('success', 'Role berhasil dihapus.');
    }
}
