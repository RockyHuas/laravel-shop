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
        'password' => 'required|string|min:6',
        'real_name' => 'required|string',
        'gender' => 'nullable|integer|in:0,1,2',
        'image' => 'required',
        'phone' => 'required|integer|regex:/^1[34578][0-9]{9}$/',
        'province_id' => 'required|integer',
        'city_id' => 'required|integer',
        'district_id' => 'nullable|integer',
        'shop_name' => 'nullable|string',
        'category_id' => 'nullable|integer',
        'brand_id' => 'nullable|integer',
        'price_num' => 'nullable|integer',
        'min_price' => 'nullable|string',
        'max_price' => 'nullable|string',
        'sort' => 'nullable|integer|in:0,1,2',
        'sort_order' => 'nullable|string|in:asc,desc',
        'product_id' => 'required|integer',
        'product_ids' => 'required|string',
        'amount' => 'required|integer|min:1',
        'address' => 'required|string',
        'default' => 'nullable|integer',
        'province' => 'required|string',
        'city' => 'required|string',
        'district' => 'required|string',
        'user_address_id' => 'required|integer',
        'products' => 'required|json',
        'total_amount' => 'required|string|min:0',
        'order_status' => 'nullable|integer|in:0,1,2,3,4,5',
        'old_password' => 'required|string',
        'open_id' => 'nullable|string',
        'mini_id' => 'nullable|string',
        'code' => 'required|string',
        'note'=>'nullable|string',
        'key' => 'required|string',
    ];
}
