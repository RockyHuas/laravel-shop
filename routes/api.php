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

    $api->group(['middleware' => ['api.auth','user_active']], function ($api) {
        $api->get('article/categories', 'HomeController@getArticleCategory');
    });
}
);
