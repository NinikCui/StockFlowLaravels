<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Boms;
use App\Models\Item;
use App\Models\Product;
use Illuminate\Http\Request;

class BomController extends Controller
{
    // TAMPILKAN BOM PER PRODUK
    public function index($companyCode, Product $product)
    {
        $this->authorizeProduct($product);

        $companyId = session('role.company.id');

        $bomItems = Boms::where('company_id', $companyId)
            ->where('products_id', $product->id)
            ->with('item')
            ->get();

        $items = Item::where('company_id', $companyId)->get();

        return view('company.products.bom', compact('product', 'bomItems', 'items', 'companyCode'));
    }

    // TAMBAH ITEM BOM
    public function store(Request $request, $companyCode, Product $product)
    {
        $this->authorizeProduct($product);

        $companyId = session('role.company.id');

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty_per_unit' => 'required|numeric|min:0.01',
        ]);

        // Cegah duplikat item
        $exists = Boms::where('company_id', $companyId)
            ->where('product_id', $product->id)
            ->where('item_id', $validated['item_id'])
            ->first();

        if ($exists) {
            return back()->withErrors('Bahan tersebut sudah ada dalam BOM.');
        }

        Boms::create([
            'company_id' => $companyId,
            'product_id' => $product->id,
            'item_id' => $validated['item_id'],
            'qty_per_unit' => $validated['qty_per_unit'],
        ]);

        return back()->with('success', 'Bahan berhasil ditambahkan ke BOM.');
    }

    // UPDATE BOM ITEM (misal edit qty)
    public function update(Request $request, $companyCode, Boms $bom)
    {
        $this->authorizeBom($bom);

        $validated = $request->validate([
            'qty_per_unit' => 'required|numeric|min:0.01',
        ]);

        $bom->update($validated);

        return back()->with('success', 'Bahan BOM diperbarui.');
    }

    // DELETE ITEM BOM
    public function destroy($companyCode, Boms $bom)
    {
        $this->authorizeBom($bom);

        $bom->delete();

        return back()->with('success', 'Bahan BOM dihapus.');
    }

    // PRIVATE GUARDS
    private function authorizeProduct(Product $product)
    {
        if ($product->company_id !== session('role.company.id')) {
            abort(403);
        }
    }

    private function authorizeBom(Boms $bom)
    {
        if ($bom->company_id !== session('role.company.id')) {
            abort(403);
        }
    }
}
