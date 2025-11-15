<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\CabangResto;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CompanySettingController extends Controller
{
    public function edit($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        // Ambil semua setting
        $settings = CompanySetting::where('companies_id', $company->id)
            ->pluck('value', 'key')
            ->toArray();

        return view('company.settings.edit', [
            'companyCode' => $companyCode,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $data = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'ppn_rate' => 'nullable|numeric|min:0|max:100',
            'service_charge' => 'nullable|numeric|min:0|max:100',
            'receipt_footer' => 'nullable|string|max:500',
        ]);

        foreach ($data as $key => $value) {
            CompanySetting::updateOrCreate(
                [
                    'companies_id' => $company->id,
                    'key' => "global.$key",
                ],
                ['value' => $value]
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Pengaturan perusahaan berhasil diperbarui.');
    }
}
