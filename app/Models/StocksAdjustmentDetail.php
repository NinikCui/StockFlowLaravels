<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StocksAdjustmentDetail extends Model
{
    protected $table = 'stocks_adjustmens_detail';

    protected $fillable = [
        'stocks_adjustmens_id',
        'stocks_id',
        'prev_qty',
        'after_qty'
    ];

    public $timestamps = false;

    public function adjustment()
    {
        return $this->belongsTo(StocksAdjustment::class, 'stocks_adjustmens_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stocks_id');
    }
}
