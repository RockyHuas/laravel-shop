<?php

use Illuminate\Database\Seeder;

class OrderNoSeeder extends Seeder
{
    public $description="批量更改订单号";
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $init_no = date('Ymd') . '0000';

        \App\Models\Order::get()->each(function ($order, $index) use ($init_no) {
            $order->update([
                'no'=> $init_no + $index
            ]);
        });
    }
}
