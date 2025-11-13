<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'companies_id', 'name', 'code', 'cabang_resto_id'
    ];

    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class, 'companies_id');
    }

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'roles_id');
    }

    public function permissions()
    {
        return $this->hasMany(RolePermission::class, 'roles_id');
    }
}
