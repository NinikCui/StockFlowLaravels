<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTrans extends Model
{
    protected $table = 'inven_trans';

    protected $fillable = [
        'cabang_id_to',
        'cabang_id_from',
        'trans_number',
        'trans_date',
        'status',
        'note',
        'reason',
        'created_by', 
        'posted_at',
        'updated_at',
    ];

    protected $casts = [
        'trans_date' => 'date',
    ];

    public $timestamps = false;

    public function cabangFrom()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_id_from');
    }

    public function cabangTo()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_id_to');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(InvenTransDetail::class, 'inven_trans_id');
    }
}
