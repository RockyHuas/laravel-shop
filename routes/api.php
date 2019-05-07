<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');


// 客户端接口
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api', 'middleware' => ['api', 'bindings']], function ($api) {

    $api->get('test', 'TestController@index');

    $api->get('wechat/scan', 'WeChatController@scanUrl');

    $api->post('images', 'ImageController@store')
        ->name('api.images.store');

    // 登录
    $api->post('authorizations', 'AuthorizationController@store')
        ->name('api.authorizations.store');


    // 忘记密码
    $api->patch('users', 'UserController@updatePassword')
        ->name('api.users.update.password');
    $api->put('users', 'UserController@updatePassword')
        ->name('api.users.update.password');

    // 用户注册
    $api->post('users', 'UserController@store')
        ->name('api.users.store');

    // 省份
    $api->get('area/provinces', 'AreaController@getProvince')
        ->name('api.area.province');

    // 城市
    $api->get('area/cities/{china_area}', 'AreaController@getCity')
        ->name('api.area.city');

    // 地区
    $api->get('area/districts/{china_area}', 'AreaController@getDistrict')
        ->name('api.area.district');

    // 网站设置
    $api->get('website', 'HomeController@getWebSiteSetting');

    $api->group(['middleware' => ['api.auth']], function ($api) {
        // 删除token
        $api->delete('authorizations/current', 'AuthorizationController@destroy')
            ->name('api.authorization.destroy');
    });
    // 推荐的导航文章
    $api->get('nav/articles', 'HomeController@getNavArticles');

    // 文章分类
    $api->get('article/categories', 'HomeController@getArticleCategory');

    // 产品分类
    $api->get('product/categories', 'ProductController@getCategories');

    $api->group(['middleware' => ['api.auth', 'user_active']], function ($api) {

        // banner
        $api->get('banners', 'HomeController@getBanners');
        // 广告
        $api->get('home/ads', 'HomeController@getAds');
        // 推荐首页品牌
        $api->get('home/brands', 'HomeController@getRecBrands');
        // 推荐商品
        $api->get('home/rec_products', 'HomeController@getRecProducts');
        // 劲爆产品
        $api->get('home/hot_products', 'HomeController@getHotProducts');

        // 产品品牌
        $api->get('product/brands', 'ProductController@getBrands');
        // 产品价格
        $api->get('product/prices', 'ProductController@getPrices');
        // 商品列表
        $api->get('products', 'ProductController@getProducts');
        // 商品详细
        $api->get('products/{product}/detail', 'ProductController@show');
        // 购物车列表
        $api->get('cart/products', 'CartController@getProducts');
        // 购物车商品添加
        $api->post('cart/products', 'CartController@addProducts');
        // 购物车商品修改
        $api->put('cart/products', 'CartController@changeProducts');
        // 购物车商品删除
        $api->delete('cart/products', 'CartController@deleteProducts');
        // 收货地址列表
        $api->get('user/addresses', 'UserAddressController@index');
        // 新增收货地址
        $api->post('user/addresses', 'UserAddressController@store');
        // 编辑收货地址
        $api->put('user/addresses/{user_address}', 'UserAddressController@update');
        // 删除收货地址
        $api->delete('user/addresses/{user_address}', 'UserAddressController@destroy');
        // 设为默认收货地址
        $api->put('user/addresses/{user_address}/default', 'UserAddressController@setDefault');
        // 订单列表
        $api->get('orders', 'OrderController@index');
        // 订单统计
        $api->get('orders/status/stat', 'OrderController@stat');
        // 提交订单
        $api->post('orders', 'OrderController@store');
        // 取消订单
        $api->delete('orders/{order}', 'OrderController@cancel');
        // 确认收获
        $api->patch('orders/{order}/confirm', 'OrderController@confirm');
        // 确认收获
        $api->put('orders/{order}/confirm', 'OrderController@confirm');
        // 订单详情
        $api->get('orders/{order}', 'OrderController@show');
        // 修改密码
        $api->put('users/password', 'UserController@changePassword');
        // 获取用户信息
        $api->get('users/me', 'UserController@getUserInfo');
        // 绑定微信
        $api->patch('users/bind', 'UserController@bindWeChat');
        $api->put('users/bind', 'UserController@bindWeChat');
        // 修改用户资料
        $api->put('users/profile', 'UserController@updateProfile');


        // 文章详情
        $api->get('articles/{article}', 'HomeController@getArticleInfo');
        // 支付方式
        $api->get('payments', 'HomeController@getPayments');
    });
}
);
