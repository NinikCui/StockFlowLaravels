<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CabangController extends Controller
{
    public function index(Request $request, $companyCode)
    {
        // Cari company berdasarkan code tenant
        $company = Company::where('code', $companyCode)->firstOrFail();
        $branchCode = session('role.branch.code');
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

        return view('company.cabang.index', compact('cabang', 'companyCode', 'branchCode'));
    }

    public function create($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $cabangUtama = CabangResto::where('company_id', $company->id)
            ->where('utama', true)
            ->first();

        return view('company.cabang.create', compact(
            'companyCode',
            'cabangUtama'
        ));
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
            'utama' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($validated, $company) {

            // Jika cabang baru dijadikan cabang utama
            if (! empty($validated['utama']) && $validated['utama']) {
                CabangResto::where('company_id', $company->id)
                    ->update(['utama' => false]);
            }

            CabangResto::create([
                'company_id' => $company->id,
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'city' => $validated['city'],
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'utama' => $validated['utama'] ?? false,
            ]);
        });

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

        // ============================
        // 1. DATA CABANG
        // ============================
        $cabang = CabangResto::where('company_id', $companyId)
            ->where('code', $code)
            ->firstOrFail();

        // ============================
        // 2. ROLE KHUSUS CABANG INI
        // ============================
        $roles = Role::where('company_id', $companyId)
            ->where('cabang_resto_id', $cabang->id)
            ->orderBy('code')
            ->get();

        // ============================
        // 3. PEGAWAI YANG PUNYA ROLE DI CABANG INI
        // ============================
        $pegawai = User::with(['roles'])
            ->whereHas('roles', function ($q) use ($cabang) {
                $q->where('roles.cabang_resto_id', $cabang->id);   // 🔥 WAJIB pakai prefix roles.
            })
            ->orderBy('username')
            ->get()
            ->map(function ($p) {

                $role = $p->roles->first();

                return [
                    'id' => $p->id,
                    'username' => $p->username,
                    'email' => $p->email,
                    'role_code' => $role?->code,
                    'role_name' => $role?->name,
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

        $cabangUtama = CabangResto::where('company_id', $companyId)
            ->where('utama', true)
            ->first();

        $pegawai = User::with('roles')
            ->whereHas('roles', function ($q) use ($cabang) {
                $q->where('roles.cabang_resto_id', $cabang->id);
            })
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'username' => $p->username,
                'role_code' => $p->roles->first()?->code ?? '-',
            ]);

        return view('company.cabang.edit', compact(
            'companyCode',
            'cabang',
            'cabangUtama',
            'pegawai'
        ));
    }

    public function update(Request $request, $companyCode, $code)
    {
        $companyId = session('role.company.id');

        $cabang = CabangResto::where('company_id', $companyId)
            ->where('code', $code)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                Rule::unique('cabang_resto', 'code')
                    ->ignore($cabang->id)
                    ->where('company_id', $companyId),
            ],
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean',
            'manager_user_id' => ['nullable', Rule::exists('users', 'id')],
            'utama' => 'nullable|boolean',
        ]);

        if ($request->manager_user_id) {
            $valid = User::where('id', $request->manager_user_id)
                ->whereHas('roles', fn ($q) => $q->where('roles.cabang_resto_id', $cabang->id)
                )
                ->exists();

            if (! $valid) {
                return back()->withErrors([
                    'manager_user_id' => 'Pegawai ini tidak memiliki role pada cabang ini.',
                ]);
            }
        }

        DB::transaction(function () use ($request, $cabang, $companyId) {

            if ($request->boolean('utama')) {
                CabangResto::where('company_id', $companyId)
                    ->update(['utama' => false]);
            }

            $cabang->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'city' => $request->city,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => $cabang->utama ? true : $request->is_active,
                'manager_user_id' => $request->manager_user_id,
                'utama' => $request->boolean('utama'),
            ]);
        });

        return redirect()
            ->route('cabang.detail', [$companyCode, strtoupper($request->code)])
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy($companyCode, $code)
    {
        $companyId = session('role.company.id');

        $cabang = CabangResto::where('company_id', $companyId)
            ->where('code', $code)
            ->firstOrFail();

        // Cek apakah cabang ini punya pegawai
        $hasEmployees = User::whereHas('roles', function ($q) use ($cabang) {
            $q->where('roles.cabang_resto_id', $cabang->id);
        })->exists();

        if ($hasEmployees) {
            return redirect()
                ->route('cabang.detail', [$companyCode, $code])
                ->withErrors(['error' => 'Cabang ini tidak dapat dihapus karena masih memiliki pegawai terkait.']);
        }

        $cabang->delete();

        return redirect()
            ->route('cabang.index', $companyCode)
            ->with('success', 'Cabang berhasil dihapus.');
    }
}
