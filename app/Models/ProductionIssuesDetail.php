<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionIssuesDetail extends Model
{
    protected $table = 'production_issues_detail';

    protected $fillable = [
        'stocks_id',
        'production_issues_id',
        'qty_issues',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;

    public function productionIssue()
    {
        return $this->belongsTo(ProductionIssue::class, 'production_issues_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stocks_id');
    }
}
