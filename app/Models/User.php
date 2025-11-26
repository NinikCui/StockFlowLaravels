<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    protected $table = 'users';

    use HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'username', 'email', 'password', 'phone', 'is_active',
        'created_at', 'updated_at', 'last_login_at',
    ];

    public $timestamps = false;

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
