<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'item_id',
        'type',
        'qty',
        'reference',
        'notes',
        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }



    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'IN'           => 'Stok Masuk',
            'OUT'          => 'Stok Keluar',
            'TRANSFER_IN'  => 'Transfer Masuk',
            'TRANSFER_OUT' => 'Transfer Keluar',
            'ADJUSTMENT'   => 'Penyesuaian',
            default        => 'Unknown',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}