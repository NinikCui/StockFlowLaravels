<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BranchRoleController extends Controller
{
    protected function buildPermissionGroups()
    {
        return Permission::orderBy('name')
            ->get()
            ->map(function ($p) {

                $resource = $p->resource;
                $action = $p->action;

                if (! $resource || ! $action) {
                    $parts = explode('.', $p->name);

                    $resource = $resource ?: strtolower($parts[0] ?? 'other');
                    $action = $action ?: strtolower($parts[1] ?? 'view');
                }

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

    public function index(Request $request, $branchCode)
    {
        $companyCode = session('role.company.code');
        $company = Company::where('code', $companyCode)->firstOrFail();

        $branch = CabangResto::where('company_id', $company->id)
            ->where('code', $branchCode)
            ->firstOrFail();

        $query = Role::where('company_id', $company->id)
            ->where('cabang_resto_id', $branch->id);

        if ($request->q) {
            $search = strtolower($request->q);
            $query->whereRaw('LOWER(code) LIKE ?', ["%{$search}%"]);
        }

        $sortKey = $request->sortKey ?? 'code';
        $sortDir = $request->sortDir ?? 'asc';

        $roles = $query->orderBy($sortKey, $sortDir)->get();

        $cabangList = CabangResto::where('company_id', $company->id)->get();

        return view('branch.roles.index', [
            'companyCode' => $companyCode,
            'branchCode' => $branchCode,
            'roles' => $roles,
            'branch' => $branch,
            'cabangList' => $cabangList,
        ]);
    }

    public function create($branchCode)
    {
        $companyCode = session('role.company.code');

        $company = Company::where('code', $companyCode)->firstOrFail();

        $branch = CabangResto::where('company_id', $company->id)
            ->where('code', $branchCode)
            ->firstOrFail();

        $permissions = $this->buildPermissionGroups();

        return view('branch.roles.create', compact(
            'companyCode',
            'branchCode',
            'branch',
            'permissions'
        ));
    }

    public function store(Request $request, $branchCode)
    {
        $companyCode = session('role.company.code');

        $company = Company::where('code', $companyCode)->firstOrFail();

        $branch = CabangResto::where('company_id', $company->id)
            ->where('code', $branchCode)
            ->firstOrFail();

        $validated = $request->validate([
            'code' => 'required|string|max:100|alpha_dash',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|min:1',
        ]);

        $code = strtoupper($validated['code']);
        $autoName = $code.'_'.strtoupper($branchCode);

        // Duplikat role di branch ini?
        if (Role::where('company_id', $company->id)
            ->where('cabang_resto_id', $branch->id)
            ->where('code', $code)
            ->exists()) {
            return back()->withErrors([
                'code' => 'Kode role sudah digunakan di cabang ini.',
            ])->withInput();
        }

        DB::transaction(function () use (
            $company, $branch, $autoName, $code, $validated
        ) {
            $role = Role::create([
                'company_id' => $company->id,
                'cabang_resto_id' => $branch->id,
                'code' => $code,
                'name' => $autoName,
                'guard_name' => 'web',
            ]);

            $permissionNames = array_filter(
                array_unique($validated['permissions']),
                fn ($p) => trim($p) !== ''
            );

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
        });

        return redirect()
            ->route('branch.roles.index', [$companyCode, $branchCode])
            ->with('success', 'Role berhasil dibuat.');
    }

    public function show($branchCode, $code)
    {
        $companyCode = session('role.company.code');

        $company = Company::where('code', $companyCode)->firstOrFail();
        $branch = CabangResto::where('company_id', $company->id)
            ->where('code', $branchCode)
            ->firstOrFail();

        $role = Role::with('permissions')
            ->where('company_id', $company->id)
            ->where('cabang_resto_id', $branch->id)
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

        return view('branch.roles.show', compact(
            'companyCode',
            'branchCode',
            'role',
            'permissions',
            'branch'
        ));
    }

    public function edit($branchCode, $code)
    {
        $companyCode = session('role.company.code');

        $company = Company::where('code', $companyCode)->firstOrFail();
        $branch = CabangResto::where('company_id', $company->id)
            ->where('code', $branchCode)
            ->firstOrFail();

        $role = Role::with('permissions')
            ->where('company_id', $company->id)
            ->where('cabang_resto_id', $branch->id)
            ->where('code', $code)
            ->firstOrFail();

        $permissions = $this->buildPermissionGroups();
        $selected = $role->permissions->pluck('name')->toArray();

        return view('branch.roles.edit', compact(
            'companyCode',
            'branchCode',
            'branch',
            'role',
            'permissions',
            'selected'
        ));
    }

    public function update(Request $req, $branchCode, $oldCode)
    {
        $companyCode = session('role.company.code');

        $company = Company::where('code', $companyCode)->firstOrFail();
        $branch = CabangResto::where('company_id', $company->id)
            ->where('code', $branchCode)
            ->firstOrFail();

        $role = Role::where('company_id', $company->id)
            ->where('cabang_resto_id', $branch->id)
            ->where('code', $oldCode)
            ->firstOrFail();

        $data = $req->validate([
            'code' => [
                'required',
                'string',
                'alpha_dash',
                Rule::unique('roles', 'code')
                    ->where('company_id', $company->id)
                    ->where('cabang_resto_id', $branch->id)
                    ->ignore($role->id),
            ],
            'permissions' => 'array',
            'permissions.*' => 'string|min:1',
        ]);

        $role->code = strtoupper($data['code']);
        $role->name = $role->code.'_'.strtoupper($branchCode);
        $role->save();

        $permissionNames = array_filter(
            $data['permissions'] ?? [],
            fn ($p) => trim($p) !== ''
        );

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
            ->route('branch.roles.show', [$branchCode, $role->code])
            ->with('success', 'Role berhasil diupdate.');
    }

    public function destroy($branchCode, $code)
    {
        $companyCode = session('role.company.code');

        $company = Company::where('code', $companyCode)->firstOrFail();
        $branch = CabangResto::where('company_id', $company->id)
            ->where('code', $branchCode)
            ->firstOrFail();

        $role = Role::where('company_id', $company->id)
            ->where('cabang_resto_id', $branch->id)
            ->where('code', $code)
            ->firstOrFail();

        if ($role->code === 'OWNER') {
            return back()->with('error', 'Role OWNER tidak dapat dihapus.');
        }

        $role->syncPermissions([]);
        $role->delete();

        return redirect()
            ->route('branch.roles.index', [$companyCode, $branchCode])
            ->with('success', 'Role berhasil dihapus.');
    }
}
