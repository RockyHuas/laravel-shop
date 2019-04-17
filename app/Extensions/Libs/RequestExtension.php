<?php
/**
 * 输入类
 * Users: Huangzb
 * Date: 2019/3/5
 * Time: 10:21
 *
 * 用法：
 * App\Http\Requests\AdminRequest $request->fields(['parameter1'=>['default'=>'123','rule'=>'required|integer','as'='alias','type'=>'int'],'parameter2'=>12,'paramter3'])
 *
 * 3种参数的输入形式
 *      1.不指定默认值的参数 如：'paramter'
 *      2.指定默认值的参数 如：'paramter'=>'默认值'
 *      3.指定默认值,别名,类型,验证规则的参数 如 'parameter1'=>['default'=>'123','rule'=>'required|integer','as'='alias']
 *      notice:以上1,2种方法，如果没有指定验证规则，则会调用App\Http\Requests\AdminRequest里面的默认验证规则
 *
 * 参数可选的属性
 * default:指定参数的默认值
 * rule:指定参数的验证规则
 *      rule输入形式：直接用laravel自带的验证规格，参考：https://laravel-china.org/docs/laravel/5.6/validation/1372#c58a91
 * as: 指定参数的别名，如"post_name"参数指定别名为"name",返回的数据的键名为"name"
 * type:指定参数的数据类型
 *      可选的值：
 *          -int  强制转换成int类型
 *          -float 强制转换成float类型
 *          -ip  ip类型
 *          -url url类型
 *          -email 邮件类型
 *          -split 将字符串转换成数组
 *          -array 将json数据转换成数组
 *          -json 将json数据转换成数组
 *
 *
 *
 *
 */

namespace App\Extensions\Libs;

use Illuminate\Support\Collection;

final class RequestExtension
{
    const VALID_STRINGS = [
        'email' => FILTER_VALIDATE_EMAIL,
        'url' => FILTER_VALIDATE_URL,
        'ip' => FILTER_VALIDATE_IP,
        'float' => FILTER_VALIDATE_FLOAT,
        'int' => FILTER_VALIDATE_INT

    ];
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function get(array $arguments)
    {
        $controller=array_shift($arguments);
        //解析
        $configs = $this->expalinConfigs($arguments);
        //验证参数的合法性
        $validate_rules = $this->coverToValidatorRules($configs);
        $data = $controller->validate( $this->request,$validate_rules, $this->request->messages());


        //获取参数的默认值
        $result = $this->getRuleValues($data, $configs);
        return $result;
    }

    private function expalinConfigs(array $arguments)
    {
        // ['attribute1'=>['default'=>1,'rule'=>'required|array','as'=>'alias','type'=>'int' ],'attribute2','attribute3'=>3]
        return collect($arguments)->mapWithKeys(function ($item, $key) {
            if (is_numeric($key)) {
                $key = $item;
                $item = ['default' => null];
            } elseif (!is_array($item)) {
                $item = ['default' => $item];
            }
            return [$key => $item];
        })->toArray();
    }


    /**
     * 转换成laravel默认的验证规则
     * @param array $configs
     */
    private function coverToValidatorRules(array $configs)
    {
        if (method_exists($this->request, 'getRules')) {
            $request_rules = $this->request->getRules(array_keys($configs));
        } else {
            $request_rules = [];
        }
        foreach ($configs as $key => $config) {
            $request_rules[$key] = $config['rule'] ?? $request_rules[$key] ?? 'string';
        };
        return $request_rules;
    }

    private function getRuleValues(array $validate_data, array $configs)
    {
        $result = [];
        foreach ($configs as $key => $config) {
            $input = $validate_data[$key] ?? '';
            is_string($input) && $input = rawurldecode($input);
            if (empty($config)) {
                $result[$key] = $input ?? '';
                continue;
            }
            if (!isset($input) || blank($input)) {
                // 设默认值
                $input = $config['default'] ?? '';
            } elseif (isset($config['type'])) {
                // 转类型
                $input = $this->convertToType($input, $config['type']);
            }

            //别名转换
            if (isset($config['as'])) {
                $result[$config['as']] = $input;
            } else {
                $result[$key] = $input;
            }
        }
        return $result;
    }

    public function convertToType($value, $type)
    {
        switch ($type) {
            case 'int':
            case 'float':
            case 'ip':
            case 'email':
            case 'url':
                $value = filter_var($value, self::VALID_STRINGS[$type]);
                throw_on($value === false, '参数类型不正确');
                break;
            case 'split':
                $value = explode(',', $value);
                break;
            case 'array':
            case 'json':
                if (is_array($value)) {
                    break;
                }
                $value = json_decode($value, JSON_UNESCAPED_UNICODE);
                break;
        }
        return $value;
    }
}
