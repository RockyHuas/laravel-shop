<?php
/**
 * Created by RockyHuas.
 * User: Huangzb
 * Date: 2019/5/15
 * Time: 13:43
 */

namespace App\Observers;


use App\Models\Order;
use App\Models\OrderItem;

class OrderObserver
{
    public function deleted(Order $order)
    {
        \DB::transaction(function () use ($order) {
            // 删除订单详情
            OrderItem::whereOrderId($order->id)->get()->each(function ($order_item) {
                $order_item->delete();
            });
        });
    }
}