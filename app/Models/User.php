<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'username','email','password','phone','is_active',
        'created_at','updated_at','last_login_at','roles_id'
    ];

    public $timestamps = false;

    public function role()
    {
        return $this->belongsTo(Role::class, 'roles_id');
    }

    public function overrides()
    {
        return $this->hasMany(UserPermissionOverride::class, 'users_id');
    }

    public function inventoryTransCreated()
    {
        return $this->hasMany(InventoryTrans::class, 'created_by');
    }

    public function posShiftsOpened()
    {
        return $this->hasMany(PosShift::class, 'opened_by');
    }

    public function posShiftsClosed()
    {
        return $this->hasMany(PosShift::class, 'closed_by');
    }

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class, 'cashier_id');
    }
}
