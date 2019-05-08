<?php

namespace App\Traits;

use App\Models\User;

trait UserTrait
{
    /**
     * 创建新用户
     * @param array $data
     * @return mixed
     */
    public function userCreate(array $data)
    {
        // 过滤非法字段
        $insert_data = array_only($data, ['name', 'phone', 'password', 'shop_name',
            'province_id', 'city_id', 'district_id', 'open_id','mini_id']);

        // 密码字段加密
        $insert_data['password'] = bcrypt($insert_data['password']);
        // 判断公众号是否被绑定
        $open_id = array_get($data, 'open_id');
        throw_on($open_id && User::whereOpenId($open_id)->first(), '该微信已被绑定');
        // 判断小程序是否被绑定
        $mini_id = array_get($data, 'mini_id');
        throw_on($mini_id && User::where('mini_id',$mini_id)->first(), '该微信已被绑定');

        throw_on(!$insert_data['province_id'] || !$insert_data['city_id'], '地区不能为空');
        // 判断用户名是否重复
        throw_on(User::whereName($insert_data['name'])->first(), '用户名重复');
        throw_on(User::wherePhone($insert_data['phone'])->first(), '注册手机重复');

        // 创建
        return User::create($insert_data);
    }

    /**
     * 更新用户资料
     * @param int $user_id
     * @param array $data
     * @return mixed
     */
    public function userProfileUpdate(int $user_id, array $data)
    {
        $update_data = array_only($data, ['name', 'shop_name', 'province_id', 'city_id', 'district_id']);

        // 判断用户名是否重复
        throw_on(User::whereName($update_data['name'])->where('id','<>',$user_id)->first(), '用户名重复');

        $user = User::whereKey($user_id)->firstOrFail();

        $user->update($update_data);

        return $user;
    }

    /**
     * 忘记密码
     * @param string $name
     * @param string $phone
     * @param string $password
     * @return mixed
     */
    public function userPasswordUpdate(string $name, string $phone, string $password)
    {
        $user = User::where([
            'name' => $name,
            'phone' => $phone
        ])->first();

        throw_on(!$user, '用户不存在');

        return $user->update([
            'password' => bcrypt($password)
        ]);
    }
}