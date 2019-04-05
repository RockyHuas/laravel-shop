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

// 加载“授权”相关路由
if (!function_exists('load_authorizations_routes')) {
    function load_authorizations_routes($api)
    {
        // 登录
        $api->post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');

        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function ($api) {
            // 刷新token
            $api->put('authorizations/current', 'AuthorizationsController@update')
                ->name('api.authorizations.update');

            // 删除token
            $api->delete('authorizations/current', 'AuthorizationsController@destroy')
                ->name('api.authorizations.destroy');
        });

    }
}

// 加载"用户"相关路由
if (!function_exists('load_users_routes')) {
    function load_users_routes($api)
    {
        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function ($api) {
            // 增加用户
            $api->post('users', 'UsersController@store')
                ->name('api.users.store');

            // 管理员修改成员资料
            $api->post('members/{user}/profile', 'UsersController@updateMemberProfile')
                ->name('api.members.profile.update');

            // 查询成员列表
            $api->get('members', 'UsersController@index')
                ->name('api.members.index');

            // 查询成员详细信息
            $api->get('members/{user}', 'UsersController@show')
                ->name('api.members.show');

            // 修改成员激活状态
            $api->put('members/{user}/status', 'UsersController@changeStatus')
                ->name('api.members.status');

            // 修改成员角色
            $api->put('members/{user}/role', 'UsersController@changeRole')
                ->name('api.members.role');

            // 重置密码
            $api->put('members/{user}/password', 'UsersController@resetPassword')
                ->name('api.members.password');

            // 删除成员
            $api->delete('members/{user}', 'UsersController@destroy')
                ->name('api.members.destroy');

            // 修改用户头像
            $api->post('users/avatar', 'UsersController@changeAvatar')
                ->name('api.users.avatar');

            // 个人修改用户资料
            $api->post('users/{user}/profile', 'UsersController@updateUserProfile')
                ->name('api.users.profile.update');
            // 获取当前登录用户信息
            $api->get('users/me', 'UsersController@getCurrentUserInfo')
                ->name('api.users.me');
        });

    }
}

// 客户端接口
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api', 'middleware' => ['api', 'bindings']], function ($api) {

    $api->get('test', 'TestController@index');

//    // 开始加载路由
//    $routes = ['authorizations', 'users'];
//
//    array_map(function ($route) use ($api) {
//        $function = 'load_' . $route . '_routes';
//        $function($api);
//    }, $routes);
}
);
