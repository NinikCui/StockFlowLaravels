<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}