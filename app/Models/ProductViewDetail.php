<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductViewDetail extends Model
{
    protected $guarded = ['id'];

    // 关联的用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联的产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
