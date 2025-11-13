<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table = 'satuan';

    protected $fillable = ['name', 'symbol'];

    public $timestamps = false;

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
}
