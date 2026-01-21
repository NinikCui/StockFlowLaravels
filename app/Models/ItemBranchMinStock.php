<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemBranchMinStock extends Model
{
    protected $table = 'item_branch_min_stocks';

    protected $fillable = [
        'company_id', 'cabang_resto_id', 'item_id', 'min_stock', 'max_stock',
    ];
}
