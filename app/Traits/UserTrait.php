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
            'province_id', 'city_id', 'district_id','open_id']);

        // 密码字段加密
        $insert_data['password'] = bcrypt($insert_data['password']);

        throw_on(!$insert_data['province_id'] || !$insert_data['city_id'], '地区不能为空');
        // 判断用户名是否重复
        throw_on(User::whereName($insert_data['name'])->first(), '用户名重复');
        throw_on(User::wherePhone($insert_data['phone'])->first(), '注册手机重复');

        // 创建
        return User::create($insert_data);
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