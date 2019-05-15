<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Extensions\Libs\RequestExtension;
use App\Extensions\Paginator\TchLengthAwarePaginator;
use App\Models\Order;
use App\Models\User;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Monolog\Logger;
use Yansongda\Pay\Pay;
use Illuminate\Support\ServiceProvider;
use Exception;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app('Dingo\Api\Exception\Handler')->register(function (Exception $exception) {
            return (new Handler($this->app))->render(request(), $exception);
        });

        // 请求参数
        Request::macro('fields', function (array $arguments, bool $remove_keys = false) {
            static $request_extend;
            if (!$request_extend) {
                $request_extend = new RequestExtension($this);
            }
            $result = $request_extend->get($arguments);
            return !$remove_keys ? $result : array_values($result);
        });

        // 自定义分布，将分布属性 data 改为 items
        $this->app->bind(LengthAwarePaginator::class, function ($_, $arguments) {
            extract($arguments);
            return new TchLengthAwarePaginator($items, $total, $perPage, $currentPage, $options);
        });

        User::observe(UserObserver::class);
        Order::observe(OrderObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 往服务容器中注入一个名为 alipay 的单例对象
        $this->app->singleton('alipay', function () {
            $config = config('pay.alipay');
            $config['notify_url'] = route('payment.alipay.notify');
            $config['return_url'] = route('payment.alipay.return');
            // 判断当前项目运行环境是否为线上环境
            if (app()->environment() !== 'production') {
                $config['mode'] = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function () {
            $config = config('pay.wechat');
            $config['notify_url'] = route('payment.wechat.notify');
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }
}
