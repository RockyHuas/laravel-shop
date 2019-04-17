<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Models\ChinaArea;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AreaController extends Controller
{
    /**
     * 获取省份
     * @param ApiRequest $request
     * @param ChinaArea $area
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProvince(ApiRequest $request, ChinaArea $area)
    {
        $result = ChinaArea::whereParentId('86')->get(['id', DB::raw('name as text')]);

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
