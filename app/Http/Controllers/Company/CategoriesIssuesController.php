<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CategoriesIssues;
use App\Models\Company;
use Illuminate\Http\Request;

class CategoriesIssuesController extends Controller
{
    public function index($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $issues = CategoriesIssues::where('company_id', $company->id)->orderBy('name')->get();

        return view('company.settings.masalah.index', compact('issues', 'companyCode'));
    }

    public function store(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();
        $request->validate([
            'name' => 'required|string|max:100',
            'desc' => 'nullable|string|max:255',
        ]);

        CategoriesIssues::create([
            'name' => $request->name,
            'desc' => $request->desc,
            'company_id' => $company->id,
        ]

        );

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
            'name' => 'required|string|max:100',
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
