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
            'province_id', 'city_id', 'district_id']);

        // 密码字段加密
        $insert_data['password'] = bcrypt($insert_data['password']);

        // 判断用户名是否重复
        throw_on(User::wherePhone($insert_data['phone'])->first(), '注册用户重复');

        // 创建
        return User::create($insert_data);
    }
}