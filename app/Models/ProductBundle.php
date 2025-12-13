<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBundle extends Model
{
    protected $fillable = [
        'company_id',
        'cabang_resto_id',
        'name',
        'bundle_price',
        'is_active',
    ];

    public function items()
    {
        return $this->hasMany(ProductBundleItem::class);
    }
}
