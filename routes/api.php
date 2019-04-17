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

    $api->post('images', 'ImageController@store')
        ->name('api.images.store');

    // 登录
    $api->post('authorizations', 'AuthorizationController@store')
        ->name('api.authorizations.store');

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

    $api->group(['middleware' => ['api.auth', 'user_active']], function ($api) {
        // 文章分类
        $api->get('article/categories', 'HomeController@getArticleCategory');
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
        // 产品分类
        $api->get('product/categories', 'ProductController@getCategories');
        // 产品品牌
        $api->get('product/brands', 'ProductController@getBrands');
        // 产品价格
        $api->get('product/prices', 'ProductController@getPrices');
        // 商品列表
        $api->get('products', 'ProductController@getProducts');
    });
}
);
