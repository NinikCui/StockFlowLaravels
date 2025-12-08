<?php

namespace App\Http\Controllers\Branch;

use app\http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Product;

class BranchProductController extends Controller
{
    public function index($branchCode)
    {
        $companyId = session('role.company.id');

        // Pastikan cabang milik perusahaan ini
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // Produk milik perusahaan
        $products = Product::with('category')
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('branch.products.index', compact('branchCode', 'products'));
    }

    public function show($branchCode, Product $product)
    {
        $companyId = session('role.company.id');

        // validasi cabang
        $branch = CabangResto::where('company_id', $companyId)
            ->where('code', $branchCode)
            ->firstOrFail();

        // cegah branch lihat produk perusahaan lain
        if ($product->company_id != $companyId) {
            abort(403);
        }

        // ambil BOM produk
        $bomItems = $product->bomItems()
            ->with(['item.satuan', 'item.kategori'])
            ->orderBy('id')
            ->get();

        $totalCost = $bomItems->sum(fn ($b) => $b->qty_per_unit * ($b->price_per_unit ?? 0));

        return view('branch.products.show', compact(
            'branchCode',
            'product',
            'bomItems',
            'totalCost'
        ));
    }
}
