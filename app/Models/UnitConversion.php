<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitConversion extends Model
{
    protected $fillable = [
        'from_satuan_id',
        'to_satuan_id',
        'factor',
        'is_active',
    ];

    public function fromSatuan()
    {
        return $this->belongsTo(Satuan::class, 'from_satuan_id');
    }

    public function toSatuan()
    {
        return $this->belongsTo(Satuan::class, 'to_satuan_id');
    }
}
