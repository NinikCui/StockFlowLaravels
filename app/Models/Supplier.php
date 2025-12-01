<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'company_id',
        'cabang_resto_id',
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

    public function supplierItems()
    {
        return $this->hasMany(SuppliersItem::class, 'suppliers_id');
    }

    public function suppliedItems()
    {
        return $this->belongsToMany(Item::class, 'suppliers_item', 'suppliers_id', 'items_id')
            ->withPivot(['price', 'min_order_qty', 'last_price_update'])
            ->withTimestamps();
    }

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }

    public function items()
    {
        return $this->hasManyThrough(
            Item::class,
            SuppliersItem::class,
            'id',
            'id',
        );
    }

    public function scores()
    {
        return $this->hasMany(SupplierScore::class, 'suppliers_id');
    }

    public function performanceCategory()
    {
        $score = $this->scores->first(); // score terbaru

        if (! $score) {
            return 'unknown';
        }

        if ($score->on_time_rate >= 90 && $score->reject_rate <= 10 && $score->price_variance <= 5) {
            return 'good';
        }

        if ($score->on_time_rate >= 70 && $score->reject_rate <= 20) {
            return 'average';
        }

        return 'poor';
    }
}
