<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Boms;
use App\Models\Item;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BomController extends Controller
{
    public function index($companyCode, Product $product)
    {

        $companyId = session('role.company.id');

        $bomItems = Boms::where('company_id', $companyId)
            ->where('product_id', $product->id)
            ->with(['item.kategori', 'item.satuan'])
            ->get();

        $items = Item::where('company_id', $companyId)
            ->with('satuan')
            ->get();

        return view('company.products.bom', compact('product', 'bomItems', 'items', 'companyCode'));
    }

    public function store(Request $request, $companyCode, Product $product)
    {

        $companyId = session('role.company.id');

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty_per_unit' => 'required|numeric|min:0.001',
        ]);

        if (Boms::where('company_id', $companyId)
            ->where('product_id', $product->id)
            ->where('item_id', $validated['item_id'])
            ->exists()
        ) {
            return back()->withErrors(['item_id' => 'Bahan tersebut sudah ada dalam BOM.']);
        }

        Boms::create([
            'company_id' => $companyId,
            'product_id' => $product->id,
            'item_id' => $validated['item_id'],
            'qty_per_unit' => $validated['qty_per_unit'],
        ]);

        return back()->with('success', 'Bahan berhasil ditambahkan ke BOM.');
    }

    public function update(Request $request, $companyCode, Product $product, Boms $bom)
    {

        // Pastikan BOM milik product ini
        if ($bom->product_id != $product->id) {
            abort(403);
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty_per_unit' => 'required|numeric|min:0.001',
        ]);

        // Cek duplikat item (kecuali item yang sedang diedit)
        $exists = Boms::where('company_id', session('role.company.id'))
            ->where('product_id', $product->id)
            ->where('item_id', $validated['item_id'])
            ->where('id', '!=', $bom->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['item_id' => 'Bahan tersebut sudah ada dalam BOM.']);
        }

        $bom->update($validated);

        return back()->with('success', 'Bahan BOM berhasil diupdate.');
    }

    public function destroy($companyCode, Product $product, Boms $bom)
    {
        Log::info('anjayy sampai sini 1 ');

        // Pastikan BOM milik product ini
        if ($bom->product_id != $product->id) {
            abort(403);
        }
        Log::info('anjayy sampai sini  2');
        $bom->delete();

        return back()->with('success', 'Bahan berhasil dihapus dari BOM.');
    }
}
