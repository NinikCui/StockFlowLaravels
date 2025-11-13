<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitConversion extends Model
{
    protected $table = 'unit_conversions';

    protected $fillable = [
        'items_id',
        'from_satuan_id',
        'to_satuan_id'
    ];

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class, 'items_id');
    }

    public function fromSatuan()
    {
        return $this->belongsTo(Satuan::class, 'from_satuan_id');
    }

    public function toSatuan()
    {
        return $this->belongsTo(Satuan::class, 'to_satuan_id');
    }
}
