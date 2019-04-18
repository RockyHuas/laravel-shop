<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use App\Repositories\UserAddressRepo;
use DB;

class UserAddressController extends Controller
{
    protected $repo;

    public function __construct(UserAddressRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 获取用户收货地址
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ApiRequest $request)
    {
        $result = $this->repo->userAddressQuery();

        return ok($result);
    }

    /**
     * 创建收货地址
     * @param ApiRequest $request
     * @param UserAddress $user_address
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ApiRequest $request, UserAddress $user_address)
    {
        $params = $request->fields([$this,
            'province',
            'city',
            'district',
            'address',
            'name',
            'phone',
            'default' => ['default' => 0]
        ]);

        $result = $this->repo->userAddressCreate($params);

        return ok($result);
    }

    /**
     * 更新收货人地址
     * @param ApiRequest $request
     * @param UserAddress $user_address
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ApiRequest $request, UserAddress $user_address)
    {
        $params = $request->fields([$this,
            'province',
            'city',
            'district',
            'address',
            'name',
            'phone',
            'default' => ['default' => 0]
        ]);

        $result = $this->repo->userAddressUpdate($user_address, $params);

        return ok($result);
    }

    /**
     * 删除用户收货地址
     * @param ApiRequest $request
     * @param UserAddress $user_address
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(ApiRequest $request, UserAddress $user_address)
    {
        $result = $this->repo->userAddressDelete($user_address);

        return ok($result);
    }

    /**
     * 设置为默认收货地址
     * @param ApiRequest $request
     * @param UserAddress $user_address
     * @return \Illuminate\Http\JsonResponse
     */
    public function setDefault(ApiRequest $request, UserAddress $user_address)
    {
        $result=$this->repo->userAddressSetDefault($user_address);

        return ok($result);
    }


}
