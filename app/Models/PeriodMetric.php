<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodMetric extends Model
{
    protected $table = 'period_metrics';

    protected $fillable = [
        'cabang_resto_id',
        'period_start',
        'period_end',
        'waste_rate',
        'on_time_delivery',
        'reject_rate',
        'stockout_events',
        'service_level',
        'turnover',
        'extra_metrics',
        'computed_at'
    ];

    public $timestamps = false;

    protected $casts = [
        'extra_metrics' => 'array'
    ];

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }
}
