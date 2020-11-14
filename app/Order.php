<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'name', 'phone', 'email', 'address', 'order_reference', 'payment_reference', 'amount', 'amount_paid', 'status'];

    protected $hidden = ['created_at', 'updated_at', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function orderContent () {
        return $this->hasMany(OrderContent::class, 'order_reference', 'order_reference');
    }
}
