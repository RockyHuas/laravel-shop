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
    // 用户列表
    $router->get('users', 'UserController@index')->name('admin.Users.index');
    // 用户创建
    $router->get('users/create', 'UserController@create');
    $router->post('users', 'UserController@store');
    // 用户删除
    $router->delete('users/{id}', 'UserController@delete');
    // 用户详情
    $router->get('users/{id}/edit', 'UserController@edit');
    // 用户更新
    $router->put('users/{id}', 'UserController@update');

    $router->get('users/batch/upload', 'UserController@upload'); // get 请求
    $router->post('users/batch/upload', 'UserController@upload');// post 请求

    // 产品列表
    $router->get('products', 'ProductsController@index')->name('admin.products.index');
    // 下架产品列表
    $router->get('products/down', 'ProductsController@downIndex');
    // 产品创建
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    // 产品删除
    $router->delete('products/{id}', 'ProductsController@delete');
    // 产品详情
    $router->get('products/{id}/edit', 'ProductsController@edit');
    // 产品更新
    $router->put('products/{id}', 'ProductsController@update');

    // 产品分类列表
    $router->get('product/categories', 'ProductCategoryController@index')->name('admin.product.category.index');
    // 产品分类创建
    $router->get('product/categories/create', 'ProductCategoryController@create');
    $router->post('product/categories', 'ProductCategoryController@store');
    // 产品分类删除
    $router->delete('product/categories/{id}', 'ProductCategoryController@delete');
    // 产品分类详情
    $router->get('product/categories/{id}/edit', 'ProductCategoryController@edit');
    // 产品分类更新
    $router->put('product/categories/{id}', 'ProductCategoryController@update');

    // 产品品牌列表
    $router->get('product/brands', 'ProductBrandController@index')->name('admin.product.brand.index');
    // 产品品牌创建
    $router->get('product/brands/create', 'ProductBrandController@create');
    $router->post('product/brands', 'ProductBrandController@store');
    // 产品品牌删除
    $router->delete('product/brands/{id}', 'ProductBrandController@delete');
    // 产品品牌详情
    $router->get('product/brands/{id}/edit', 'ProductBrandController@edit');
    // 产品品牌更新
    $router->put('product/brands/{id}', 'ProductBrandController@update');

    // 批量复制产品
    $router->post('products/batch/copy', 'ProductsController@copy');
    // 批量导入产品
    $router->post('products/batch/upload', 'ProductsController@upload');// post 请求
    $router->get('products/batch/upload', 'ProductsController@upload'); // get 请求

    // 文章分类列表
    $router->get('article/categories', 'ArticleCategoryController@index')->name('admin.article.category.index');
    // 文章分类创建
    $router->get('article/categories/create', 'ArticleCategoryController@create');
    $router->post('article/categories', 'ArticleCategoryController@store');
    // 文章分类删除
    $router->delete('article/categories/{id}', 'ArticleCategoryController@delete');
    // 文章分类详情
    $router->get('article/categories/{id}/edit', 'ArticleCategoryController@edit');
    // 文章分类更新
    $router->put('article/categories/{id}', 'ArticleCategoryController@update');

    // 文章列表
    $router->get('articles', 'ArticleController@index')->name('admin.article.index');
    // 文章创建
    $router->get('articles/create', 'ArticleController@create');
    $router->post('articles', 'ArticleController@store');
    // 文章删除
    $router->delete('articles/{id}', 'ArticleController@delete');
    // 文章详情
    $router->get('articles/{id}/edit', 'ArticleController@edit');
    // 文章更新
    $router->put('articles/{id}', 'ArticleController@update');

    // banner 列表
    $router->get('banners', 'BannerController@index')->name('admin.banner.index');
    // banner 创建
    $router->get('banners/create', 'BannerController@create');
    $router->post('banners', 'BannerController@store');
    // banner 删除
    $router->delete('banners/{id}', 'BannerController@delete');
    // banner 详情
    $router->get('banners/{id}/edit', 'BannerController@edit');
    // banner 更新
    $router->put('banners/{id}', 'BannerController@update');

    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    $router->get('orders/{order}/print', 'OrdersController@print')->name('admin.orders.print');
    $router->post('orders/{order}/change', 'OrdersController@change')->name('admin.orders.change');

    // 管理员列表
    $router->get('admin/user', 'AdminUserController@index')->name('admin.admin.user.index');
    // 管理员创建
    $router->get('admin/user/create', 'AdminUserController@create')->name('admin.admin.user.create');
    $router->post('admin/user', 'AdminUserController@store')->name('admin.admin.user.store');
    // 管理员删除
    $router->delete('admin/user/{id}', 'AdminUserController@delete')->name('admin.admin.user.delete');
    // 管理员详情
    $router->get('admin/user/{id}/edit', 'AdminUserController@edit')->name('admin.admin.user.edit');
    // 保存角色
    $router->put('admin/user/{id}', 'AdminUserController@update')->name('admin.admin.user.update');


    // 角色列表
    $router->get('admin/role', 'AdminRoleController@index')->name('admin.admin.role.index');
    // 角色创建
    $router->get('admin/role/create', 'AdminRoleController@create')->name('admin.admin.role.create');
    $router->post('admin/role', 'AdminRoleController@store')->name('admin.admin.role.store');
    // 角色删除
    $router->delete('admin/role/{id}', 'AdminRoleController@delete')->name('admin.admin.role.delete');
    // 角色详情
    $router->get('admin/role/{id}/edit', 'AdminRoleController@edit')->name('admin.admin.role.edit');
    // 保存角色
    $router->put('admin/role/{id}', 'AdminRoleController@update')->name('admin.admin.role.update');

    // 权限列表
    $router->get('admin/permission', 'AdminPermissionController@index')->name('admin.admin.permission.index');
    // 权限创建
    $router->get('admin/permission/create', 'AdminPermissionController@create')->name('admin.admin.permission.create');
    $router->post('admin/permission', 'AdminPermissionController@store')->name('admin.admin.permission.store');
    // 权限删除
    $router->delete('admin/permission/{id}', 'AdminPermissionController@delete')->name('admin.admin.permission.delete');
    // 权限详情
    $router->get('admin/permission/{id}/edit', 'AdminPermissionController@edit')->name('admin.admin.permission.edit');
    // 保存权限
    $router->put('admin/permission/{id}', 'AdminPermissionController@update')->name('admin.admin.permission.update');

    // 广告列表
    $router->get('ads', 'AdController@index')->name('admin.ad.index');
    // 广告创建
    $router->get('ads/create', 'AdController@create');
    $router->post('ads', 'AdController@store');
    // 广告删除
    $router->delete('ads/{id}', 'AdController@delete');
    // 广告详情
    $router->get('ads/{id}/edit', 'AdController@edit');
    // 广告更新
    $router->put('ads/{id}', 'AdController@update');

    // 广告分类列表
    $router->get('ad_categories', 'AdCategoryController@index')->name('admin.ad.category.index');
    // 广告分类创建
    $router->get('ad_categories/create', 'AdCategoryController@create');
    $router->post('ad_categories', 'AdCategoryController@store');
    // 广告分类删除
    $router->delete('ad_categories/{id}', 'AdCategoryController@delete');
    // 广告分类详情
    $router->get('ad_categories/{id}/edit', 'AdCategoryController@edit');
    // 广告分类更新
    $router->put('ad_categories/{id}', 'AdCategoryController@update');

    // 系统设置列表
    $router->get('system_settings', 'SystemSettingController@index')->name('admin.system.setting.index');
    // 系统设置创建
    $router->get('system_settings/create', 'SystemSettingController@create');
    $router->post('system_settings', 'SystemSettingController@store');
    // 系统设置删除
    $router->delete('system_settings/{id}', 'SystemSettingController@delete');
    // 系统设置详情
    $router->get('system_settings/{id}/edit', 'SystemSettingController@edit');
    // 系统设置更新
    $router->put('system_settings/{id}', 'SystemSettingController@update');

    // 支付方式列表
    $router->get('pays', 'PayController@index')->name('admin.pay.index');
    // 支付方式创建
    $router->get('pays/create', 'PayController@create');
    $router->post('pays', 'PayController@store');
    // 支付方式删除
    $router->delete('pays/{id}', 'PayController@delete');
    // 支付方式详情
    $router->get('pays/{id}/edit', 'PayController@edit');
    // 支付方式更新
    $router->put('pays/{id}', 'PayController@update');
});