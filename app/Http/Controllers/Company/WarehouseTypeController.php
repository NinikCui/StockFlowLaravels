<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Models\WarehouseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WarehouseTypeController extends Controller
{
    public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $types = WarehouseType::where('company_id', $company->id)->get();

        return view('company.warehouse_types.index', compact('companyCode', 'types'));
    }

    public function store(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:50'
        ]);

        WarehouseType::create([
            'company_id' => $company->id,
            'name' => strtoupper($data['name'])
        ]);

        return back()->with('success', 'Tipe gudang berhasil ditambahkan');
    }
}