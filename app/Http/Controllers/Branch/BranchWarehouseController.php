<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use Illuminate\Http\Request;

class BranchWarehouseController extends Controller
{
    public function warehousesIndex($branchCode)
    {
        $companyCode = session('role.company.code');
        $companyId = session('role.company.id');

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

        // SORTING
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

        $warehouses = $query->get();

        // Dropdown type list
        $types = WarehouseType::orderBy('name')->get();

        return view('branch.warehouse.index', [
            'companyCode' => strtolower($companyCode),
            'branchCode' => $branchCode,
            'branch' => $branch,
            'warehouses' => $warehouses,
            'types' => $types,
        ]);
    }

    public function create($branchCode)
    {
        $companyId = session('role.company.id');

        // Validasi branch
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Ambil semua tipe gudang
        $types = WarehouseType::where('company_id', $companyId)->orderBy('name')->get();

        return view('branch.warehouse.create', [
            'branchCode' => $branchCode,
            'branch' => $branch,
            'types' => $types,
        ]);
    }

    public function store(Request $request, $branchCode)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:warehouse,code',
            'warehouse_type_id' => 'required|exists:warehouse_types,id',
        ]);

        Warehouse::create([
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'warehouse_type_id' => $data['warehouse_type_id'],
            'cabang_resto_id' => $branch->id,
        ]);

        return redirect()
            ->route('branch.warehouse.index', $branchCode)
            ->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function update(Request $request, $branchCode, Warehouse $warehouse)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        if ($warehouse->cabang_resto_id !== $branch->id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:warehouse,code,'.$warehouse->id,
            'warehouse_type_id' => 'required|exists:warehouse_types,id',
        ]);

        $warehouse->update($data);

        return redirect()
            ->route('branch.warehouse.index', $branchCode)
            ->with('success', 'Gudang berhasil diperbarui.');
    }

    public function edit($branchCode, Warehouse $warehouse)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        if ($warehouse->cabang_resto_id !== $branch->id) {
            abort(403);
        }

        $types = WarehouseType::all();

        return view('branch.warehouse.edit', [
            'branchCode' => $branchCode,
            'warehouse' => $warehouse,
            'types' => $types,
        ]);
    }

    public function destroy($branchCode, Warehouse $warehouse)
    {
        $companyId = session('role.company.id');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        if ($warehouse->cabang_resto_id !== $branch->id) {
            abort(403);
        }

        if ($warehouse->stocks()->count() > 0) {
            return back()->withErrors(['Gudang tidak dapat dihapus karena masih memiliki stok.']);
        }

        $warehouse->delete();

        return back()->with('success', 'Gudang berhasil dihapus.');
    }
}
