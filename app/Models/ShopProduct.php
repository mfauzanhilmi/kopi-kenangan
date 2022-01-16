<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProduct extends Model
{
    protected $table = 'shop_products';
    protected $fillable = ['shop_id', 'product_id'];

    public function shop() {
        return $this->belongsTo('App\Models\Shop');
    }

    public function product() {
        return $this->belongsTo('App\Models\Product');
    }
}