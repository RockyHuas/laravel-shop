<?php

namespace App\Repositories;
;

use App\Models\Product;
use App\Traits\CartItemTrait;

class CartRepo
{
    use CartItemTrait;

    /**
     * 修改商品
     * @param int $product_id
     * @param int $amount
     * @param int $add
     * @return \App\Models\CartItem
     */
    public function changeProducts(int $product_id, int $amount, int $add=1)
    {
        //检查数据异常
        $this->checkData($product_id, $amount);

        $cart_item = $this->cartItemAdd($product_id, $amount,$add);

        return $cart_item;
    }

    /**
     * 检查数据异常
     * @param int $product_id
     * @param int $amount
     */
    protected function checkData(int $product_id, int $amount)
    {
        $product=Product::whereKey($product_id)->first();

        throw_on(!$product,'该商品不存在');

        throw_on(!$product->on_sale,'该商品未上架');

        throw_on(!$product->stock,'该商品已售完');

        throw_on($product->stock < $amount,'该商品库存不足');
    }
}
