<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use DB;

class UserAddressController extends Controller
{

    /**
     * 收货地址列表
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ApiRequest $request)
    {
        $result = UserAddress::latest()->get();

        return ok($result);
    }

    /**
     * 获取城市
     * @param ApiRequest $request
     * @param ChinaArea $area
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCity(ApiRequest $request, ChinaArea $china_area)
    {
        $result = ChinaArea::whereParentId($china_area->id)->get(['id', DB::raw('name as text')]);

        return ok($result);
    }

    /**
     * 获取地区
     * @param ApiRequest $request
     * @param ChinaArea $area
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistrict(ApiRequest $request, ChinaArea $china_area)
    {
        $result = ChinaArea::whereParentId($china_area->id)->get(['id', DB::raw('name as text')]);

        return ok($result);
    }
}
