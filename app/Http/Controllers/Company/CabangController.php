<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CabangController extends Controller
{
    public function index(Request $request, $companyCode)
    {
        // Cari company berdasarkan code tenant
        $company = Company::where('code', $companyCode)->firstOrFail();

        $query = CabangResto::with([
            'manager:id,username,email',
        ])->where('company_id', $company->id);

        /** FILTER STATUS */
        if ($request->status === 'ACTIVE') {
            $query->where('is_active', true);
        } elseif ($request->status === 'INACTIVE') {
            $query->where('is_active', false);
        }

        /** SEARCH */
        if ($request->filled('search')) {
            $q = strtolower($request->search);

            $query->where(function ($x) use ($q) {
                $x->whereRaw('LOWER(name) LIKE ?', ["%$q%"])
                    ->orWhereRaw('LOWER(code) LIKE ?', ["%$q%"])
                    ->orWhereRaw('LOWER(city) LIKE ?', ["%$q%"])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ["%$q%"]);
            });
        }

        /** SORTING */
        $sort = $request->sort ?? 'created_at';
        $allowedSort = ['created_at', 'name', 'code', 'city'];
        if (! in_array($sort, $allowedSort)) {
            $sort = 'created_at';
        }

        if ($sort === 'created_at') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy($sort, 'asc');
        }

        $cabang = $query->get();

        return view('company.cabang.index', compact('cabang', 'companyCode'));
    }

    public function create($companyCode)
    {
        return view('company.cabang.create', compact('companyCode'));
    }

    public function store(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|max:100',
            'code' => [
                'required',
                'max:100',
                Rule::unique('cabang_resto', 'code')
                    ->where('company_id', $company->id),
            ],
            'city' => 'required',
            'address' => 'nullable',
            'phone' => 'nullable|max:20',
        ]);

        CabangResto::create([
            'company_id' => $company->id,
            ...$validated,
        ]);

        return redirect()
            ->route('cabang.index', $companyCode)
            ->with('success', 'Cabang berhasil ditambahkan!');
    }

    public function detail(Request $req, $companyCode, $code)
    {
        $companyId = session('role.company.id');
        if (! $companyId) {
            abort(403, 'Session perusahaan tidak valid');
        }

        $cabang = CabangResto::where('company_id', $companyId)
            ->where('code', $code)
            ->firstOrFail();

        /** AMBIL ROLE KHUSUS CABANG INI */
        $roles = Role::where('cabang_resto_id', $cabang->id)
            ->orderBy('name')
            ->get();

        /** AMBIL PEGAWAI YANG ROLE-NYA BERADA DI CABANG INI (SPATIE) */
        $pegawai = User::with(['roles'])
            ->whereHas('roles', function ($q) use ($cabang) {
                $q->where('roles.cabang_resto_id', $cabang->id);
            })
            ->orderBy('username')
            ->get()
            ->map(function ($p) {
                $role = $p->roles->first();

                return [
                    'id' => $p->id,
                    'username' => $p->username,
                    'email' => $p->email,
                    'role_name' => $role?->name,
                    'role_code' => $role?->code,
                ];
            });

        return view('company.cabang.detail', [
            'companyCode' => strtolower($companyCode),
            'cabang' => $cabang,
            'roles' => $roles,
            'pegawai' => $pegawai,
        ]);
    }

    public function edit($companyCode, $code)
    {
        $companyId = session('role.company.id');

        $cabang = CabangResto::where('company_id', $companyId)
            ->where('code', $code)
            ->firstOrFail();

        /** PEGAWAI DI CABANG INI BERDASARKAN ROLE SPATIE */
        $pegawai = User::whereHas('roles', function ($q) use ($cabang) {
            $q->where('roles.cabang_resto_id', $cabang->id);
        })
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'username' => $p->username,
                ];
            });

        return view('company.cabang.edit', [
            'companyCode' => $companyCode,
            'cabang' => $cabang,
            'pegawai' => $pegawai,
        ]);
    }

    public function update(Request $request, $companyCode, $code)
    {
        $companyId = session('role.company.id');

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'address' => 'required|string',
            'manager_user_id' => 'nullable|exists:users,id',
        ]);

        $cabang = CabangResto::where('company_id', $companyId)
            ->where('code', $code)
            ->firstOrFail();

        $cabang->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'is_active' => $request->is_active,
            'manager_user_id' => $request->manager_user_id,
        ]);

        return redirect()
            ->route('cabang.detail', [$companyCode, strtoupper($request->code)])
            ->with('success', 'Cabang berhasil diperbarui.');
    }
}
