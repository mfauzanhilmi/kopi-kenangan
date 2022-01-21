<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_products';
    protected $fillable = ['order_id', 'product_id', 'lots'];

    public function order() {
        return $this->belongsTo('App\Models\Order');
    }

    public function product() {
        return $this->belongsTo('App\Models\Product');
    }
}