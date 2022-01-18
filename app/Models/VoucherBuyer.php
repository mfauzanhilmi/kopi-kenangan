<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherBuyer extends Model
{
    protected $table = 'voucher_buyers';
    protected $fillable = [
        'voucher_id',
        'buyer_id',
        'created_at',
        'updated_at'];

    public function vouchers() {
        return $this->hasMany('App\Models\Voucher');
    }

    public function buyer() {
        return $this->belongsTo('App\Models\Buyer');
    }
}