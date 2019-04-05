<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //
        return [];
    }

    public function getRules(array $rule_keys)
    {
        $rules = array_merge($this->_global_rules, $this->_rules);
        return array_only($rules, $rule_keys);
    }

    protected $_rules = [];

    // 全局验证规则，如果定义了 $_rules 相同规则 ，将会覆盖全局规则
    protected $_global_rules = [
        'name' => 'required|string',
        'summary' => 'nullable|string',
        'size' => 'nullable|integer',
        'keywords' => 'nullable|string',
        'id' => 'required|integer',
        'page' => 'nullable|integer',
    ];
}
