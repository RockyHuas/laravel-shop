<?php

namespace App\Traits;

use App\Models\Order;

trait OrderTrait
{
    /**
     * 订单查询
     * @param int $size
     * @param int $order_status
     * @return mixed
     */
    public function orderQuery(int $size = 2, int $order_status = 0)
    {
        return Order::with('items.product')
            ->when($order_status, function ($query, $value) {
                // 待支付
                if ($value == 1) $query->whereClosed(0)->whereNull('paid_at');
                // 待发货
                if ($value == 2) $query->whereClosed(0)->whereNotNull('paid_at')->where('ship_status', Order::SHIP_STATUS_PENDING);
                // 已完成
                if ($value == 3) $query->whereClosed(0)->whereNotNull('paid_at')->where('ship_status', Order::SHIP_STATUS_RECEIVED);
                // 已取消
                if ($value == 4) $query->whereClosed(1);
            })->latest()
            ->paginate($size);
    }
}