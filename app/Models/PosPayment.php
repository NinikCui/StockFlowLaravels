<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosPayment extends Model
{
    protected $table = 'pos_payments';

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    protected $fillable = [
        'pos_order_id',
        'method',
        'amount',
        'ref_number',
        'paid_at',
        'status',
        'note', 'paid_amount',
        'change_amount',
    ];

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }
}
