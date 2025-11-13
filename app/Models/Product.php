<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'code',
        'base_price',
        'is_active',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;

    public function boms()
    {
        return $this->hasMany(Bom::class, 'products_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'products_id');
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'products_id');
    }
}
