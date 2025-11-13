<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosShift extends Model
{
    protected $table = 'pos_shifts';

    protected $fillable = [
        'cabang_resto_id',
        'opened_by',
        'closed_by',
        'opened_at',
        'opening_cash',
        'closed_at',
        'closing_cash',
        'status',
        'note'
    ];

    public $timestamps = false;

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }

    public function openedByUser()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function orders()
    {
        return $this->hasMany(PosOrder::class, 'pos_shifts_id');
    }
}
