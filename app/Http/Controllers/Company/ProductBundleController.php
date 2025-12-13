<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\ProductBundleItem;
use Illuminate\Http\Request;

class ProductBundleController extends Controller
{
    protected function loadCompany(): Company
    {
        $companyId = session('role.company.id');
        abort_if(! $companyId, 403);

        return Company::findOrFail($companyId);
    }

    /**
     * LIST PAKET
     */
    public function index()
    {
        $company = $this->loadCompany();
        $companyCode = strtolower(session('role.company.code'));
        $bundles = ProductBundle::with('items.product')
            ->where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->get();

        return view('company.bundles.index', compact(
            'bundles', 'companyCode'
        ));
    }

    /**
     * FORM CREATE
     */
    public function create()
    {
        $company = $this->loadCompany();
        $companyCode = strtolower(session('role.company.code'));

        $products = Product::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('company.bundles.create', compact(
            'products', 'companyCode'
        ));
    }

    /**
     * SIMPAN PAKET BARU
     */
    public function store(Request $request)
    {
        $company = $this->loadCompany();
        $companyCode = strtolower(session('role.company.code'));

        $request->validate([
            'name' => 'required|string|max:100',
            'bundle_price' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $bundle = ProductBundle::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'bundle_price' => $request->bundle_price,
            'is_active' => true,
        ]);

        foreach ($request->items as $item) {
            ProductBundleItem::create([
                'product_bundle_id' => $bundle->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
            ]);
        }

        return redirect()
            ->route('bundles.index', [$companyCode])
            ->with('success', 'Paket berhasil dibuat');
    }

    /**
     * FORM EDIT
     */
    public function edit($companyCode, ProductBundle $bundle)
    {
        $company = $this->loadCompany();
        $companyCode = strtolower(session('role.company.code'));

        abort_if($bundle->company_id !== $company->id, 403);

        $bundle->load('items.product');

        $products = Product::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('company.bundles.edit', compact(
            'bundle',
            'products', 'companyCode'
        ));
    }

    /**
     * UPDATE PAKET
     */
    public function update($companyCode, Request $request, ProductBundle $bundle)
    {
        $company = $this->loadCompany();

        abort_if($bundle->company_id !== $company->id, 403);

        $request->validate([
            'name' => 'required|string|max:100',
            'bundle_price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $bundle->update([
            'name' => $request->name,
            'bundle_price' => $request->bundle_price,
            'is_active' => $request->is_active,
        ]);

        $bundle->items()->delete();

        foreach ($request->items as $item) {
            ProductBundleItem::create([
                'product_bundle_id' => $bundle->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
            ]);
        }

        return redirect()
            ->route('bundles.index', [$companyCode])
            ->with('success', 'Paket berhasil diperbarui');
    }

    /**
     * HAPUS PAKET
     */
    public function destroy($companyCode, ProductBundle $bundle)
    {
        $company = $this->loadCompany();

        abort_if($bundle->company_id !== $company->id, 403);

        $bundle->delete();

        return redirect()
            ->route('bundles.index', [$companyCode])
            ->with('success', 'Paket berhasil dihapus');
    }
}
