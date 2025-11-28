<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Item;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemsController extends Controller
{
    private function getCompany($companyCode)
    {
        return Company::where('code', $companyCode)->firstOrFail();
    }

    public function index($companyCode)
    {
        $company = $this->getCompany($companyCode);

        return view('company.items.index', [
            'companyCode' => $companyCode,
            'items' => Item::where('company_id', $company->id)->get(),
            'kategori' => Category::where('company_id', $company->id)->get(),
            'satuan' => Satuan::where('company_id', $company->id)->get(),
        ]);
    }

    // ITEM
    public function createItem($companyCode)
    {
        $company = $this->getCompany($companyCode);

        return view('company.items.item.create', [
            'companyCode' => $companyCode,
            'kategori' => Category::where('company_id', $company->id)->get(),
            'satuan' => Satuan::where('company_id', $company->id)->get(),
        ]);
    }

    public function storeItem(Request $r, $companyCode)
    {
        $company = $this->getCompany($companyCode);

        $r->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan_id' => 'required|exists:satuan,id',
        ]);

        Item::create([
            'company_id' => $company->id,
            'name' => $r->name,
            'category_id' => $r->category_id,
            'satuan_id' => $r->satuan_id,
        ]);

        return redirect()->route('items.index', $companyCode)
            ->with('activeTab', 'item')
            ->with('success', 'Item berhasil ditambahkan');
    }

    public function editItem($companyCode, $id)
    {
        $company = $this->getCompany($companyCode);

        $item = Item::where('company_id', $company->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('company.items.item.edit', [
            'companyCode' => $companyCode,
            'item' => $item,
            'kategori' => Category::where('company_id', $company->id)->get(),
            'satuan' => Satuan::where('company_id', $company->id)->get(),
        ]);
    }

    public function updateItem(Request $r, $companyCode, $id)
    {
        $company = $this->getCompany($companyCode);

        $item = Item::where('company_id', $company->id)
            ->where('id', $id)
            ->firstOrFail();

        $r->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan_id' => 'required|exists:satuan,id',
        ]);

        $item->update([
            'name' => $r->name,
            'category_id' => $r->category_id,
            'satuan_id' => $r->satuan_id,
        ]);

        return redirect()->route('items.index', $companyCode)
            ->with('activeTab', 'item')
            ->with('success', 'Item berhasil diperbarui');
    }

    public function deleteItem($companyCode, $id)
    {
        $company = $this->getCompany($companyCode);

        $item = Item::where('company_id', $company->id)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return redirect()->route('items.index', $companyCode)
            ->with('activeTab', 'item')
            ->with('success', 'Item berhasil dihapus');
    }

    // CATEGORY
    public function createCategory($companyCode)
    {
        return view('company.items.category.create', [
            'companyCode' => $companyCode,
        ]);
    }

    public function storeCategory(Request $r, $companyCode)
    {
        $company = $this->getCompany($companyCode);

        $r->validate([
            'code' => ['required', Rule::unique('categories')->where('company_id', $company->id)],
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'company_id' => $company->id,
            'code' => $r->code,
            'name' => $r->name,
        ]);

        return redirect()->route('items.index', $companyCode)
            ->with('activeTab', 'kategori')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function editCategory($companyCode, $code)
    {
        $company = $this->getCompany($companyCode);

        $category = Category::where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        return view('company.items.category.edit', [
            'companyCode' => $companyCode,
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $r, $companyCode, $code)
    {
        $company = $this->getCompany($companyCode);

        $category = Category::where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        $r->validate([
            'code' => [
                'required',
                Rule::unique('categories')->where('company_id', $company->id)->ignore($category->id),
            ],
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'code' => $r->code,
            'name' => $r->name,
        ]);

        return redirect()->route('items.index', $companyCode)
            ->with('activeTab', 'kategori')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function deleteCategory($companyCode, $code)
    {
        $company = $this->getCompany($companyCode);

        $category = Category::where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        $category->delete();

        return redirect()->route('items.index', $companyCode)
            ->with('activeTab', 'kategori')
            ->with('success', 'Kategori berhasil dihapus');
    }

    // SATUAN
    public function createSatuan($companyCode)
    {
        return view('company.items.satuan.create', [
            'companyCode' => $companyCode,
        ]);
    }

    public function storeSatuan(Request $r, $companyCode)
    {
        $company = $this->getCompany($companyCode);

        $r->validate([
            'code' => ['required', Rule::unique('satuan')->where('company_id', $company->id)],
            'name' => 'required|string|max:255',
        ]);

        Satuan::create([
            'company_id' => $company->id,
            'code' => $r->code,
            'name' => $r->name,
        ]);

        return redirect()->route('items.index', $companyCode)
            ->with('activeTab', 'satuan')
            ->with('success', 'Satuan berhasil ditambahkan');
    }

    public function editSatuan($companyCode, $code)
    {
        $company = $this->getCompany($companyCode);

        $satuan = Satuan::where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        return view('company.items.satuan.edit', [
            'companyCode' => $companyCode,
            'satuan' => $satuan,
        ]);
    }

    public function updateSatuan(Request $r, $companyCode, $code)
    {
        $company = $this->getCompany($companyCode);

        $satuan = Satuan::where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        $r->validate([
            'code' => [
                'required',
                Rule::unique('satuan')->where('company_id', $company->id)->ignore($satuan->id),
            ],
            'name' => 'required|string|max:255',
        ]);

        $satuan->update([
            'code' => $r->code,
            'name' => $r->name,
        ]);

        return redirect()->route('items.index', $companyCode)
            ->with('activeTab', 'satuan')
            ->with('success', 'Satuan berhasil diperbarui');
    }

    public function deleteSatuan($companyCode, $code)
    {
        $company = $this->getCompany($companyCode);

        $satuan = Satuan::where('company_id', $company->id)
            ->where('code', $code)
            ->firstOrFail();

        $satuan->delete();

        return redirect()->route('items.index', $companyCode)
            ->with('activeTab', 'satuan')
            ->with('success', 'Satuan berhasil dihapus');
    }
}
