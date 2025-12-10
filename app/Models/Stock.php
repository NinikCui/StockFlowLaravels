<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'code',
        'company_id',
        'warehouse_id',
        'item_id',
        'expired_at',
        'qty',
    ];

    public $timestamps = true;

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function invenTransDetails()
    {
        return $this->hasMany(InvenTransDetail::class, 'stocks_id');
    }

    public function productionIssuesDetails()
    {
        return $this->hasMany(ProductionIssuesDetail::class, 'stocks_id');
    }

    public function stocksAdjustmentDetail()
    {
        return $this->hasMany(StocksAdjustmentDetail::class, 'stocks_id');
    }

    public function scopeCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    // scope by item
    public function scopeItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public static function generateCode($warehouseId)
    {
        $warehouse = Warehouse::find($warehouseId);

        $prefix = 'STK-'.strtoupper($warehouse->code).'-';

        $last = self::where('warehouse_id', $warehouseId)
            ->where('code', 'LIKE', $prefix.'%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($last) {
            $num = intval(substr($last->code, strlen($prefix))) + 1;
        } else {
            $num = 1;
        }

        return $prefix.str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function historyAdjustments()
    {
        return DB::table('stocks_adjustmens_detail AS d')
            ->join('stocks_adjustmens AS sa', 'sa.id', '=', 'd.stocks_adjustmens_id')
            ->leftJoin('users AS u', 'u.id', '=', 'sa.created_by')
            ->where('d.stocks_id', $this->id)
            ->get([
                'sa.adjustment_date AS date',
                DB::raw("'ADJUSTMENT' AS source"),
                DB::raw('(d.after_qty - d.prev_qty) AS diff'),
                'u.username AS user',
                'sa.note AS note',
            ]);
    }

    public function historyMovements()
    {
        return DB::table('stock_movements AS sm')
            ->leftJoin('users AS u', 'u.id', '=', 'sm.created_by')
            ->where('sm.stock_id', $this->id)
            ->get([
                'sm.created_at AS date',
                'sm.type AS source',
                'sm.qty AS diff',
                'u.username AS user',
                'sm.notes AS note',
            ]);
    }

    public function historyReceives()
    {
        return DB::table('po_receive_detail AS rd')
            ->join('po_receive AS pr', 'pr.id', '=', 'rd.po_receive_id')
            ->leftJoin('users AS u', 'u.id', '=', 'pr.received_by')
            ->where('rd.item_id', $this->item_id)
            ->get([
                'pr.received_at AS date',
                DB::raw("'PO RECEIVE' AS source"),
                'rd.qty_received AS diff',
                'u.username AS user',
                'rd.note AS note',
            ]);
    }

    public function historyTransfers()
    {
        $branchId = $this->warehouse->cabang_resto_id;

        return DB::table('inven_trans_detail AS itd')
            ->join('inven_trans AS it', 'it.id', '=', 'itd.inven_trans_id')
            ->leftJoin('users AS u', 'u.id', '=', 'it.created_by')
            ->where('itd.items_id', $this->item_id)
            ->get([
                'it.trans_date AS date',
                DB::raw("CONCAT('TRANSFER ', it.status) AS source"),
                DB::raw("CASE WHEN it.cabang_id_to = {$branchId} THEN itd.qty ELSE -itd.qty END AS diff"),
                'u.username AS user',
                'it.note AS note',
            ]);
    }
}
