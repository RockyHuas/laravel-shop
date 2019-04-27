<?php

namespace App\Http\Controllers;

use App\Events\WechatScanLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeChatController extends Controller
{
    public function serve()
    {
        $app = app('wechat.official_account');
        Log::info('进入 serve：');
        $app->server->push(function ($message) {
            if ($message['Event'] === 'SCAN') {
                Log::info('进入：' . json_encode($message));
                // 获取用户的 $openid
                $openid = $message['FromUserName'];
                Log::info('open_id：' . $openid);
                $user = User::where('open_id', $openid)->first();

                // 如果用户存在
                if ($user) {
                    Log::info('用户存在，登录');
                    $token = \Auth::guard('api')->fromUser($user);

                    // 广播扫码登录的消息，以便前端处理
                    event(new WechatScanLogin($openid,'Bearer ' . $token));

                    Log::info('登录成功了');
                    return '登录成功！';
                } else { // 用户不存在,返回 open_id
                    Log::info('用户不存在，创建');
                    event(new WechatScanLogin($openid));
                    return '请绑定用户';
                }
            } else {
                return true;
            }
        }, \EasyWeChat\Kernel\Messages\Message::EVENT);

        return $app->server->serve();
    }
}
