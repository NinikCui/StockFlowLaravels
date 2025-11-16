<?php
use App\Models\CompanySetting;

if (! function_exists('setting')) {
    function setting($key, $default = null, $companyId = null)
    {
        $companyId = $companyId ?: session('role.company.id');

        if (! $companyId) {
            return $default;
        }

        $record = CompanySetting::where('companies_id', $companyId)
                ->where('key', $key)
                ->first();

        return $record->value ?? $default;
    }
}