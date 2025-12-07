<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'code',
        'base_price',
        'is_active',
    ];

    public $timestamps = true;

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'products_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'products_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bomItems()
    {
        return $this->hasMany(Boms::class, 'product_id');
    }
}
