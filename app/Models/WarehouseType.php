<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseType extends Model
{
    protected $fillable = [
        'company_id',
        'name',
    ];

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'warehouse_type_id');
    }
}