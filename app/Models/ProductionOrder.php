<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $table = 'production_orders';

    protected $fillable = [
        'order_number',
        'cabang_resto_id',
        'warehouse_id',
        'products_id',
        'qty_planned',
        'due_date',
        'status',
        'note',
        'created_by',
        'started_at',
        'completed_at',
        'updated_at',
        'created_at'
    ];

    public $timestamps = false;

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function issues()
    {
        return $this->hasMany(ProductionIssue::class, 'production_orders_id');
    }
}
