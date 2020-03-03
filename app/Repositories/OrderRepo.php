<?php

namespace App\Repositories;

use App\Models\ChinaArea;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddress;
use App\Traits\OrderTrait;

class OrderRepo
{
    use OrderTrait;

    /**
     * 创建订单
     * @param int $user_address_id
     * @param array $products
     * @param $total_amount
     * @param string $note
     * @return mixed
     * @throws \Throwable
     */
    public function createOrder(int $user_address_id, array $products, $total_amount, $note = '')
    {
        return \DB::transaction(function () use ($user_address_id, $products, $total_amount, $note) {

            $products = collect($products)->map(function ($item) {
                
                $product = Product::whereKey($item['product_id'])
                    ->where('on_sale', 1)->first();
                throw_on(!$product, '产品已下架');
                throw_on($product->stock < $item['amount'],'产品库存不足');
                $product->total_amount = $item['amount'];
                $product->total_price = $product->price * $item['amount'];
                return $product;
            });

            // 获取地址
            $province = '';
            $city = '';
            $address = UserAddress::with(['province', 'city', 'district'])->whereKey($user_address_id)->firstOrFail();
            $product = $products->first();
            $product->province && $province = ChinaArea::whereKey($product->province)->first();

            $product->city && $city = ChinaArea::whereKey($product->city)->first();
            // 创建订单
            $order = Order::create([
                'user_id' => \Auth::id(),
                'product_province' => $province ? $province->name : '全国',
                'product_city' => $city ? $city->name : '全部地区',
                'address' => [
                    'address' => data_get($address, 'province.name') . ' ' . data_get($address, 'city.name') . ' '
                        . data_get($address, 'district.name') . ' ' . $address['address'],
                    'contact_name' => $address['contact_name'],
                    'contact_phone' => $address['contact_phone'],
                ],
                'total_amount' => $products->sum('total_price'),
                'note' => $note
            ]);

            // 同步订单和产品的关系
            $products->each(function ($item) use ($order) {
                OrderItem::create([
                    'product_id' => $item->id,
                    'amount' => $item->total_amount,
                    'price' => $item->price,
                    'order_id' => $order->id
                ]);

                // 减少库存
                $item->decrement('stock',$item->total_amount);
            });

            return $order;
        });

    }
}