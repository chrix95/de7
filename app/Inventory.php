<?php

namespace App;

use App\Category;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['name', 'categories_id', 'price', 'quantity', 'description'];

    public function category () {
        return $this->belongsTo(Category::class);
    }
}
