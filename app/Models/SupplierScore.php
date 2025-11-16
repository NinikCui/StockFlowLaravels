<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierScore extends Model
{
    protected $table = 'supplier_scores';

    protected $fillable = [
        'suppliers_id',
        'on_time_rate',
        'reject_rate',
        'avg_quality',
        'price_variance',
        'notes',
        'calculated_at',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'suppliers_id');
    }
}
