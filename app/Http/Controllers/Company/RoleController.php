<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $query = Role::query()
            ->where('companies_id', $company->id);

        // === FILTER CABANG ===
        if ($request->filterCabang && $request->filterCabang !== 'all') {
            if ($request->filterCabang === 'universal') {
                $query->whereNull('cabang_resto_id');
            } else {
                $query->where('cabang_resto_id', $request->filterCabang);
            }
        }

        // === SEARCH ===
        if ($request->q) {
            $q = strtolower($request->q);
            $query->where(function ($x) use ($q) {
                $x->whereRaw('LOWER(name) LIKE ?', ["%$q%"])
                ->orWhereRaw('LOWER(code) LIKE ?', ["%$q%"]);
            });
        }

        // === SORT ===
        $sortKey = $request->sortKey ?? 'name';
        $sortDir = $request->sortDir ?? 'asc';
        $query->orderBy($sortKey, $sortDir);

        // 3. Ambil data
        $roles = $query->get();

        // 4. List cabang unik
        $cabangList = CabangResto::whereIn(
            'id',
            $roles->pluck('cabang_resto_id')->filter()
        )->get();

        return view(
            'company.roles.index',
            compact('roles', 'cabangList', 'companyCode', 'sortKey', 'sortDir')
        );
    }


    public function create($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $cabangList = $company->cabang()->get(['id', 'name', 'code']);

        return view('company.roles.create', compact('companyCode', 'cabangList'));
    }

    public function store(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $validated = $request->validate([
            'name'            => 'required|string',
            'code'            => 'required|string|max:100|alpha_dash|unique:roles,code',
            'permissions'     => 'required|array|min:1',
            'permissions.*'   => 'string',
            'cabangRestoId'   => 'nullable|integer',
            'isUniversal'     => 'nullable|boolean',
        ]);
        $isUniversal = $validated['isUniversal'] ?? false;

        $cabangRestoId = $isUniversal
            ? null
            : ($validated['cabangRestoId'] ?? null);


        $exists = Role::where('companies_id', $company->id)
            ->where('code', $validated['code'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kode role sudah digunakan.');
        }

        $role = Role::create([
            'companies_id'    => $company->id,
            'name'            => $validated['name'],
            'code'            => $validated['code'],
            'cabang_resto_id' => $cabangRestoId,
        ]);

        $uniquePerms = array_unique($validated['permissions']);

        $permissionIds = [];

        foreach ($uniquePerms as $permCode) {
            [$resource, $action] = explode('.', $permCode);

            $perm = Permission::updateOrCreate(
                [
                    'companies_id' => $company->id,
                    'code' => $permCode
                ],
                [
                    'resource' => $resource,
                    'action'   => $action,
                    'effect'   => 'ALLOW'
                ]
            );

            $permissionIds[] = $perm->id;
        }

        // === Insert role-permission ===
        foreach ($permissionIds as $pid) {
            RolePermission::create([
                'roles_id'       => $role->id,
                'permission_id'  => $pid
            ]);
        }

        return redirect()
            ->route('roles.create', $companyCode)
            ->with('success', 'Role berhasil dibuat.');
    }

    public function show($companyCode, $code)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $role = Role::with(['permissions', 'cabangResto'])
            ->where('companies_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        $groupedPermissions = collect($role->permissions)
            ->map(function ($p) {
                $parts = explode('.', $p->code);

                return [
                    'prefix'    => $parts[0] ?? 'other',
                    'resource'  => $parts[0] ?? 'other',
                    'action'    => $parts[1] ?? strtoupper($p->code),   // fallback untuk action
                    'code'      => $p->code,
                    'isGranted' => true,
                ];
            })
            ->groupBy('prefix')
            ->sortKeys()
            ->toArray();
            Log::info(  $role);
        return view('company.roles.show', [
            'companyCode' => $companyCode,
            'role' => $role,
            'permissions' => $groupedPermissions
        ]);
    }
    public function destroy($companyCode, $code)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $role = Role::where('companies_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        if (strtoupper($role->code) === 'OWNER') {
            return back()->with('error', 'Role OWNER tidak boleh dihapus.');
        }

        DB::table('role_permissions')
            ->where('roles_id', $role->id)
            ->delete();

        // 🔹 Hapus role
        $role->delete();

        return redirect()
            ->route('roles.index', $companyCode)
            ->with('success', 'Role berhasil dihapus.');
    }

    public function edit($companyCode, $code)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $role = Role::with(['permissions', 'cabangResto'])
            ->where('companies_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        // id permission yang dimiliki role
        $grantedIds = $role->permissions->pluck('id')->toArray();

        // list semua permission
        $permissions = Permission::orderBy('code')->get()->map(function ($p) use ($grantedIds) {
            return [
                'id'          => $p->id,
                'code'        => $p->code,
                'name'        => $p->name,
                'description' => $p->description,
                'isGranted'   => in_array($p->id, $grantedIds),
            ];
        });

        $cabangList = CabangResto::where('companies_id', $company->id)->get();

        return view('company.roles.edit', [
            'role'        => $role,
            'companyCode' => $companyCode,
            'permissions' => $permissions,
            'cabangList'  => $cabangList
        ]);
    }

    public function update(Request $req, $companyCode, $oldCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $role = Role::where('companies_id', $company->id)
            ->where('code', $oldCode)
            ->firstOrFail();

        $req->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'permissionIds' => 'array',
        ]);

        $role->code = strtoupper($req->code);
        $role->name = $req->name;

        if ($req->isUniversal) {
            $role->cabang_resto_id = null;
        } else {
            $role->cabang_resto_id = $req->cabangRestoId ?: null;
        }

        $role->save();

        $permIds = $req->permissionIds ?? [];

        RolePermission::where('roles_id', $role->id)->delete();

        if (!empty($permIds)) {
            $rows = collect($permIds)->map(fn($pid) => [
                'roles_id' => $role->id,
                'permission_id' => $pid,
            ]);

            RolePermission::insert($rows->toArray());
        }

        return redirect("/$companyCode/pegawai/roles/$role->code")
            ->with('status', 'Role berhasil diperbarui!');
    }

}