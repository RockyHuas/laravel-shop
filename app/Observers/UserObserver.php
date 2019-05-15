<?php
/**
 * Created by RockyHuas.
 * User: Huangzb
 * Date: 2019/5/15
 * Time: 13:43
 */

namespace App\Observers;


use App\Models\Order;
use App\Models\ProductViewDetail;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserLoginDetail;

class UserObserver
{
    public function deleted(User $user)
    {
        \DB::transaction(function()use($user){
            // 删除用户订单
            Order::where('user_id',$user->id)->get()->each(function($order){
               $order->delete();
            });
            // 删除登录记录
            ProductViewDetail::whereUserId($user->id)->get()->each(function($product_view_detail){
                $product_view_detail->delete();
            });
            // 删除商品浏览记录
            UserLoginDetail::whereUserId($user->id)->get()->each(function($product_view_detail){
                $product_view_detail->delete();
            });
            // 删除用户收货地址
            UserAddress::whereUserId($user->id)->get()->each(function($user_address){
                $user_address->delete();
            });

        });
    }
}