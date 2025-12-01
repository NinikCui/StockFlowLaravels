<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use Illuminate\Http\Request;

class BranchWarehouseController extends Controller
{
    /**
     * INDEX — Daftar Gudang Cabang
     */
    public function warehousesIndex($branchCode)
    {
        $companyCode = session('role.company.code');
        $companyId = session('role.company.id');

        // Validasi cabang milik company
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $query = Warehouse::with(['type'])
            ->where('cabang_resto_id', $branch->id)
            ->withCount('stocks')
            ->withSum('stocks', 'qty');

        // SEARCH
        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            });
        }

        // FILTER TYPE
        if ($type = request('type')) {
            $query->where('warehouse_type_id', $type);
        }

        // SORT
        switch (request('sort')) {
            case 'name_desc':
                $query->orderBy('name', 'DESC');
                break;

            case 'latest':
                $query->orderBy('id', 'DESC');
                break;

            default:
                $query->orderBy('name', 'ASC');
        }

        // Ambil semua data tanpa pagination
        $warehouses = $query->get();

        // Dropdown tipe gudang
        $types = WarehouseType::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('branch.warehouse.index', [
            'companyCode' => strtolower($companyCode),
            'branchCode' => $branchCode,
            'branch' => $branch,
            'warehouses' => $warehouses,
            'types' => $types,
        ]);
    }

    /**
     * CREATE — Form Tambah Gudang
     */
    public function create($branchCode)
    {
        $companyId = session('role.company.id');
        $cabang = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();
        $types = WarehouseType::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('branch.warehouse.create', [
            'branch' => $cabang,
            'branchCode' => $branchCode,
            'types' => $types,
        ]);
    }

    /**
     * STORE — Simpan Gudang Baru
     */
    public function store(Request $request, $branchCode)
    {
        $companyId = session('role.company.id');

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:warehouse,code',
            'warehouse_type_id' => 'required|exists:warehouse_types,id',
        ]);

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        Warehouse::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'warehouse_type_id' => $request->warehouse_type_id,
            'cabang_resto_id' => $branch->id,
        ]);

        return redirect()
            ->route('branch.warehouse.index', $branchCode)
            ->with('success', 'Gudang berhasil ditambahkan.');
    }

    /**
     * EDIT — Form Edit Gudang
     */
    public function edit($branchCode, Warehouse $warehouse)
    {
        $companyId = session('role.company.id');

        // Validasi scope
        $this->authorizeWarehouse($warehouse, $companyId);

        $types = WarehouseType::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('branch.warehouse.edit', [
            'branchCode' => $branchCode,
            'warehouse' => $warehouse,
            'types' => $types,
        ]);
    }

    /**
     * UPDATE — Simpan Perubahan
     */
    public function update(Request $request, $branchCode, Warehouse $warehouse)
    {
        $companyId = session('role.company.id');
        $this->authorizeWarehouse($warehouse, $companyId);

        $request->validate([
            'name' => 'required|string|max:255',
            'warehouse_type_id' => 'required|exists:warehouse_types,id',
        ]);

        $warehouse->update([
            'name' => $request->name,
            'warehouse_type_id' => $request->warehouse_type_id,
        ]);

        return redirect()
            ->route('branch.warehouse.index', $branchCode)
            ->with('success', 'Gudang berhasil diperbarui.');
    }

    /**
     * DELETE — Hapus Gudang
     */
    public function destroy($branchCode, Warehouse $warehouse)
    {
        $companyId = session('role.company.id');
        $this->authorizeWarehouse($warehouse, $companyId);

        $warehouse->delete();

        return redirect()
            ->route('branch.warehouse.index', $branchCode)
            ->with('success', 'Gudang berhasil dihapus.');
    }

    private function authorizeWarehouse(Warehouse $warehouse, $companyId)
    {
        if ($warehouse->branch->company_id !== $companyId) {
            abort(403, 'Tidak boleh mengakses gudang ini.');
        }
    }
}
