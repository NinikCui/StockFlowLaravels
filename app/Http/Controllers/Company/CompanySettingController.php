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
    public function general($companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        $settings = CompanySetting::where('company_id', $company->id)
            ->pluck('value', 'key')
            ->toArray();

        return view('company.settings.general', [
            'company' => $company,
            'settings' => $settings,
            'companyCode' => $companyCode,
        ]);
    }

    public function generalUpdate(Request $request, $companyCode)
    {
        $company = Company::where('code', $companyCode)->firstOrFail();

        // VALIDASI TEXT
        $data = $request->validate([
            'address' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:255',
            'established_year' => 'nullable|numeric|min:1900|max:' . date('Y'),
        ]);

        // VALIDASI LOGO FILE
        $request->validate([
            'logo' => 'nullable|file|mimes:jpg,png,jpeg,webp|max:2048',
        ], [
            'logo.mimes' => 'Logo harus berupa file JPG, JPEG, PNG, atau WEBP.',
            'logo.max'   => 'Ukuran logo maksimal 2 MB.',
        ]);

        // SIMPAN TEXT SETTING
        foreach ($data as $key => $value) {
            CompanySetting::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'key' => "general.$key",
                ],
                ['value' => $value]
            );
        }

        // === PROSES LOGO BARU ===
        if ($request->hasFile('logo')) {

            // AMBIL LOGO LAMA
            $old = CompanySetting::where('company_id', $company->id)
                ->where('key', 'general.logo')
                ->first();

            // HAPUS FILE LAMA
            if ($old && $old->value) {
                $oldPath = str_replace('/storage/', '', $old->value);

                if (\Storage::disk('public')->exists($oldPath)) {
                    \Storage::disk('public')->delete($oldPath);
                }
            }

            // SIMPAN LOGO BARU
            $path = $request->file('logo')->store(
                "company/{$company->id}/logo",
                'public'
            );

            // SIMPAN KE DATABASE
            CompanySetting::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'key' => "general.logo",
                ],
                [
                    'value' => "/storage/" . $path,
                ]
            );
        }

        return back()->with('success', 'Pengaturan General berhasil disimpan.');
    }



}
