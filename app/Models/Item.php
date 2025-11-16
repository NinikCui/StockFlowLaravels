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
        'mudah_rusak',
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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'suppliers_id');
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
        return $this->hasMany(Stock::class, 'items_id');
    }

    public function poDetails()
    {
        return $this->hasMany(PoDetail::class, 'items_id');
    }

    public function boms()
    {
        return $this->hasMany(Bom::class, 'items_id');
    }

    public function restockRecommendations()
    {
        return $this->hasMany(RestockRecommendation::class, 'items_id');
    }
}
