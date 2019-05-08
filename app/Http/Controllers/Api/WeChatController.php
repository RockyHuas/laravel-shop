<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequest;

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

    // 小程序 open_id
    public function getOpenId(ApiRequest $request)
    {
        [$code] = $request->fields([$this,
            'code',
        ], true);

        $app = app('wechat.mini_program');

        $result = $app->auth->session($code);
        $result['token'] = '';

        if ($open_id = array_get($result, 'openid')) {
            $user = User::where('mini_id', $open_id)->first();
            // 如果用户存在
            if ($user) {
                $token = \Auth::guard('api')->fromUser($user);
                $result['token'] = $token;
            }
        }
        return ok($result);
    }
}
