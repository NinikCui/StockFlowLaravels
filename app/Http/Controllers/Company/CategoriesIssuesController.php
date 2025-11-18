<?php
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\CategoriesIssues;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CategoriesIssuesController extends Controller
{
    public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $issues = CategoriesIssues::orderBy('name')->get();

        return view('company.settings.masalah.index', compact('issues', 'companyCode'));
    }

    public function create($companyCode)
    {
        return view('company.settings.masalah.create', compact('companyCode'));
    }

    public function store(Request $request, $companyCode)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'desc' => 'nullable|string|max:255',
        ]);
        
        CategoriesIssues::create($request->only('name', 'desc'));

        return redirect()->route('issues.index', $companyCode)
            ->with('success', 'Kategori masalah berhasil ditambahkan.');
    }

    public function edit($companyCode, $id)
    {
        $issue = CategoriesIssues::findOrFail($id);

        return view('company.settings.masalah.edit', compact('issue', 'companyCode'));
    }

    public function update(Request $request, $companyCode, $id)
    {
        $issue = CategoriesIssues::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:100',
            'desc' => 'nullable|string|max:255',
        ]);

        $issue->update($request->only('name', 'desc'));

        return redirect()->route('issues.index', $companyCode)
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($companyCode, $id)
    {
        CategoriesIssues::findOrFail($id)->delete();

        return redirect()->route('issues.index', $companyCode)
            ->with('success', 'Kategori berhasil dihapus.');
    }
}