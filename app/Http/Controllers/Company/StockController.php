<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CategoriesIssues;
use App\Models\Company;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StocksAdjustment;
use App\Models\StocksAdjustmentDetail;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    public function createIn($companyCode, $warehouseId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        // Tenant check
        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang bukan milik perusahaan ini.');
        }

        // Generate CODE
        $prefix = 'STK-'.strtoupper($warehouse->code).'-';

        $last = Stock::where('warehouse_id', $warehouse->id)
            ->orderBy('id', 'DESC')
            ->first();

        $nextNumber = $last
            ? intval(substr($last->code, -4)) + 1
            : 1;

        $generatedCode = $prefix.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Ambil items untuk select
        $items = Item::where('company_id', $company->id)
            ->with('satuan')
            ->orderBy('name')
            ->get();

        return view('company.warehouse.detail.partials.stock-in-create', [
            'companyCode' => $companyCode,
            'warehouse' => $warehouse,
            'items' => $items,
            'generatedCode' => $generatedCode,
        ]);
    }

    public function storeIn(Request $request, $companyCode, $warehouseId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:stocks,code',
            'item_id' => 'required|exists:items,id',
            'qty' => 'required|numeric|min:0.01',
            'expired_at' => 'nullable|date|after:yesterday',
            'notes' => 'nullable|string|max:255',
        ]);

        // Buat stok baru (1 row = 1 batch)
        $stock = Stock::create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'item_id' => $data['item_id'],
            'qty' => $data['qty'],
            'code' => $data['code'],
            'expired_at' => $data['expired_at'] ?? null,
        ]);

        // Movement log
        StockMovement::create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'created_by' => auth()->id(),
            'item_id' => $data['item_id'],
            'stock_id' => $stock->id,
            'type' => 'IN',
            'qty' => $data['qty'],
            'expired_at' => $data['expired_at'] ?? null, // opsional
            'notes' => $data['notes'],
            'reference' => 'Manual Stock In - '.$data['code'],
        ]);

        return redirect()->route('warehouse.show', [$companyCode, $warehouse->id])
            ->with('success', 'Stok berhasil ditambahkan.');
    }

    public function storeAdjustment(Request $request, $companyCode, $warehouseId)
    {
        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'prev_qty' => 'required|numeric',
            'after_qty' => 'required|numeric',
            'categories_issues_id' => 'required|exists:categories_issues,id',
            'note' => 'nullable|string|max:200',
        ]);

        if ($request->after_qty == $request->prev_qty) {
            return back()->withErrors([
                'after_qty' => 'Qty penyesuaian tidak berubah.',
            ])->withInput();
        }

        $company = Company::where('code', $companyCode)->firstOrFail();
        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403, 'Gudang tidak valid.');
        }

        $stock = Stock::where('id', $request->stock_id)
            ->where('warehouse_id', $warehouseId)
            ->firstOrFail();

        // 1️⃣ Buat header adjustment
        $adj = StocksAdjustment::create([
            'warehouse_id' => $warehouseId,
            'categories_issues_id' => $request->categories_issues_id,
            'adjustment_date' => now(),
            'status' => 'POSTED',
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);

        // 2️⃣ Detail adjustment
        StocksAdjustmentDetail::create([
            'stocks_adjustmens_id' => $adj->id,
            'stocks_id' => $stock->id,
            'prev_qty' => $request->prev_qty,
            'after_qty' => $request->after_qty,
        ]);

        // 3️⃣ Update stok utama
        $stock->qty = $request->after_qty;
        $stock->save();

        return back()->with('success', 'Penyesuaian stok berhasil disimpan.');
    }

    public function itemHistory($companyCode, $warehouseId, $stockId)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $warehouse = Warehouse::with('cabangResto')
            ->findOrFail($warehouseId);

        if ($warehouse->cabangResto->company_id !== $company->id) {
            abort(403);
        }

        // Ambil stok + item
        $stock = Stock::with('item', 'warehouse')
            ->where('id', $stockId)
            ->where('warehouse_id', $warehouseId)
            ->firstOrFail();

        $item = $stock->item;

        // ================================
        // MERGE SEMUA HISTORY (UNIVERSAL)
        // ================================
        $history = collect()
            ->merge($stock->historyAdjustments())   // ADJUSTMENT
            ->merge($stock->historyMovements())
            ->sortByDesc('date')
            ->values();

        // Semua user terkait
        $users = User::whereIn('username', $history->pluck('user')->filter())->get();

        // Issue categories (untuk filter jika diperlukan)
        $categoriesIssues = CategoriesIssues::where('company_id', $company->id)->get();

        return view('company.warehouse.detail.item-history', compact(
            'companyCode',
            'warehouse',
            'stock',
            'item',
            'history',
            'users',
            'categoriesIssues'
        ));
    }

    public function destroy($companyCode, $warehouseId, $stockId)
    {
        $stock = Stock::findOrFail($stockId);

        if ($stock->qty > 0) {
            return back()->with('error', 'Stok tidak bisa dihapus karena masih memiliki jumlah.');
        }

        $stock->delete();

        return back()->with('success', 'Stok berhasil dihapus.');
    }
}
