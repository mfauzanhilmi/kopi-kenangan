<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'shops';
    protected $fillable = ['name', 'address', 'phone'];

    public function products() {
        return $this->hasMany('App\Models\ShopProduct');
    }
}