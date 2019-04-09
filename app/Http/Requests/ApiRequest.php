<?php

namespace App\Http\Requests;

class ApiRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected $_rules = [
        'password' => 'required|string|min:6|confirmed',
        'real_name' => 'required|string',
        'gender' => 'nullable|integer|in:0,1,2',
        'image' => 'required',
        'phone' => 'required|integer|size:11',
        'province_id' => 'required|integer',
        'city_id' => 'required|integer',
        'district_id' => 'nullable|integer',
        'shop_name' => 'nullable|string',
    ];
}
