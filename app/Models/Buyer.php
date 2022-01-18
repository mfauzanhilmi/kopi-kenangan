<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $table = 'buyers';
    protected $fillable = ['gender', 'address', 'phone', 'user_id'];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function vouchers() {
        return $this->hasMany('App\Models\VoucherBuyer');
    }
}