<?php
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Category;
use App\Models\Company;
use App\Models\Item;
use App\Models\Role;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class ItemController extends Controller
{
    // LIST
    public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $items = Item::with(['kategori', 'satuan', 'supplier'])
            ->where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('company.item.index', compact('items', 'companyCode'));
    }

    // FORM CREATE
    public function create($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        return view('company.item.create', [
            'companyCode' => $companyCode,
            'categories' => Category::where('company_id', $company->id)->get(),
            'satuan'      => Satuan::where('company_id', $company->id)->get(),
            'suppliers'   => Supplier::where('company_id', $company->id)->get()
        ]);
    }

    // STORE
    public function store(Request $request, $companyCode)
    {
        $request->validate([
            'name'         => 'required|max:45',
            'category_id'  => 'required|exists:categories,id',
            'satuan_id'    => 'required|exists:satuan,id',
            'min_stock'    => 'required|integer|min:0',
            'max_stock'    => 'required|integer|min:0',
        ]);

        $company = Company::where('code', $companyCode)->firstOrFail();

        Item::create([
            'company_id'       => $company->id,
            'category_id'      => $request->category_id,
            'satuan_id'        => $request->satuan_id,
            'name'             => $request->name,
            'mudah_rusak'      => $request->mudah_rusak ?? false,
            'min_stock'        => $request->min_stock,
            'max_stock'        => $request->max_stock,
            'forecast_enabled' => $request->forecast_enabled ?? false,
            'suppliers_id'     => $request->suppliers_id,
        ]);

        return redirect()->route('item.index', $companyCode)
            ->with('success', 'Item berhasil ditambahkan.');
    }

    // FORM EDIT
    public function edit($companyCode, $id)
    {
        $item    = Item::findOrFail($id);
        $company = Company::where('code', $companyCode)->firstOrFail();

        return view('company.item.edit', [
            'item'        => $item,
            'companyCode' => $companyCode,
            'categories'  => Category::where('company_id', $company->id)->get(),
            'satuan'      => Satuan::where('company_id', $company->id)->get(),
            'suppliers'   => Supplier::where('company_id', $company->id)->get()
        ]);
    }

    // UPDATE
    public function update(Request $request, $companyCode, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'name'        => 'required|max:45',
            'category_id' => 'required',
            'satuan_id'   => 'required',
        ]);

        $item->update([
            'name'             => $request->name,
            'category_id'      => $request->category_id,
            'satuan_id'        => $request->satuan_id,
            'mudah_rusak'      => $request->mudah_rusak ?? false,
            'min_stock'        => $request->min_stock,
            'max_stock'        => $request->max_stock,
            'forecast_enabled' => $request->forecast_enabled ?? false,
            'suppliers_id'     => $request->suppliers_id,
        ]);

        return redirect()->route('item.index', $companyCode)
            ->with('success', 'Item berhasil diperbarui.');
    }

    // DELETE
    public function destroy($companyCode, $id)
    {
        Item::findOrFail($id)->delete();

        return redirect()->route('item.index', $companyCode)
            ->with('success', 'Item berhasil dihapus.');
    }
}