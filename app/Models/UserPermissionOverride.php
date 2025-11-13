<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermissionOverride extends Model
{
    protected $table = 'user_permission_overrides';

    protected $fillable = [
        'users_id',
        'permission_id',
        'effect',
        'cabang_resto_id'
    ];

    public $timestamps = false;

    // ----------------- RELASI -----------------

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function cabang()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }
}
