<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = ['name', 'code'];

    public $timestamps = false;

    public function items()
    {
        return $this->hasMany(Item::class, 'kategori_id');
    }
}
