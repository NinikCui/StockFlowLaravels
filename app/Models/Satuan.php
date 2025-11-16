<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table = 'satuan';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'is_active',
    ];

    public $timestamps = true;

    public function items()
    {
        return $this->hasMany(Item::class, 'satuan_id');
    }

    public function conversionsFrom()
    {
        return $this->hasMany(UnitConversion::class, 'from_satuan_id');
    }

    public function conversionsTo()
    {
        return $this->hasMany(UnitConversion::class, 'to_satuan_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
