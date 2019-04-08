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
    $router->get('article/categories', 'ArticleCategoryController@index')->name('admin.article.brand.index');
    // 文章分类创建
    $router->get('article/categories/create', 'ArticleCategoryController@create');
    $router->post('article/categories', 'ArticleCategoryController@store');
    // 文章分类删除
    $router->delete('article/categories/{id}', 'ArticleCategoryController@delete');
    // 文章分类详情
    $router->get('article/categories/{id}/edit', 'ArticleCategoryController@edit');
    // 文章分类更新
    $router->put('article/categories/{id}', 'ArticleCategoryController@update');

    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');
    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('admin.orders.handle_refund');

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

});