<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestockRecommendation extends Model
{
    protected $table = 'restock_recomendations';

    protected $fillable = [
        'date',
        'warehouse_id',
        'items_id',
        'method',
        'recommended_qty',
        'safety_stock',
        'confidence',
        'reason',
        'review_status',
        'created_at'
    ];

    public $timestamps = false;

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'items_id');
    }
}
