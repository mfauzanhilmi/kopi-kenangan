<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// model shop
class Shop extends Model
{
    protected $table = 'shops';
    protected $fillable = ['name', 'address', 'phone'];

    public function products() {
        return $this->hasMany('App\Models\ShopProduct');
    }
}