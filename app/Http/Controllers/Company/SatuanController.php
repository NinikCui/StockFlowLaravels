<?php
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Category;
use App\Models\Company;
use App\Models\Role;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SatuanController extends Controller
{
    // Daftar satuan
    public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $satuan  = Satuan::where('company_id', $company->id)
                    ->orderBy('name')
                    ->get();

        return view('company.satuan.index', compact('satuan', 'companyCode'));
    }

    // Form tambah
    public function create($companyCode)
    {
        return view('company.satuan.create', compact('companyCode'));
    }

    // Simpan satuan
    public function store(Request $request, $companyCode)
    {
        $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|max:20|unique:satuan,code',
        ]);

        $company = Company::where('code', $companyCode)->firstOrFail();

        Satuan::create([
            'company_id' => $company->id,
            'name'       => $request->name,
            'code'       => strtoupper($request->code),
            'is_active'  => true,
        ]);

        return redirect()->route('satuan.index', $companyCode)
                         ->with('success', 'Satuan berhasil ditambahkan.');
    }

    // Form edit
    public function edit($companyCode, $code)
    {
        $satuan = Satuan::where('code', $code)->firstOrFail();
        return view('company.satuan.edit', compact('satuan', 'companyCode'));
    }

    // Update satuan
    public function update(Request $request, $companyCode, $code)
    {
        $satuan = Satuan::where('code', $code)->firstOrFail();

        $request->validate([
            'name' => 'required|max:255',
        ]);

        $satuan->update([
            'name'      => $request->name,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->route('satuan.index', $companyCode)
                         ->with('success', 'Satuan berhasil diperbarui.');
    }

    // Hapus satuan
    public function destroy($companyCode, $code)
    {
        $satuan = Satuan::where('code', $code)->firstOrFail();
        $satuan->delete();

        return redirect()->route('satuan.index', $companyCode)
                         ->with('success', 'Satuan berhasil dihapus.');
    }
}