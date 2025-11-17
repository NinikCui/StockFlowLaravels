<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'warehouse';

    protected $fillable = [
        'cabang_resto_id',
        'name',
        'code',
        'warehouse_type_id'
    ];

    public $timestamps = false;

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'warehouse_id');
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'warehouse_id');
    }

    public function invFrom()
    {
        return $this->hasMany(InventoryTrans::class, 'warehouse_id_from');
    }

    public function invTo()
    {
        return $this->hasMany(InventoryTrans::class, 'warehouse_id_to');
    }

    public function stocksAdjustments()
    {
        return $this->hasMany(StocksAdjustment::class, 'warehouse_id');
    }

    public function demandDaily()
    {
        return $this->hasMany(DemandDaily::class, 'warehouse_id');
    }

    public function restockRecommendations()
    {
        return $this->hasMany(RestockRecommendation::class, 'warehouse_id');
    }
    public function type()
    {
        return $this->belongsTo(WarehouseType::class, 'warehouse_type_id');
    }
}
