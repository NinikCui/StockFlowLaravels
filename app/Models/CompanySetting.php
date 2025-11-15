<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'companies_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'json', 
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'companies_id');
    }
}
