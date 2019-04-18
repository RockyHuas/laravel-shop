<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Models\Order;
use App\Repositories\OrderRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    protected $repo;

    public function __construct(OrderRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 查询订单
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ApiRequest $request)
    {
        [$size, $order_status] = $request->fields([$this,
            'size' => ['default' => 2],
            'order_status'=>['default'=>0]
        ], true);

        $result = $this->repo->orderQuery($size, $order_status);

        return ok($result);
    }

    /**
     * 创建新的订单
     * @param ApiRequest $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ApiRequest $request, Order $order)
    {
        [$user_address_id, $products, $total_amount] = $request->fields([$this,
            'user_address_id',
            'products' => ['type' => 'json'],
            'total_amount',
        ], true);

        $result = $this->repo->createOrder($user_address_id, $products, $total_amount);

        return ok($result);
    }

    /**
     * 取消订单
     * @param ApiRequest $request
     * @param Order $order
     * @return bool
     */
    public function cancel(ApiRequest $request, Order $order)
    {
        throw_on($order->user_id != \Auth::id(), '您不能取消他人的订单');

        $order->update(['closed' => 1]);

        return ok(true);
    }


}
