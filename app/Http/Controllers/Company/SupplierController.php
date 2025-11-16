<?php
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Category;
use App\Models\Company;
use App\Models\Role;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    // LIST
    public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $suppliers = Supplier::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('company.supplier.index', compact('suppliers', 'companyCode'));
    }

    // FORM TAMBAH
    public function create($companyCode)
    {
        return view('company.supplier.create', compact('companyCode'));
    }

    // SIMPAN
    public function store(Request $request, $companyCode)
    {
        $request->validate([
            'name'   => 'required|max:100',
            'email'  => 'nullable|email|max:100',
            'phone'  => 'nullable|max:50',
        ]);

        $company = Company::where('code', $companyCode)->firstOrFail();

        Supplier::create([
            'company_id'   => $company->id,
            'name'         => $request->name,
            'contact_name' => $request->contact_name,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'address'      => $request->address,
            'city'         => $request->city,
            'notes'        => $request->notes,
            'is_active'    => true,
        ]);

        return redirect()->route('supplier.index', $companyCode)
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    // FORM EDIT
    public function edit($companyCode, $id)
    {
        $supplier = Supplier::findOrFail($id);

        return view('company.supplier.edit', compact('supplier', 'companyCode'));
    }

    // UPDATE
    public function update(Request $request, $companyCode, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name'   => 'required|max:100',
            'email'  => 'nullable|email|max:100',
            'phone'  => 'nullable|max:50',
        ]);

        $supplier->update([
            'name'         => $request->name,
            'contact_name' => $request->contact_name,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'address'      => $request->address,
            'city'         => $request->city,
            'notes'        => $request->notes,
            'is_active'    => $request->is_active ?? false,
        ]);

        return redirect()->route('supplier.index', $companyCode)
            ->with('success', 'Data supplier berhasil diperbarui.');
    }

    // DELETE
    public function destroy($companyCode, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('supplier.index', $companyCode)
            ->with('success', 'Supplier berhasil dihapus.');
    }
}