<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class WarehouseController extends Controller
{
   public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $cabangs = CabangResto::where('company_id', $company->id)->get();

        $warehouses = Warehouse::with('type', 'cabangResto')
            ->whereIn('cabang_resto_id', $cabangs->pluck('id'))
            ->orderBy('id', 'desc')
            ->get();

        $types = WarehouseType::where('company_id', $company->id)->get();

        return view('company.warehouse.index', [
            'companyCode' => $companyCode,
            'warehouses'  => $warehouses,
            'types'       => $types,
            'cabangs'     => $cabangs,
        ]);
    }

    public function create($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $cabangs = CabangResto::where('company_id', $company->id)->get();
        $types = WarehouseType::where('company_id', $company->id)->get();   

        return view('company.warehouse.create', compact('companyCode', 'cabangs', 'types'));
    }

    public function store(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $data = $request->validate([
            'cabang_resto_id'    => 'required|exists:cabang_resto,id',
            'name'               => 'required|string|max:45',
            'code'               => 'nullable|string|max:45',
            'warehouse_type_id'  => 'nullable|exists:warehouse_types,id',
        ]);

        $cabangValid = CabangResto::where('id', $data['cabang_resto_id'])
            ->where('company_id', $company->id)
            ->exists();

        if (!$cabangValid) {
            abort(403, 'Cabang tidak valid untuk perusahaan ini.');
        }

        // Validasi type juga harus milik company
        if (!empty($data['warehouse_type_id'])) {
            $typeValid = WarehouseType::where('id', $data['warehouse_type_id'])
                ->where('company_id', $company->id)   // <── PENTING, FIX
                ->exists();

            if (!$typeValid) {
                abort(403, 'Tipe warehouse tidak valid untuk perusahaan ini.');
            }
        }

        Warehouse::create([
            'cabang_resto_id'    => $data['cabang_resto_id'],
            'name'               => $data['name'],
            'code'               => strtoupper($data['code'] ?? "WH-" . Str::random(5)),
            'warehouse_type_id'  => $data['warehouse_type_id'] ?? null,
        ]);

        return redirect()->route('warehouse.index', $companyCode)
            ->with('success', 'Warehouse berhasil ditambahkan');
    }
    public function edit($companyCode, $id)
    {
        // Ambil company
        $company = Company::where('code', $companyCode)->firstOrFail();

        // Ambil warehouse
        $warehouse = Warehouse::findOrFail($id);

        // Pastikan warehouse berasal dari cabang yang milik company ini
        $isValidWarehouse = CabangResto::where('id', $warehouse->cabang_resto_id)
            ->where('company_id', $company->id)
            ->exists();

        if (!$isValidWarehouse) {
            abort(403, 'Warehouse tidak valid untuk perusahaan ini.');
        }

        // Ambil semua cabang milik company
        $cabangs = CabangResto::where('company_id', $company->id)->get();

        // Ambil semua tipe warehouse milik company
        $types = WarehouseType::where('company_id', $company->id)->get();

        return view('company.warehouse.edit', compact(
            'warehouse', 'companyCode', 'cabangs', 'types'
        ));
    }
    public function update(Request $request, $companyCode, $id)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $warehouse = Warehouse::findOrFail($id);

        // Validasi input
        $data = $request->validate([
            'cabang_resto_id'    => 'required|exists:cabang_resto,id',
            'name'               => 'required|string|max:45',
            'code'               => 'nullable|string|max:45',
            'warehouse_type_id'  => 'nullable|exists:warehouse_types,id',
        ]);

        // Validasi cabang yang dipilih berasal dari company yang benar
        $validCabang = CabangResto::where('id', $data['cabang_resto_id'])
            ->where('company_id', $company->id)
            ->exists();

        if (!$validCabang) {
            abort(403, 'Cabang tidak valid untuk perusahaan ini.');
        }

        // Validasi type yang dipilih berasal dari company yang benar
        if (!empty($data['warehouse_type_id'])) {
            $validType = WarehouseType::where('id', $data['warehouse_type_id'])
                ->where('company_id', $company->id)
                ->exists();

            if (!$validType) {
                abort(403, 'Tipe warehouse tidak valid untuk perusahaan ini.');
            }
        }

        // UPDATE
        $warehouse->update([
            'cabang_resto_id'    => $data['cabang_resto_id'],
            'name'               => $data['name'],
            'code'               => strtoupper($data['code'] ?? $warehouse->code),
            'warehouse_type_id'  => $data['warehouse_type_id'] ?? null,
        ]);

        return redirect()->route('warehouse.index', $companyCode)
            ->with('success', 'Warehouse berhasil diperbarui');
    }


    public function destroy($companyCode, $id)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $warehouse = Warehouse::findOrFail($id);

        $validWarehouse = CabangResto::where('id', $warehouse->cabang_resto_id)
            ->where('company_id', $company->id)
            ->exists();

        if (!$validWarehouse) {
            abort(403, 'Warehouse tidak valid untuk perusahaan ini.');
        }

        $warehouse->delete();

        return back()->with('success', 'Warehouse berhasil dihapus');
    }


    public function typesStore(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        WarehouseType::create([
            'company_id' => $company->id,
            'name'       => strtoupper($data['name']),
        ]);

        return back()->with('success', 'Tipe gudang berhasil ditambahkan');
    }

    public function typesDestroy($companyCode, $id)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $type = WarehouseType::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $type->delete();

        return back()->with('success', 'Tipe gudang berhasil dihapus');
    }
    public function typesIndex($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $types = WarehouseType::where('company_id', $company->id)->get();

        return redirect()->route('warehouse.index', [
            'companyCode' => $companyCode,
            'tab' => 'types'
        ]);
    }

    public function show($companyCode, $id)
{
    $company = Company::where('code', $companyCode)->firstOrFail();

    $warehouse = Warehouse::with('cabangResto', 'type')
        ->findOrFail($id);

    // Tenant validation
    $isValid = CabangResto::where('id', $warehouse->cabang_resto_id)
        ->where('company_id', $company->id)
        ->exists();

    if (!$isValid) abort(403, 'Warehouse tidak valid untuk perusahaan ini.');

    return view('company.warehouse.show', [
        'companyCode' => $companyCode,
        'warehouse'   => $warehouse,
    ]);
}
}