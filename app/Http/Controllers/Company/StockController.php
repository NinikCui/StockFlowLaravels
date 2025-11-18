<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Category;
use App\Models\Company;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\Role;
use App\Models\Satuan;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StocksAdjustment;
use App\Models\StocksAdjustmentDetail;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
class StockController extends Controller
{
    public function createIn($companyCode, $warehouseId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $warehouse = Warehouse::where('id', $warehouseId)->firstOrFail();

        // Validasi tenant
        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid untuk perusahaan ini.');
        }

        // Semua item milik company
        $items = Item::where('company_id', $company->id)
            ->with(['kategori', 'satuan'])
            ->orderBy('name')
            ->get();

        return view('company.warehouse.detail.partials.stock-in-create', [
            'companyCode' => $companyCode,
            'warehouse'   => $warehouse,
            'items'       => $items,
        ]);
    }

    public function storeIn(Request $request, $companyCode, $warehouseId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        // Tenant check
        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        // Validasi form
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty'     => 'required|numeric|min:0.01',
            'notes'   => 'nullable|string|max:255',
        ]);

        // Cek stok eksisting
        $stock = Stock::where('warehouse_id', $warehouse->id)
            ->where('item_id', $data['item_id'])
            ->first();

        if (!$stock) {
            $stock = Stock::create([
                'company_id'   => $company->id,
                'warehouse_id' => $warehouse->id,
                'item_id'      => $data['item_id'],
                'qty'          => 0,
            ]);
        }

        // Tambahkan stok
        $stock->qty += $data['qty'];
        $stock->save();

        // Buat movement log
        StockMovement::create([
            'company_id'   => $company->id,
            'warehouse_id' => $warehouse->id,
            'item_id'      => $data['item_id'],
            'type'         => 'IN',
            'qty'          => $data['qty'],
            'notes'        => $data['notes'],
            'reference'    => 'Manual Stock In',
        ]);

        return redirect()->route('warehouse.show', [$companyCode, $warehouse->id])
            ->with('success', 'Stok berhasil ditambahkan.');
    }

    public function storeAdjustment(Request $request, $companyCode, $warehouseId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        $data = $request->validate([
            'stock_id'              => 'required|exists:stocks,id',
            'prev_qty'              => 'required|numeric',
            'after_qty'             => 'required|numeric',
            'categories_issues_id'  => 'required|exists:categories_issues,id',
            'note'                  => 'nullable|string|max:200',
        ]);

        $stock = Stock::findOrFail($data['stock_id']);

        // 1️⃣ Buat header adjustment
        $adj = StocksAdjustment::create([
            'warehouse_id'         => $warehouseId,
            'categories_issues_id' => $data['categories_issues_id'],
            'adjustment_date'      => now(),
            'status'               => 'DRAFT',
            'note'                 => $data['note'],
            'created_by'           => auth()->id(),
            'stockId'              => $stock->id,
        ]);

        // 2️⃣ Buat detail adjustment
        StocksAdjustmentDetail::create([
            'stocks_adjustmens_id' => $adj->id,
            'stocks_id'            => $stock->id,
            'prev_qty'             => $data['prev_qty'],
            'after_qty'            => $data['after_qty'],
        ]);

        // 3️⃣ Update stok utama
        $stock->qty = $data['after_qty'];
        $stock->save();

        return back()->with('success', 'Penyesuaian stok berhasil disimpan.');
    }


}
