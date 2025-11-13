<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriesIssues extends Model
{
    protected $table = 'categories_issues';

    protected $fillable = [
        'name',
        'desc'
    ];

    public $timestamps = false;

    public function productionIssues()
    {
        return $this->hasMany(ProductionIssue::class, 'categories_issues_id');
    }

    public function stocksAdjustments()
    {
        return $this->hasMany(StocksAdjustment::class, 'categories_issues_id');
    }
}
