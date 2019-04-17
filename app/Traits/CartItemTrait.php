<?php

namespace App\Traits;


use App\Models\CartItem;
use Auth;

trait CartItemTrait
{


    /**
     * 购物车商品列表
     * @return CartItem[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function cartItemQuery()
    {
        $user_id = Auth::id();
        return CartItem::with('product')
            ->where('user_id', $user_id)->get();
    }

    /**
     * 添加商品到购物车
     * @param int $product_id
     * @param int $amount
     * @return CartItem
     */
    public function cartItemAdd(int $product_id, int $amount, int $add = 1)
    {
        $user = Auth::user();
        // 从数据库中查询该商品是否已经在购物车中
        if ($item = CartItem::where([
            'product_id' => $product_id,
            'user_id' => $user->id
        ])->first()) {
            $amount=$add ? $item->amount + $amount : $amount;
            // 如果存在则直接叠加商品数量
            $item->update([
                'amount' => $amount,
            ]);
        } else {
            // 否则创建一个新的购物车记录
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->product()->associate($product_id);
            $item->save();
        }

        return $item;
    }


    /**
     * 删除购物车商品
     * @param $product_ids
     * @return mixed
     */
    public function cartItemRemove(array $product_ids)
    {
        $user_id = Auth::id();

        if ($product_ids) {
            return CartItem::where('user_id', $user_id)
                ->whereIn('product_id', $product_ids)->delete();
        }
        return true;
    }
}