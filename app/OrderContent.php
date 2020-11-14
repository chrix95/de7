<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderContent extends Model
{
    protected $fillable = ['order_reference', 'product_name', 'product_quantity', 'product_price', 'total', 'categoryId', 'productId'];

    protected $hidden = [];

    public function order() {
        return $this->belongsTo(Order::class, 'order_reference', 'order_reference');
    }
}
