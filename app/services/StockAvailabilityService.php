<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class StockAvailabilityService
{
    protected array $stockMap = [];

    public function __construct($branchId)
    {
        $this->stockMap = Stock::select('item_id', DB::raw('SUM(qty) as total'))
            ->whereIn('warehouse_id', function ($q) use ($branchId) {
                $q->select('id')
                    ->from('warehouse')
                    ->where('cabang_resto_id', $branchId);
            })
            ->groupBy('item_id')
            ->pluck('total', 'item_id')
            ->toArray();
    }

    public function hasEnough($itemId, $requiredQty)
    {
        return ($this->stockMap[$itemId] ?? 0) >= $requiredQty;
    }
}
