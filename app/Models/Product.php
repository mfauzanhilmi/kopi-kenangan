<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['name', 'price', 'image'];

    public function getImageAttribute($value)
    {
        return json_decode($value);
    }

    public function setImageAttribute($value)
    {
        $this->attributes['image'] = json_encode($value);
    }
}