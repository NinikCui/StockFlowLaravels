<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name', 'code', 'timezone', 'tax_id', 'created_at', 'updated_at'
    ];

    public $timestamps = true;

    public function roles()
    {
        return $this->hasMany(Role::class, 'companies_id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'companies_id');
    }

    public function cabang()
    {
        return $this->hasMany(CabangResto::class, 'companies_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'companies_id');
    }
}
