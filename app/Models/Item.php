<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = [
        'company_id',
        'category_id',
        'satuan_id',
        'name',
        'is_main_ingredient',
        'min_stock',
        'max_stock',
        'forecast_enabled',
        'suppliers_id',
    ];

    public $timestamps = false;

    public function kategori()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'suppliers_item', 'items_id', 'suppliers_id')
            ->withPivot(['price', 'min_order_qty', 'last_price_update'])
            ->withTimestamps();
    }

    public function demandDaily()
    {
        return $this->hasMany(DemandDaily::class, 'items_id');
    }

    public function unitConversions()
    {
        return $this->hasMany(UnitConversion::class, 'items_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'item_id');
    }

    public function poDetails()
    {
        return $this->hasMany(PoDetail::class, 'items_id');
    }

    public function boms()
    {
        return $this->hasMany(Boms::class, 'item_id');
    }

    public function restockRecommendations()
    {
        return $this->hasMany(RestockRecommendation::class, 'items_id');
    }

    public function receiveDetails()
    {
        return $this->hasMany(PoReceiveDetail::class, 'item_id');
    }
}
