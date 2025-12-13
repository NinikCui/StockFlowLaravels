<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBundle extends Model
{
    protected $fillable = [
        'company_id',
        'cabang_resto_id',
        'name',
        'bundle_price',
        'is_active',
    ];

    public function items()
    {
        return $this->hasMany(ProductBundleItem::class);
    }

    public function isStockAvailableForOne($branchId): bool
    {
        foreach ($this->items as $bundleItem) {
            $product = $bundleItem->product;

            if (! $product) {
                return false;
            }

            $productQtyInBundle = $bundleItem->qty;

            foreach ($product->bomItems as $bom) {
                $neededQty = $bom->qty_per_unit * $productQtyInBundle;

                $availableQty = \DB::table('stocks')
                    ->where('item_id', $bom->item_id)
                    ->whereIn('warehouse_id', function ($q) use ($branchId) {
                        $q->select('id')
                            ->from('warehouse')
                            ->where('cabang_resto_id', $branchId);
                    })
                    ->sum('qty');

                if ($availableQty < $neededQty) {
                    return false;
                }
            }
        }

        return true; // ✅ semua aman
    }
}
