<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\CategoriesIssues;
use App\Models\Company;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StocksAdjustmentDetail;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{
    public function index($companyCode, Request $request)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $cabangs = CabangResto::where('company_id', $company->id)->get();

        $warehouses = Warehouse::with(['type', 'cabangResto'])
            ->whereIn('cabang_resto_id', $cabangs->pluck('id'))
            ->when($request->q, function ($q) use ($request) {
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', '%'.$request->q.'%')
                        ->orWhere('code', 'like', '%'.$request->q.'%');
                });
            })
            ->when($request->cabang, function ($q) use ($request) {
                $q->where('cabang_resto_id', $request->cabang);
            })
            ->when($request->type, function ($q) use ($request) {
                $q->where('warehouse_type_id', $request->type);
            })
            ->when($request->sort, function ($q) use ($request) {
                match ($request->sort) {
                    'name_asc' => $q->orderBy('name', 'asc'),
                    'name_desc' => $q->orderBy('name', 'desc'),
                    'latest' => $q->latest(),
                    default => $q->orderBy('name'),
                };
            }, fn ($q) => $q->orderBy('name'))

            ->get();

        $types = WarehouseType::where('company_id', $company->id)->get();

        return view('company.warehouse.index', [
            'companyCode' => $companyCode,
            'warehouses' => $warehouses,
            'types' => $types,
            'cabangs' => $cabangs,
            'filters' => $request->only(['q', 'cabang', 'type', 'sort']),
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
            'cabang_resto_id' => 'required|exists:cabang_resto,id',
            'name' => 'required|string|max:45',
            'code' => 'nullable|string|max:45',
            'warehouse_type_id' => 'nullable|exists:warehouse_types,id',
        ]);

        $cabangValid = CabangResto::where('id', $data['cabang_resto_id'])
            ->where('company_id', $company->id)
            ->exists();

        if (! $cabangValid) {
            abort(403, 'Cabang tidak valid untuk perusahaan ini.');
        }

        // Validasi type juga harus milik company
        if (! empty($data['warehouse_type_id'])) {
            $typeValid = WarehouseType::where('id', $data['warehouse_type_id'])
                ->where('company_id', $company->id)   // <── PENTING, FIX
                ->exists();

            if (! $typeValid) {
                abort(403, 'Tipe warehouse tidak valid untuk perusahaan ini.');
            }
        }

        Warehouse::create([
            'cabang_resto_id' => $data['cabang_resto_id'],
            'name' => $data['name'],
            'code' => strtoupper($data['code'] ?? 'WH-'.Str::random(5)),
            'warehouse_type_id' => $data['warehouse_type_id'] ?? null,
        ]);

        return redirect()->route('warehouse.index', $companyCode)
            ->with('success', 'Warehouse berhasil ditambahkan');
    }

    public function edit($companyCode, $id)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $warehouse = Warehouse::findOrFail($id);

        // Pastikan warehouse brasal dari cabang yang milik company ini
        $isValidWarehouse = CabangResto::where('id', $warehouse->cabang_resto_id)
            ->where('company_id', $company->id)
            ->exists();

        if (! $isValidWarehouse) {
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
            'cabang_resto_id' => 'required|exists:cabang_resto,id',
            'name' => 'required|string|max:45',
            'code' => 'nullable|string|max:45',
            'warehouse_type_id' => 'nullable|exists:warehouse_types,id',
        ]);

        // Validasi cabang yang dipilih berasal dari company yang benar
        $validCabang = CabangResto::where('id', $data['cabang_resto_id'])
            ->where('company_id', $company->id)
            ->exists();

        if (! $validCabang) {
            abort(403, 'Cabang tidak valid untuk perusahaan ini.');
        }

        // Validasi type yang dipilih berasal dari company yang benar
        if (! empty($data['warehouse_type_id'])) {
            $validType = WarehouseType::where('id', $data['warehouse_type_id'])
                ->where('company_id', $company->id)
                ->exists();

            if (! $validType) {
                abort(403, 'Tipe warehouse tidak valid untuk perusahaan ini.');
            }
        }

        // UPDATE
        $warehouse->update([
            'cabang_resto_id' => $data['cabang_resto_id'],
            'name' => $data['name'],
            'code' => strtoupper($data['code'] ?? $warehouse->code),
            'warehouse_type_id' => $data['warehouse_type_id'] ?? null,
        ]);

        return redirect()->route('warehouse.show', [$companyCode, $warehouse->id])
            ->with('success', 'Warehouse berhasil diperbarui');
    }

    public function destroy($companyCode, $id)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $warehouse = Warehouse::findOrFail($id);

        $validWarehouse = CabangResto::where('id', $warehouse->cabang_resto_id)
            ->where('company_id', $company->id)
            ->exists();

        if (! $validWarehouse) {
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
            'name' => strtoupper($data['name']),
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
            'tab' => 'types',
        ]);
    }

    public function show($companyCode, $warehouseId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        $stocks = Stock::with(['item.kategori', 'item.satuan'])
            ->where('warehouse_id', $warehouseId)
            ->orderBy('item_id')
            ->get();

        $filterFrom = request('from');
        $filterTo = request('to');
        $filterIssue = request('issue');

        // =============================
        // MOVEMENTS (IN / OUT / TRANSFER)
        // =============================
        $movements = StockMovement::query()
            ->selectRaw("
        stock_movements.created_at AS date,
        items.name AS item_name,
        stocks.code AS stock_code,
        CASE 
            WHEN stock_movements.type='IN' THEN 'Stok Masuk'
            WHEN stock_movements.type='OUT' THEN 'Stok Keluar'
            WHEN stock_movements.type='TRANSFER_IN' THEN 'Transfer Masuk'
            WHEN stock_movements.type='TRANSFER_OUT' THEN 'Transfer Keluar'
        END AS issue_name,
        NULL AS prev_qty,
        NULL AS after_qty,
        CASE
            WHEN stock_movements.type='IN' THEN stock_movements.qty
            WHEN stock_movements.type='OUT' THEN -stock_movements.qty
            ELSE stock_movements.qty
        END AS diff,
        stock_movements.notes AS note,
        users.username AS created_by_name
    ")
            ->join('items', 'items.id', '=', 'stock_movements.item_id')
            ->join('stocks', 'stocks.id', '=', 'stock_movements.stock_id')
            ->leftJoin('users', 'users.id', '=', 'stock_movements.created_by')
            ->where('stock_movements.warehouse_id', $warehouseId);

        // Filter tanggal movement
        if ($filterFrom) {
            $movements->whereDate('stock_movements.created_at', '>=', $filterFrom);
        }
        if ($filterTo) {
            $movements->whereDate('stock_movements.created_at', '<=', $filterTo);
        }

        // Filter berdasarkan issue (khusus movement)
        $movementMap = [
            'Stok Masuk' => 'IN',
            'Stok Keluar' => 'OUT',
            'Transfer Masuk' => 'TRANSFER_IN',
            'Transfer Keluar' => 'TRANSFER_OUT',
        ];

        if ($filterIssue && isset($movementMap[$filterIssue])) {
            $movements->where('stock_movements.type', $movementMap[$filterIssue]);
        }

        // Jika filter issue adalah kategori adjustment → movement kosong
        if ($filterIssue && ! isset($movementMap[$filterIssue])) {
            $movements->whereRaw('1=0');
        }

        // =============================
        // ADJUSTMENTS
        // =============================
        $adjustments = StocksAdjustmentDetail::query()
            ->selectRaw('
                stocks_adjustmens.adjustment_date AS date,
                items.name AS item_name,
                stocks.code AS stock_code,
                categories_issues.name AS issue_name,
                prev_qty,
                after_qty,
                (after_qty - prev_qty) AS diff,
                stocks_adjustmens.note AS note,
                users.username AS created_by_name
            ')
            ->join('stocks_adjustmens', 'stocks_adjustmens.id', '=', 'stocks_adjustmens_detail.stocks_adjustmens_id')
            ->join('stocks', 'stocks.id', '=', 'stocks_adjustmens_detail.stocks_id')
            ->join('items', 'items.id', '=', 'stocks.item_id')
            ->join('categories_issues', 'categories_issues.id', '=', 'stocks_adjustmens.categories_issues_id')
            ->leftJoin('users', 'users.id', '=', 'stocks_adjustmens.created_by')
            ->where('stocks_adjustmens.warehouse_id', $warehouseId);

        // Filter tanggal adjustment
        if ($filterFrom) {
            $adjustments->whereDate('stocks_adjustmens.adjustment_date', '>=', $filterFrom);
        }
        if ($filterTo) {
            $adjustments->whereDate('stocks_adjustmens.adjustment_date', '<=', $filterTo);
        }

        // Filter kategori penyesuaian
        if ($filterIssue && ! isset($movementMap[$filterIssue])) {
            $adjustments->where('categories_issues.name', $filterIssue);
        }

        // Jika filter issue movement → kosongkan adjustment
        if ($filterIssue && isset($movementMap[$filterIssue])) {
            $adjustments->whereRaw('1=0');
        }

        // =============================
        // MERGE MOVEMENT + ADJUSTMENT
        // =============================
        $warehouseMutations = collect()
            ->merge($movements->get())
            ->merge($adjustments->get())
            ->sortByDesc('date')
            ->values();

        $categoriesIssues = CategoriesIssues::where('company_id', $company->id)->orderBy('name')->get();

        return view('company.warehouse.detail.show', [
            'companyCode' => $companyCode,
            'warehouse' => $warehouse,
            'stocks' => $stocks,
            'categoriesIssues' => $categoriesIssues,
            'warehouseMutations' => $warehouseMutations,
        ]);
    }
}
