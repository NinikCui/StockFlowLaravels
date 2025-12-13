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

    public function isStockAvailableForOne($branchId)
    {
        foreach ($this->bomItems as $bom) {

            $requiredQty = $bom->qty_per_unit;

            $availableStock = Stock::where('item_id', $bom->item_id)
                ->whereIn('warehouse_id', function ($q) use ($branchId) {
                    $q->select('id')
                        ->from('warehouse')
                        ->where('cabang_resto_id', $branchId);
                })
                ->sum('qty');

            if ($availableStock < $requiredQty) {
                return false;
            }
        }

        return true;
    }
}
