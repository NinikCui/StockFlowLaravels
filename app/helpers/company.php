<?php

use App\Models\CompanySetting;

if (!function_exists('company_setting')) {
    function company_setting($companyId, $key, $default = null)
    {
        $row = CompanySetting::where('companies_id', $companyId)
            ->where('key', $key)
            ->first();

        return $row?->value ?? $default;
    }
}
