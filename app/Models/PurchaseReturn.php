<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $table = 'purchase_returns';

    protected $fillable = [
        'po_detail_id',
        'purchase_returns',
        'reason',
        'created_by',
        'qty_returned',
        'posted_at',
        'updated_at'
    ];

    public $timestamps = false;

    public function poDetail()
    {
        return $this->belongsTo(PoDetail::class, 'po_detail_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
