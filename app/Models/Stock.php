<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'code',
        'company_id',
        'warehouse_id',
        'item_id',
        'expired_at',
        'qty',
    ];

    public $timestamps = true;

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function invenTransDetails()
    {
        return $this->hasMany(InvenTransDetail::class, 'stocks_id');
    }

    public function productionIssuesDetails()
    {
        return $this->hasMany(ProductionIssuesDetail::class, 'stocks_id');
    }

    public function stocksAdjustmentDetail()
    {
        return $this->hasMany(StocksAdjustmentDetail::class, 'stocks_id');
    }

    public function scopeCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    // scope by item
    public function scopeItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public static function generateCode($warehouseId)
    {
        $warehouse = Warehouse::find($warehouseId);

        $prefix = 'STK-'.strtoupper($warehouse->code).'-';

        $last = self::where('warehouse_id', $warehouseId)
            ->where('code', 'LIKE', $prefix.'%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($last) {
            $num = intval(substr($last->code, strlen($prefix))) + 1;
        } else {
            $num = 1;
        }

        return $prefix.str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
