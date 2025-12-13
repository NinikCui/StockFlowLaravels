<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\ProductBundleItem;
use Illuminate\Http\Request;

class BranchProductBundleController extends Controller
{
    /* =====================================================
     * LOAD BRANCH CONTEXT
     * ===================================================== */
    protected function loadBranch(string $branchCode): array
    {
        $companyId = session('role.company.id');
        abort_if(! $companyId, 403, 'Company tidak ditemukan.');

        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        $company = Company::findOrFail($companyId);

        return [$company, $branch];
    }

    /* =====================================================
     * INDEX
     * ===================================================== */
    public function index(string $branchCode)
    {
        [$company, $branch] = $this->loadBranch($branchCode);

        $bundles = ProductBundle::with('items.product')
            ->where('company_id', $company->id)
            ->where(function ($q) use ($branch) {
                $q->whereNull('cabang_resto_id')
                    ->orWhere('cabang_resto_id', $branch->id);
            })
            ->orderByRaw('cabang_resto_id IS NULL') // universal di atas
            ->orderByDesc('created_at')
            ->get();

        return view('branch.bundles.index', compact(
            'branchCode',
            'branch',
            'bundles'
        ));
    }

    /* =====================================================
     * CREATE FORM
     * ===================================================== */
    public function create(string $branchCode)
    {
        [$company, $branch] = $this->loadBranch($branchCode);

        $products = Product::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('branch.bundles.create', compact(
            'branchCode',
            'branch',
            'products'
        ));
    }

    public function store(Request $request, $branchCode)
    {
        [$company, $branch] = $this->loadBranch($branchCode);
        $request->validate([
            'name' => 'required|string|max:100',
            'bundle_price' => 'required|numeric|min:1',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $bundle = ProductBundle::create([
            'company_id' => $company->id,
            'cabang_resto_id' => $branch->id,
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
            ->route('bundles.index', [$branchCode])
            ->with('success', 'Paket berhasil dibuat');
    }

    public function edit(string $branchCode, ProductBundle $bundle)
    {
        [$company, $branch] = $this->loadBranch($branchCode);

        abort_if(
            $bundle->company_id !== $company->id ||
            $bundle->cabang_resto_id !== $branch->id,
            403
        );

        $bundle->load('items.product');

        $products = Product::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('branch.bundles.edit', compact(
            'branchCode',
            'branch',
            'bundle',
            'products'
        ));
    }

    /* =====================================================
     * UPDATE
     * ===================================================== */
    public function update(Request $request, string $branchCode, ProductBundle $bundle)
    {
        [$company, $branch] = $this->loadBranch($branchCode);

        abort_if(
            $bundle->company_id !== $company->id ||
            $bundle->cabang_resto_id !== $branch->id,
            403
        );

        $request->validate([
            'name' => 'required|string|max:100',
            'bundle_price' => 'required|numeric|min:1',
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

        // reset item paket
        $bundle->items()->delete();

        foreach ($request->items as $item) {
            ProductBundleItem::create([
                'product_bundle_id' => $bundle->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
            ]);
        }

        return redirect()
            ->route('bundles.index', [$branchCode])
            ->with('success', 'Paket berhasil diperbarui');
    }

    /* =====================================================
     * DELETE
     * ===================================================== */
    public function destroy(string $branchCode, ProductBundle $bundle)
    {
        [$company, $branch] = $this->loadBranch($branchCode);

        abort_if(
            $bundle->company_id !== $company->id ||
            $bundle->cabang_resto_id !== $branch->id,
            403
        );

        $bundle->delete();

        return redirect()
            ->route('bundles.index', [$branchCode])
            ->with('success', 'Paket berhasil dihapus');
    }
}
