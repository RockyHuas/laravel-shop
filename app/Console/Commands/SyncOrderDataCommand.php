<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class SyncOrderDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单数据同步';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('开始同步订单数据');
        Order::with('items.product')->chunk(2000,function($orders){
            $orders->each(function($order){
                $order->items->each(function($order_item){
                    $product=$order_item->product;
                    $order_item->update([
                        'product_title'=>data_get($product,'title'),
                        'product_sku_id'=>0
                    ]);
                });
            });
        });
    }
}
