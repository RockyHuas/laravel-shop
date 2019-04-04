<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Models\ChinaArea;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    use ModelForm;

    // 获取省份
    public function province()
    {
        $province = ChinaArea::whereParentId('86')->get(['id', DB::raw('name as text')]);

        return response()->json($province);
    }

    // 获取城市
    public function city(Request $request)
    {
        $province_id = $request->get('q');

        $city = ChinaArea::whereParentId($province_id)->get(['id', DB::raw('name as text')]);

        return response()->json($city);
    }

    // 获取地区
    public function district(Request $request)
    {
        $city_id = $request->get('q');

        $city = ChinaArea::whereParentId($city_id)->get(['id', DB::raw('name as text')]);

        return response()->json($city);
    }
}
