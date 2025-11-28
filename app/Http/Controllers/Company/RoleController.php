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
    // =======================================
    // BUILD PERMISSION GROUPS
    // =======================================
    protected function buildPermissionGroups()
    {
        return Permission::orderBy('name')
            ->get()
            ->map(function ($p) {

                $resource = $p->resource;
                $action = $p->action;

                // Bila belum ada di DB → fallback dari name
                if (! $resource || ! $action) {
                    $parts = explode('.', $p->name);

                    $resource = $resource ?: strtolower($parts[0] ?? 'other');
                    $action = $action ?: strtolower($parts[1] ?? 'view');
                }

                // Normalisasi
                $resource = strtolower(trim($resource));
                $action = strtolower(trim($action));

                $label = ucfirst(str_replace('_', ' ', $action));

                return [
                    'id' => $p->id,
                    'code' => $p->name,
                    'resource' => $resource,
                    'action' => $action,
                    'label' => $label,
                ];
            })

            ->groupBy('resource')

            ->toArray();
    }

    // =======================================
    // INDEX
    // =======================================
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
            $query->whereRaw('LOWER(code) LIKE ?', ["%$q%"]);
        }

        $roles = $query
            ->orderBy($request->sortKey ?? 'code', $request->sortDir ?? 'asc')
            ->get();

        $cabangList = CabangResto::whereIn(
            'id',
            $roles->pluck('cabang_resto_id')->filter()
        )->get();

        return view('company.roles.index', compact(
            'roles', 'cabangList', 'companyCode'
        ));
    }

    // =======================================
    // CREATE
    // =======================================
    public function create($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $cabangList = $company->cabang()->get(['id', 'name', 'code']);
        $permissions = $this->buildPermissionGroups();

        return view('company.roles.create', compact(
            'companyCode', 'cabangList', 'permissions'
        ));
    }

    // =======================================
    // STORE
    // =======================================
    public function store(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $validated = $request->validate([
            'code' => 'required|string|max:100|alpha_dash',
            'permissions' => 'required|array|min:1',

            // FIXED: hindari input kosong
            'permissions.*' => 'string|min:1',

            'cabangRestoId' => 'nullable|integer',
            'isUniversal' => 'nullable|boolean',
        ]);

        $isUniversal = $validated['isUniversal'] ?? false;
        $cabangRestoId = $isUniversal ? null : ($validated['cabangRestoId'] ?? null);

        $code = strtoupper($validated['code']);
        $autoName = $code.'_'.strtoupper($companyCode);

        // Cek duplikat role
        if (Role::where('company_id', $company->id)->where('code', $code)->exists()) {
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
                'name' => $autoName,
                'guard_name' => 'web',
            ]);

            $permissionIds = [];

            // FIXED: Filter input kosong
            $permissionNames = array_filter(
                array_unique($validated['permissions']),
                fn ($p) => trim($p) !== ''
            );

            foreach ($permissionNames as $permName) {

                [$resource, $action] = array_pad(explode('.', $permName), 2, null);

                $perm = Permission::firstOrCreate(
                    [
                        'name' => $permName,
                        'guard_name' => 'web',
                    ],
                    [
                        'name' => $permName,
                        'guard_name' => 'web',
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

    // =======================================
    // SHOW
    // =======================================
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

    // =======================================
    // EDIT
    // =======================================
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

    // =======================================
    // UPDATE
    // =======================================
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
            'permissions.*' => 'string|min:1',   // FIXED
            'cabangRestoId' => 'nullable|integer',
            'isUniversal' => 'nullable|boolean',
        ]);

        $role->code = strtoupper($data['code']);
        $role->name = $role->code.'_'.strtoupper($companyCode);
        $role->cabang_resto_id = $req->isUniversal ? null : ($req->cabangRestoId ?? null);
        $role->save();

        // FIXED: filter yang kosong
        $permissionNames = array_filter($data['permissions'] ?? [], fn ($p) => trim($p) !== '');

        $permissionIds = [];

        foreach ($permissionNames as $permName) {

            [$resource, $action] = array_pad(explode('.', $permName), 2, null);

            $perm = Permission::firstOrCreate(
                [
                    'name' => $permName,
                    'guard_name' => 'web',
                ],
                [
                    'name' => $permName,
                    'guard_name' => 'web',
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

    // =======================================
    // DELETE
    // =======================================
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

    // =======================================
    // JSON UNTUK PEGAWAI
    // =======================================
    public function rolesJson(Request $req, $companyCode)
    {
        $companyId = session('role.company.id');

        if (! $companyId) {
            return response()->json(['ok' => false], 403);
        }

        $query = Role::where('company_id', $companyId);

        if ($req->query('universal') === 'true') {
            $query->whereNull('cabang_resto_id');
        }

        if ($req->query('cabangId')) {
            $query->where('cabang_resto_id', $req->query('cabangId'));
        }

        return response()->json([
            'ok' => true,
            'data' => $query->orderBy('code')->get(['id', 'code']),
        ]);
    }
}
