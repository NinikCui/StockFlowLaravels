<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boms extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'product_id',
        'item_id',
        'qty_per_unit',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
