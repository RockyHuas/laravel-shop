<?php

use Illuminate\Database\Seeder;

class OrderAreaSeeder extends Seeder
{
    public $description="订单地址";
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       \App\Models\Order::chunk(100, function ($orders) {
           $orders->each(function($order){
               $order->loadMissing('items.product');
               $province = '';
               $city = '';
               $product =data_get($order,'items.0.product');
               $product->province && $province = \App\Models\ChinaArea::whereKey($product->province)->first();

               $product->city && $city = \App\Models\ChinaArea::whereKey($product->city)->first();
               // 创建订单
               $order->update([
                  'product_province'=>$province ? $province->name :'全国',
                  'product_city'=>$city ? $city->name :'全部地区',
              ]);
           });
       });
    }
}
