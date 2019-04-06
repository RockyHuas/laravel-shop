<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    // 省份
    $router->get('area/province', 'AreaController@province');
    // 城市
    $router->get('area/city', 'AreaController@city');
    // 地区
    $router->get('area/district', 'AreaController@district');
    $router->get('area/province', 'AreaController@province');
    $router->get('users', 'UsersController@index');

    // 产品列表
    $router->get('products', 'ProductsController@index')->name('admin.products.index');
    // 产品创建
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    // 产品删除
    $router->delete('products/{id}', 'ProductsController@delete');
    // 产品详情
    $router->get('products/{id}/edit', 'ProductsController@edit');
    // 产品更新
    $router->put('products/{id}', 'ProductsController@update');
    // 批量复制产品
    $router->post('products/batch/copy', 'ProductsController@copy');
    // 批量导入产品
    $router->post('products/batch/upload', 'ProductsController@upload');// post 请求
    $router->get('products/batch/upload', 'ProductsController@upload'); // get 请求
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');
    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('admin.orders.handle_refund');

});