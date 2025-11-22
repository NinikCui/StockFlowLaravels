<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_order';

    protected $fillable = [
        'cabang_resto_id',
        'suppliers_id',
        'po_date',
        'status',
        'note',
        'ontime',
        'po_number',
        'expected_delivery_date',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'suppliers_id');
    }

    public function details()
    {
        return $this->hasMany(PoDetail::class, 'purchase_order_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
