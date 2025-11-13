<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionIssue extends Model
{
    protected $table = 'production_issues';

    protected $fillable = [
        'production_orders_id',
        'issue_datetime',
        'status',
        'note',
        'categories_issues_id',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_orders_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoriesIssues::class, 'categories_issues_id');
    }

    public function details()
    {
        return $this->hasMany(ProductionIssuesDetail::class, 'production_issues_id');
    }
}
