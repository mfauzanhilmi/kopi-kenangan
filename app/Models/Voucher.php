<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model {
    protected $table = 'vouchers';
    protected $fillable = [
        'code',
        'name', 
        'min_order',
        'image',
        'subtractor', 
        'start_date', 
        'description',
        'end_date'];

    public function voucherBuyers() {
        return $this->belongsToMany('App\Models\VoucherBuyer');
    }

    public function getImageAttribute($value) {
        return json_decode($value);
    }

    public function setImageAttribute($value) {
        $this->attributes['image'] = json_encode($value);
    }
}
