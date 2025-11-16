<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'company_id',
        'name',
        'contact_name',
        'phone',
        'email',
        'address',
        'city',
        'is_active',
        'notes',
    ];

    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'suppliers_id');
    }

    public function suppliedItems()
    {
        return $this->belongsToMany(Item::class, 'suppliers_item', 'suppliers_id', 'items_id')
            ->withPivot(['price', 'min_order_qty', 'last_price_update'])
            ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'suppliers_id');
    }

    public function scores()
    {
        return $this->hasMany(SupplierScore::class, 'suppliers_id');
    }
}
