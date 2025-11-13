<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    protected $fillable = [
        'roles_id',
        'permission_id',
        'effect',
        'cabang_resto_id'
    ];

    public $timestamps = false;

    // ----------------- RELASI -----------------

    public function role()
    {
        return $this->belongsTo(Role::class, 'roles_id');
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
