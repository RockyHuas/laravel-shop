<?php

namespace App\Repositories;

class WeChatRepo
{
    /**
     * 生成微信菜单按钮
     * @return mixed
     */
    public function createMenu()
    {
        $app = app('wechat.official_account');

        // 菜单按钮
        $buttons = [
            [
                "type" => "miniprogram",
                "name" => "在线下单",
                "url" => "http://mp.weixin.qq.com",
                "appid" => "wx59db07f0641ebbb5",
                "pagepath" => "pages/index/index"
            ],
            [
                "name" => "关于我们",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "联系我们",
                        "url" => "http://wap.51lanxun.com/lx"
                    ],
                    [
                        "type" => "view",
                        "name" => "我的故事",
                        "url" => "http://wap.51lanxun.com/gs"
                    ]
                ],
            ],
            [
                "type" => "view",
                "name" => "付款账号",
                "url" => "http://wap.51lanxun.com/zh"
            ]
        ];

        return $app->menu->create($buttons);
    }
}