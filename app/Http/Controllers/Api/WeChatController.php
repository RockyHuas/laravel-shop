<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WeChatController extends Controller
{
    /**
     * 生成扫描二维码
     * @return \Illuminate\Http\JsonResponse
     */
    public function scanUrl()
    {
        $wechat = app('wechat.official_account');

        $result = $wechat->qrcode->temporary('foo', 600);
        $qrcodeUrl = $wechat->qrcode->url($result['ticket']);

        return ok($qrcodeUrl);
    }
}
