<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Category;
use App\Models\Company;
use App\Models\Item;
use App\Models\Satuan;
use App\Models\UnitConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItemsController extends Controller
{
    private function getCompany($companyCode)
    {
        return Company::where('code', $companyCode)->firstOrFail();
    }

    private function getAvailableSatuan($companyId)
    {
        return Satuan::whereNull('company_id')
            ->orWhere('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function index($companyCode)
    {
        $company = $this->getCompany($companyCode);

        return view('company.items.index', [
            'companyCode' => $companyCode,
            'items' => Item::where('company_id', $company->id)->get(),
            'kategori' => Category::where('company_id', $company->id)->get(),
            'satuan' => $this->getAvailableSatuan($company->id),
            'unitConversions' => UnitConversion::with([
                'fromSatuan',
                'toSatuan',
            ])
                ->where('is_active', true)
                ->orderBy('from_satuan_id')
                ->orderBy('to_satuan_id')
                ->select('*')
                ->selectRaw("
                        CASE
                            WHEN factor = FLOOR(factor)
                                THEN FORMAT(factor, 0, 'id_ID')
                            ELSE
                                TRIM(TRAILING ',' FROM TRIM(TRAILING '0' FROM FORMAT(factor, 6, 'id_ID')))
                        END AS formatted_factor
                    ")
                ->get(),
        ]);
    }

    // ITEM
    public function createItem($companyCode)
    {
        $company = $this->getCompany($companyCode);

        return view('company.items.item.create', [
            'companyCode' => $companyCode,
            'kategori' => Category::where('company_id', $company->id)->get(),
            'satuan' => $this->getAvailableSatuan($company->id),
        ]);
    }

    public function storeItem(Request $r, $companyCode)
    {
        $company = $this->getCompany($companyCode);

        $r->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'satuan_id' => [
                'required',
                Rule::exists('satuan', 'id')->where(function ($q) use ($company) {
                    $q->whereNull('company_id')->orWhere('company_id', $company->id);
                }),
            ],
            'is_main_ingredient' => 'nullable|boolean',
            'forecast_enabled' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($r, $company) {

            $item = Item::create([
                'company_id' => $company->id,
                'name' => $r->name,
                'category_id' => $r->category_id,
                'satuan_id' => $r->satuan_id,
                'is_main_ingredient' => $r->has('is_main_ingredient') ? 1 : 0,
                'forecast_enabled' => $r->has('forecast_enabled') ? 1 : 0,
            ]);

            $branchIds = CabangResto::where('company_id', $company->id)->pluck('id');

            $now = now();

            $rows = $branchIds->map(function ($branchId) use ($company, $item, $now) {
                return [
                    'company_id' => $company->id,
                    'cabang_resto_id' => $branchId,
                    'item_id' => $item->id,
                    'min_stock' => 0,
                    'max_stock' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->toArray();

            // pakai upsert biar aman kalau suatu saat kepanggil lagi
            DB::table('item_branch_min_stocks')->upsert(
                $rows,
                ['company_id', 'cabang_resto_id', 'item_id'],
                ['min_stock', 'max_stock', 'updated_at']
            );
        });

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
            'satuan' => $this->getAvailableSatuan($company->id),
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
            'satuan_id' => [
                'required',
                Rule::exists('satuan', 'id')->where(function ($q) use ($company) {
                    $q->whereNull('company_id')
                        ->orWhere('company_id', $company->id);
                }),
            ],
            'is_main_ingredient' => 'nullable|boolean',

            'forecast_enabled' => 'nullable|boolean',
        ]);

        $item->update([
            'name' => $r->name,
            'category_id' => $r->category_id,
            'satuan_id' => $r->satuan_id,
            'is_main_ingredient' => $r->has('is_main_ingredient') ? 1 : 0,

            'forecast_enabled' => $r->has('forecast_enabled') ? 1 : 0,
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

        DB::transaction(function () use ($company, $item) {

            DB::table('item_branch_min_stocks')
                ->where('company_id', $company->id)
                ->where('item_id', $item->id)
                ->delete();

            $item->delete();
        });

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
