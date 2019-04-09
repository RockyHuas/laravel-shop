<?php

namespace App\Repositories\Api;

use Auth;

class AuthorizationsRepo
{
    protected $expires_in = 24 * 60 * 14; // 过期时间，单位：minutes

    /**
     * 用户登录
     * @param string $name
     * @param string $password
     * @return array
     */
    public function login(string $name, string $password)
    {
        // 组装参数
        $credentials = compact('name', 'password');

        // 设置 token 过期时间
        Auth::guard('api')->factory()->setTTL($this->expires_in);

        // 获取 token
        $token = Auth::guard('api')->attempt($credentials);

        // 如果 token 不存在，尝试通过手机号码登录
        !$token && $token=Auth::guard('api')->attempt(['phone'=>$name,'password'=>$password]);

        // 如果不存在 token，则用户名或者密码错误
        throw_on(!$token, '用户名或者密码错误');

        return $this->returnWithToken($token);
    }

    /**
     * 刷新 token
     * @return array
     */
    public function reflesh_token()
    {
        // 设置 token 过期时间
        Auth::guard('api')->factory()->setTTL($this->expires_in);

        // 获取 token
        $token = Auth::guard('api')->refresh();

        return $this->returnWithToken($token);

    }

    /**
     * 删除 token
     * @return bool
     */
    public function delete_token()
    {
        Auth::guard('api')->logout();

        return true;
    }

    /**
     * 格式化返回的数据
     * @param string $token
     * @return array
     */
    protected function returnWithToken(string $token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->expires_in * 60 //单位秒
        ];
    }
}