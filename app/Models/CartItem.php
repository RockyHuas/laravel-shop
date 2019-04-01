<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded=['id'];
    public $timestamps = false;

    // 所属的用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 所属的产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
