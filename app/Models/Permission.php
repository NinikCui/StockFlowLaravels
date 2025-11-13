<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'companies_id','code','resource','action','description',
        'scope','created_at','updated_at'
    ];

    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class, 'companies_id');
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id');
    }

    public function overrides()
    {
        return $this->hasMany(UserPermissionOverride::class, 'permission_id');
    }
}
