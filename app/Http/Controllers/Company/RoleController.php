<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // ===========================
    // UTIL: BUILD PERMISSION GROUPS
    // ===========================
    protected function buildPermissionGroups()
    {
        return Permission::orderBy('name')
            ->get()
            ->map(function ($p) {

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
                    'code' => $p->name,
                    'label' => $p->name,
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

        if ($request->filterCabang && $request->filterCabang !== 'all') {

            if ($request->filterCabang === 'universal') {
                $query->whereNull('cabang_resto_id');
            } else {
                $query->where('cabang_resto_id', $request->filterCabang);
            }
        }

        if ($request->q) {
            $q = strtolower($request->q);

            $query->where(function ($x) use ($q) {
                $x->whereRaw('LOWER(code) LIKE ?', ["%$q%"]);
            });
        }

        $sortKey = $request->sortKey ?? 'code';
        $sortDir = $request->sortDir ?? 'asc';

        $roles = $query->orderBy($sortKey, $sortDir)->get();

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

        $permissions = $this->buildPermissionGroups();

        return view('company.roles.create', compact(
            'companyCode', 'cabangList', 'permissions'
        ));
    }

    // ===========================
    // STORE
    // ===========================
    public function store(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $validated = $request->validate([
            'code' => 'required|string|max:100|alpha_dash',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string',
            'cabangRestoId' => 'nullable|integer',
            'isUniversal' => 'nullable|boolean',
        ]);

        $isUniversal = $validated['isUniversal'] ?? false;
        $cabangRestoId = $isUniversal ? null : ($validated['cabangRestoId'] ?? null);

        $code = strtoupper($validated['code']);
        $autoName = $code.'_'.strtoupper($companyCode);   // <-- AUTO NAME

        $exists = Role::where('company_id', $company->id)
            ->where('code', $code)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'code' => 'Kode role sudah digunakan di perusahaan ini.',
            ])->withInput();
        }

        DB::transaction(function () use (
            $company, $autoName, $code,
            $validated, $cabangRestoId
        ) {
            $role = Role::create([
                'company_id' => $company->id,
                'cabang_resto_id' => $cabangRestoId,
                'code' => $code,
                'name' => $autoName,   // <-- AUTO NAME ONLY
                'guard_name' => 'web',
            ]);

            $permissionIds = [];

            foreach (array_unique($validated['permissions']) as $permName) {

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
        });

        return redirect()
            ->route('roles.index', $companyCode)
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

        $permissions = $role->permissions
            ->map(function ($p) {
                $parts = explode('.', $p->name);

                return [
                    'prefix' => $p->resource ?: ($parts[0] ?? 'other'),
                    'resource' => $p->resource ?: ($parts[0] ?? 'other'),
                    'action' => $p->action ?: ($parts[1] ?? 'view'),
                    'code' => $p->name,
                    'isGranted' => true,
                ];
            })
            ->groupBy('prefix')
            ->sortKeys()
            ->toArray();

        return view('company.roles.show', compact(
            'companyCode',
            'role',
            'permissions'
        ));
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

        $permissions = $this->buildPermissionGroups();
        $selected = $role->permissions->pluck('name')->toArray();

        $cabangList = CabangResto::where('company_id', $company->id)->get();

        return view('company.roles.edit', compact(
            'companyCode',
            'role',
            'permissions',
            'selected',
            'cabangList'
        ));
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

        $req->merge([
            'isUniversal' => $req->has('isUniversal') ? 1 : 0,
        ]);

        $data = $req->validate([
            'code' => [
                'required',
                'string',
                'alpha_dash',
                Rule::unique('roles', 'code')
                    ->where('company_id', $company->id)
                    ->ignore($role->id),
            ],
            'permissions' => 'array',
            'permissions.*' => 'string',
            'cabangRestoId' => 'nullable|integer',
            'isUniversal' => 'nullable|boolean',
        ]);

        $newCode = strtoupper($data['code']);
        $role->code = $newCode;
        $role->name = $newCode.'_'.strtoupper($companyCode);  // <-- AUTO NAME UPDATE

        $role->cabang_resto_id = $req->isUniversal
            ? null
            : ($req->cabangRestoId ?? null);

        $role->save();

        // update permissions
        $permissionIds = [];

        foreach ($data['permissions'] ?? [] as $permName) {

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

        return redirect()
            ->route('roles.show', [$companyCode, $role->code])
            ->with('success', 'Role berhasil diupdate.');
    }

    public function destroy($companyCode, $code)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $role = Role::where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        if ($role->code === 'OWNER') {
            return back()->with('error', 'Role OWNER tidak dapat dihapus.');
        }

        $role->syncPermissions([]);
        $role->delete();

        return redirect()
            ->route('roles.index', $companyCode)
            ->with('success', 'Role berhasil dihapus.');
    }

    // ===========================
    // JSON UNTUK PEGAWAI
    // ===========================
    public function rolesJson(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');

        if (! $companyId) {
            return response()->json(['ok' => false], 403);
        }

        $branchId = $req->query('cabangId');
        $universal = $req->query('universal');

        $query = Role::where('company_id', $companyId);

        if ($universal === 'true') {
            $query->whereNull('cabang_resto_id');
        }

        if ($branchId) {
            $query->where('cabang_resto_id', $branchId);
        }

        $roles = $query->orderBy('code')->get(['id', 'code']);

        return response()->json(['ok' => true, 'data' => $roles]);
    }
}
