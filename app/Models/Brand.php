<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded=['id'];

    // 关联的产品
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
