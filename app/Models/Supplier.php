<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'companies_id',
        'name',
        'code',
        'contact_name',
        'phone',
        'email',
        'address',
        'rating',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class, 'companies_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'suppliers_id');
    }

    public function supplierItems()
    {
        return $this->hasMany(SuppliersItem::class, 'suppliers_id');
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
