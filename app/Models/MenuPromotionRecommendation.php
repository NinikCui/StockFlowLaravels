<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPromotionRecommendation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date',
        'cabang_resto_id',
        'item_id',
        'product_id',
        'risk_score',
        'days_to_expired',
        'potential_usage',
        'reason',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
