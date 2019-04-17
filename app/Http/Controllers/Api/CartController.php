<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequest;
use App\Repositories\CartRepo;

class CartController extends Controller
{
    protected $repo;

    public function __construct(CartRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 获取购物车商品
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(ApiRequest $request)
    {
        $result = $this->repo->cartItemQuery();

        return ok($result);
    }

    /**
     * 添加商品
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addProducts(ApiRequest $request)
    {
        [$product_id,$amount] = $request->fields([$this,
            'product_id',
            'amount'
        ], true);

        $result = $this->repo->changeProducts($product_id,$amount);

        return ok($result);
    }

    /**
     * 修改商品
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeProducts(ApiRequest $request)
    {
        [$product_id,$amount] = $request->fields([$this,
            'product_id',
            'amount'
        ], true);

        $result = $this->repo->changeProducts($product_id,$amount,0);

        return ok($result);
    }

    /**
     * 删除购物车商品
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProducts(ApiRequest $request)
    {
        [$product_ids] = $request->fields([$this,
            'product_ids'=>['type'=>'split'],
        ], true);

        $result = $this->repo->cartItemRemove($product_ids);

        return ok($result);
    }

}
