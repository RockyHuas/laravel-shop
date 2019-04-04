<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChinaArea;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    use ModelForm;

    // 获取省份
    public function province()
    {
        $province = ChinaArea::whereParentId('86')->get(['id', DB::raw('name as text')]);

        // 追加一个"全国"的选项进去
        $province->prepend(['id' => 0, 'text' => '全部']);

        return response()->json($province);
    }

    // 获取城市
    public function city(Request $request)
    {
        $province_id = $request->get('q');

        // 如果是 0
        if (!$province_id) return response()->json([]);

        $city = ChinaArea::whereParentId($province_id)->get(['id', DB::raw('name as text')]);

        // 追加一个"所有城市"的选项进去
        $city->prepend(['id' => 0, 'text' => '全部']);

        return response()->json($city);
    }

    // 获取地区
    public function district(Request $request)
    {
        $city_id = $request->get('q');

        // 如果是 0
        if (!$city_id) return response()->json([]);

        $district = ChinaArea::whereParentId($city_id)->get(['id', DB::raw('name as text')]);

        // 追加一个"所有区域"的选项进去
        $district->prepend(['id' => 0, 'text' => '全部']);

        return response()->json($district);
    }
}
