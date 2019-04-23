<?php

namespace App\Exceptions;

use App;
use ErrorException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {

    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // if ($exception instanceof ErrorException) {
        //     return parent::render($request, $exception);
        // } else
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'code' => 403,
                'data' => null,
                'message' => '接口禁止访问',
                'error' => $exception->getMessage(),
                'file' => $exception->getFile() . ':' . $exception->getLine()
            ]);
        } elseif ($exception instanceof ValidationException) {
            return response()->json([
                'code' => $exception->getCode() ?: 500,
                'data' => null,
                'message' => head($exception->errors())[0],
                'error' => $exception->getTraceAsString(),
                'file' => $exception->getFile() . ':' . $exception->getLine()
            ]);
        } elseif ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'code' => $exception->getCode() ?: 500,
                'data' => null,
                'message' => '查询结果为空',
                'error' => $exception->getTraceAsString(),
                'file' => $exception->getFile() . ':' . $exception->getLine()
            ]);
        } elseif ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'code' => $exception->getCode() ?: 404,
                'data' => null,
                'message' => '参数错误',
                'error' => $exception->getTraceAsString(),
                'file' => $exception->getFile() . ':' . $exception->getLine()
            ]);
        } elseif ($exception instanceof InvalidSignatureException) {
            return response()->json([
                'code' => $exception->getCode() ?: 500,
                'data' => null,
                'message' => '签名无效',
                'error' => $exception->getTraceAsString(),
                'file' => $exception->getFile() . ':' . $exception->getLine()
            ]);
        } elseif ($exception instanceof AuthorizationException) {
            return response()->json([
                'code' => $exception->getCode() ?: 402,
                'data' => null,
                'message' => '抱歉，您无权访问',
                'error' => $exception->getTraceAsString(),
                'file' => $exception->getFile() . ':' . $exception->getLine()
            ]);
        } elseif ($exception instanceof MaintenanceModeException) {
            return response()->json([
                'code' => $exception->getCode() ?: 500,
                'data' => null,
                'message' => '系统正在维护中……',
                'error' => $exception->getTraceAsString(),
                'file' => $exception->getFile() . ':' . $exception->getLine()
            ]);
        } elseif ($exception instanceof UnauthorizedHttpException) {
            if ($exception->getPrevious() instanceof TokenExpiredException) {
                return response()->json([
                    'code' => 401,
                    'data' => null,
                    'message' => 'Token 已过期，请重新登录',
                    'error' => $exception->getTraceAsString(),
                    'file' => $exception->getFile() . ':' . $exception->getLine()
                ]);
            }
            if ($exception->getPrevious() instanceof TokenInvalidException) {
                return response()->json([
                    'code' => 401,
                    'data' => null,
                    'message' => 'Token 已过期，请重新登录',
                    'error' => $exception->getTraceAsString(),
                    'file' => $exception->getFile() . ':' . $exception->getLine()
                ]);
            }

        }

        return response()->json([
            'code' => $exception->getCode() ?: 500,
            'data' => null,
            'message' => $exception->getMessage(),
            'error' => $exception->getTraceAsString(),
            'file' => $exception->getFile() . ':' . $exception->getLine()
        ]);
    }
}
