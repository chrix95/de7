<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['name'];

    protected $with = [];

    public function inventory () {
        return $this->hasMany(Inventory::class);
    }

}
