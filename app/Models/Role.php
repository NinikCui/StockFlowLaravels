<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'company_id',
        'cabang_resto_id',
        'code',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class);
    }
}
