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
                // 已发货
                if ($value == 3) $query->whereClosed(0)->whereNotNull('paid_at')->where('ship_status', Order::SHIP_STATUS_DELIVERED);
                // 已完成
                if ($value == 4) $query->whereClosed(0)->whereNotNull('paid_at')->where('ship_status', Order::SHIP_STATUS_RECEIVED);
                // 已取消
                if ($value == 5) $query->whereClosed(1);
            })
            ->whereUserId(\Auth::id())
            ->latest()
            ->paginate($size);
    }

    /**
     * 根据状态统计订单数量
     * @return array
     */
    public function statByStatus()
    {
        $user_id=\Auth::id();
        // 待支付
        $unpaid_count=Order::whereUserId($user_id)->whereClosed(0)->whereNull('paid_at')->count();
        // 待发货
        $unshiped_count=Order::whereUserId($user_id)->whereClosed(0)->whereNotNull('paid_at')->where('ship_status', Order::SHIP_STATUS_PENDING)->count();
        // 已发货
        $shiped_count=Order::whereUserId($user_id)->whereClosed(0)->whereNotNull('paid_at')->where('ship_status', Order::SHIP_STATUS_DELIVERED)->count();
        // 已完成
        $completed_count=Order::whereUserId($user_id)->whereClosed(0)->whereNotNull('paid_at')->where('ship_status', Order::SHIP_STATUS_RECEIVED)->count();
        // 已取消
        $canceled_count=Order::whereUserId($user_id)->whereClosed(1)->count();

        return compact('unpaid_count','unshiped_count','shiped_count','completed_count','canceled_count');
    }


}