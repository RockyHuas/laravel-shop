<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded=['id'];
    protected $dates = ['reviewed_at'];
    public $timestamps = false;

    // 订单详情关联的产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 订单详情所属订单
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
