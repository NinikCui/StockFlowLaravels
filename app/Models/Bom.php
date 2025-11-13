<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    protected $table = 'boms';

    protected $fillable = [
        'products_id',
        'items_id',
        'qty_per_unit'
    ];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'items_id');
    }
}
