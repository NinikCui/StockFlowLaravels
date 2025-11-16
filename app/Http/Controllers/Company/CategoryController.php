<?php
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Category;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class CategoryController extends Controller{
    public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $categories = Category::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('company.category.index', compact('categories', 'companyCode'));
    }

    // Form tambah
    public function create($companyCode)
    {
        return view('company.category.create', compact('companyCode'));
    }

    // Simpan
    public function store(Request $request, $companyCode)
    {
        $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|max:50|unique:categories,code',
        ]);

        $company = Company::where('code', $companyCode)->firstOrFail();

        Category::create([
            'company_id' => $company->id,
            'name'       => $request->name,
            'code'       => strtoupper($request->code),
            'is_active'  => true,
        ]);

        return redirect()->route('category.index', $companyCode)
            ->with('success', 'Category created successfully.');
    }

    // Form edit
    public function edit($companyCode, $code)
    {
        $category = Category::where('code', $code)->firstOrFail();

        return view('company.category.edit', compact('category', 'companyCode'));
    }

    // Update
    public function update(Request $request, $companyCode, $code)
    {
        $category = Category::where('code', $code)->firstOrFail();

        $request->validate([
            'name' => 'required|max:255',
        ]);

        $category->update([
            'name' => $request->name,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->route('category.index', $companyCode)
            ->with('success', 'Category updated successfully.');
    }

    // Delete
    public function destroy($companyCode, $code)
    {
        $category = Category::where('code', $code)->firstOrFail();
        $category->delete();

        return redirect()->route('category.index', $companyCode)
            ->with('success', 'Category deleted.');
    }
}