<?php

namespace App\Traits;

use App\Models\UserAddress;

trait UserAddressTrait
{
    /**
     * 用户收货地址列表
     * @return mixed
     */
    public function userAddressQuery()
    {
        $user = \Auth::user();
        $addresses = UserAddress::whereUserId($user->id)->latest()->get();

        return $addresses->map(function ($item) use ($user) {
            $item->default = 0;
            if ($item->id == $user->user_address_id) {
                $item->default = 1;
            }
            return $item;
        });
    }

    /**
     * 创建用户收货地址
     * @param array $data
     * @return mixed
     */
    public function userAddressCreate(array $data)
    {
        // 判断地址是否存在
        throw_on(UserAddress::where([
            'province_id' => array_get($data, 'province_id', 0),
            'city_id' => array_get($data, 'city_id', 0),
            'district_id' => array_get($data, 'district_id', 0),
            'address' => array_get($data, 'address'),
            'contact_name' => array_get($data, 'name'),
            'contact_phone' => array_get($data, 'phone'),
            'user_id' => \Auth::id()
        ])->first(), '存在该地址，请确认');

        // 开始新增
        $user_address = UserAddress::create([
            'province_id' => array_get($data, 'province_id'),
            'city_id' => array_get($data, 'city_id'),
            'district_id' => array_get($data, 'district_id'),
            'address' => array_get($data, 'address'),
            'contact_name' => array_get($data, 'name'),
            'contact_phone' => array_get($data, 'phone'),
            'user_id' => \Auth::id()
        ]);

        // 更新用户的默认收货地址
        if (array_get($data, 'default')) {
            \Auth::user()->update(['user_address_id' => $user_address->id]);
        }

        return $user_address;
    }

    /**
     * 更新收货地址
     * @param UserAddress $user_address
     * @param array $data
     * @return UserAddress|bool
     */
    public function userAddressUpdate(UserAddress $user_address, array $data)
    {
        // 判断地址是否存在
        throw_on(UserAddress::where([
            'province_id' => array_get($data, 'province_id'),
            'city_id' => array_get($data, 'city_id'),
            'district_id' => array_get($data, 'district_id'),
            'address' => array_get($data, 'address'),
            'contact_name' => array_get($data, 'name'),
            'contact_phone' => array_get($data, 'phone'),
            'user_id' => \Auth::id(),
        ])->where('id', '<>', $user_address->id)->first(), '存在该地址，请确认');

        // 开始新增
        $user_address->update([
            'province_id' => array_get($data, 'province_id'),
            'city_id' => array_get($data, 'city_id'),
            'district_id' => array_get($data, 'district_id'),
            'address' => array_get($data, 'address'),
            'contact_name' => array_get($data, 'name'),
            'contact_phone' => array_get($data, 'phone'),
            'user_id' => \Auth::id()
        ]);

        // 更新用户的默认收货地址
        if (array_get($data, 'default')) {
            \Auth::user()->update(['user_address_id' => $user_address->id]);
        }

        return $user_address;
    }


    /**
     * 删除收货地址
     * @param UserAddress $user_address
     * @return bool
     * @throws \Exception
     */
    public function userAddressDelete(UserAddress $user_address)
    {
        $user = \Auth::user();
        if ($user->user_address_id == $user_address->id) {
            $user->update(['user_address_id' => 0]);
        }

        // 删除用户收货地址
        $user_address->delete();

        return true;
    }

    /**
     * 设置默认收货地址
     * @param UserAddress $user_address
     * @return bool
     */
    public function userAddressSetDefault(UserAddress $user_address)
    {
        \Auth::user()->update(['user_address_id' => $user_address->id]);

        return true;
    }
}