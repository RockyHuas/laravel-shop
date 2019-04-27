<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Http\Request;
use Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EnableCrossRequestMiddleware
{
    /**
     * 跨域请求设置
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->getMethod() === 'OPTIONS') {
            $response = Response::make();
        } else {
            $response = $next($request);
        }

        // 如果是文件相应，直接返回
        if($response instanceof BinaryFileResponse){
            return $response;
        }
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $match = preg_match('/htwig\.com$/', $origin);
        $allow_origin = config('services.cros_service.allow_origin');
        if (($match || in_array($origin, $allow_origin) || empty($allow_origin))&& $response instanceof \Dingo\Api\Http\Response) {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Headers', config('services.cros_service.allow_headers'));
            $response->header('Access-Control-Expose-Headers', config('services.cros_service.expose_headers'));
            $response->header('Access-Control-Allow-Methods', config('services.cros_service.allow_methods'));
            $response->header('Access-Control-Allow-Credentials', config('services.cros_service.allow_credentials'));
            $response->header('Access-Control-Max-Age', 1728000);
        }
        return $response;
    }
}
