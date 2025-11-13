<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CabangResto extends Model
{
    protected $table = 'cabang_resto';

    protected $fillable = [
        'companies_id','name','code','address','city','phone',
        'is_active','latitude','longitude','manager_user_id',
        'created_at','updated_at'
    ];

    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class, 'companies_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'cabang_resto_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'cabang_resto_id');
    }
}
