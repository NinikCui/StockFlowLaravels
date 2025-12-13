<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    protected $table = 'pos_order';

    protected $casts = [
        'receipt_items' => 'array',
    ];

    protected $fillable = [
        'cabang_resto_id',
        'order_datetime',
        'status',
        'order_number',
        'cashier_id',
        'pos_shifts_id',
        'table_no',
        'created_at',
        'updated_at',    'receipt_items',

    ];

    public $timestamps = false;

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }

    public function posShift()
    {
        return $this->belongsTo(PosShift::class, 'pos_shifts_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'pos_order_id');
    }

    public function payments()
    {
        return $this->hasMany(PosPayment::class, 'pos_order_id');
    }
}
