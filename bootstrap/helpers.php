<?php
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

if (!function_exists('throw_e')) {
    function throw_e($err, int $code = 500)
    {
        if ($err instanceof Exception) {
            throw $err;
        } else {
            throw new Exception($err,$code);
        }
    }
}
// 条件异常
if (!function_exists('throw_on')) {
    function throw_on($bool, $err, int $code = 500)
    {
        if ($bool) {
            throw_e($err, $code);
        }
        return $bool;
    }
}

// 空异常
if (!function_exists('throw_empty')) {
    function throw_empty($empty, $err, int $code = 500)
    {
        if (empty($empty)) {
            throw_e($err, $code);
        }
        return $empty;
    }
}

//成功返回json格式数据
if (!function_exists('ok')) {
    function ok($result)
    {
        if (empty($result)) {
        } elseif (is_a($result, Illuminate\Support\Collection::class)) {
            $result = compact('result');
        } elseif (is_array($result)) {
            array_key_exists(0, $result) && $result = compact('result');
        } elseif (!is_object($result)) {
            $result = compact('result');
        }
        return response()->json($result);
    }
}

