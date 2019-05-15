<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendShipmentNotification
{
    protected $official_wechat;
    protected $template_id = 'xvlloJHqmjGPD6B1noVPR2SZXQj9Su6hUR3sgDZwVSU';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->official_wechat = app('wechat.official_account');
    }

    /**
     * Handle the event.
     *
     * @param  OrderShipped $event
     * @return void
     */
    public function handle(OrderShipped $event)
    {
        $order = $event->order;
        $order->loadMissing('user');

        if ($open_id = data_get($order, 'user.open_id')) {
            $this->official_wechat->template_message->send([
                'touser' => $open_id,
                'template_id' => $this->template_id,
                'url' => '',
                'data' => [
                    'first' => '您购买的订单已经发货啦，正快马加鞭向您飞奔而去。',
                    'keyword1' => $order->no,
                    'keyword2' => $order->updated_at,
                    'keyword3' => data_get($order,'ship_data.express_company'),
                    'keyword4' => data_get($order,'ship_data.express_no'),
                    'keyword5' => data_get($order,'address.address').' '.data_get($order,'address.contact_name')
                        .' '.data_get($order,'address.contact_phone'),
                    'remark'=>'请保持收件手机畅通！'
                ],
            ]);

        }
    }
}
