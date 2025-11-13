<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StocksAdjustment extends Model
{
    protected $table = 'stocks_adjustmens';

    protected $fillable = [
        'warehouse_id',
        'categories_issues_id',
        'adjustment_date',
        'status',
        'note',
        'created_by',
        'posted_at',
        'updated_at',
        'stockId'
    ];

    public $timestamps = false;

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoriesIssues::class, 'categories_issues_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(StocksAdjustmentDetail::class, 'stocks_adjustmens_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stockId');
    }
}
