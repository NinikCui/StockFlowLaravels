<?php
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CabangController extends Controller{
    public function index(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $query = CabangResto::where('companies_id', $company->id);

        // Filter status
        if ($request->status == 'ACTIVE') {
            $query->where('is_active', true);
        } elseif ($request->status == 'INACTIVE') {
            $query->where('is_active', false);
        }

        // Search
        if ($request->search) {
            $q = strtolower($request->search);
            $query->where(function ($x) use ($q) {
                $x->whereRaw('LOWER(name) LIKE ?', ["%$q%"])
                ->orWhereRaw('LOWER(code) LIKE ?', ["%$q%"])
                ->orWhereRaw('LOWER(city) LIKE ?', ["%$q%"]);
            });
        }

        // Sort
        $sort = $request->sort ?? 'name';
        $query->orderBy($sort);

        $cabang = $query->get();
        return view('company.cabang.index', compact('cabang','companyCode'));
    }

    public function create($companyCode)
    {
        return view('company.cabang.create', compact('companyCode'));
    }

    public function store(Request $request, $companyCode)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'code' => 'required|max:100|unique:cabang_resto,code',
            'city' => 'required',
            'address' => 'nullable',
            'phone' => 'nullable|max:20',
        ]);

        $company = Company::where('code', $companyCode)->firstOrFail();

        CabangResto::create([
            'companies_id' => $company->id,
            ...$validated,
        ]);

        return redirect()->route('cabang.index', $companyCode)
                        ->with('success', 'Cabang berhasil ditambahkan!');
    }
}
