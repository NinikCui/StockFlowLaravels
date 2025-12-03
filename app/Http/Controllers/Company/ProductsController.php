<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    // LIST
    public function index($companyCode)
    {
        $companyId = session('role.company.id');

        $products = Product::with('category')
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $categories = Category::where('company_id', $companyId)->get();

        return view('company.products.index', compact('companyCode', 'products', 'categories'));
    }

    public function show($companyCode, Product $product)
    {
        // security check
        if ($product->company_id !== session('role.company.id')) {
            abort(403);
        }

        $bomItems = $product->bomItems()->with('item')->get();

        return view('company.products.show', compact('product', 'bomItems', 'companyCode'));
    }

    // FORM CREATE
    public function create($companyCode)
    {
        $companyId = session('role.company.id');
        $categories = Category::where('company_id', $companyId)->get();

        return view('company.products.create', compact('companyCode', 'categories'));

    }

    // STORE
    // STORE
    public function store(Request $request, $companyCode)
    {
        $companyId = session('role.company.id');

        $validated = $request->validate([
            'name' => 'required|max:45',
            'code' => "required|max:45|unique:products,code,NULL,id,company_id,$companyId",
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        Product::create([
            'company_id' => $companyId,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'base_price' => $validated['base_price'],
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()
            ->route('products.index', $companyCode)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    // FORM EDIT
    public function edit($companyCode, Product $product)
    {
        $this->authorizeProduct($product);

        return view('company.products.edit', compact('product', 'companyCode'));
    }

    // UPDATE
    public function update(Request $request, $companyCode, Product $product)
    {
        $this->authorizeProduct($product);

        $validated = $request->validate([
            'name' => 'required|max:45',
            'code' => 'required|max:45',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()
            ->route('products.show', [$companyCode, $product])
            ->with('success', 'Produk berhasil diperbarui.');
    }

    // DELETE
    public function destroy($companyCode, Product $product)
    {
        $this->authorizeProduct($product);

        $product->delete();

        return redirect()
            ->route('products.index', [$companyCode])
            ->with('success', 'Produk berhasil diperbarui.');
    }

    // PRIVATE GUARD
    private function authorizeProduct(Product $product)
    {
        if ($product->company_id !== session('role.company.id')) {
            abort(403, 'Akses produk tidak valid.');
        }
    }
}
