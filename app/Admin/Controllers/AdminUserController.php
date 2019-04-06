<?php

namespace App\Admin\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ChinaArea;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.administrator'));
            $content->description(trans('admin.list'));
            $content->body($this->grid()->render());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('admin.administrator'));
            $content->description(trans('admin.edit'));
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.administrator'));
            $content->description(trans('admin.create'));
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Administrator::grid(function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->username(trans('admin.username'));
            $grid->name(trans('admin.name'));
            $grid->roles(trans('admin.roles'))->pluck('name')->label();
            $grid->created_at(trans('admin.created_at'));
            $grid->updated_at(trans('admin.updated_at'));

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->getKey() == 1) {
                    $actions->disableDelete();
                }
                $actions->disableView();
            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                });
            });

            $grid->filter(function ($filter) {

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 添加标题过滤
                $filter->like('title', '用户名');

                $filter->in('province', '省份')->multipleSelect('/admin/area/province')->load('city', '/admin/area/city');

                $filter->in('city', '城市')->multipleSelect('/admin/area/city');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Administrator::form(function (Form $form) {
            $form->display('id', 'ID');

            $form->text('username', trans('admin.username'))->rules('required');
            $form->text('name', trans('admin.name'))->rules('required');
            $form->image('avatar', trans('admin.avatar'));
            $form->password('password', trans('admin.password'))->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });

            $form->ignore(['password_confirmation']);

            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });

            $user = Admin::user();

            $form->select('province', '省份')->options(function ($id) use ($user) {
                $provinces = ChinaArea::whereParentId(86)->get()->when($user->province, function ($items, $value) {
                    return $items->filter(function ($item) use ($value) {
                        return $item->id == $value;
                    });
                })->unique();
                if (!$user->province) $provinces->prepend(['id' => 0, 'name' => '全国']);
                return $provinces->pluck('name', 'id');
            })->load('city', '/admin/area/city');

            $form->select('city', '城市')->options(function ($id) use ($user) {
                if ($id) {
                    $cities = ChinaArea::options($id)->when($user->city, function ($items, $value) {
                        return $items->filter(function ($item, $key) use ($value) {
                            return $key == $value;
                        });
                    })->unique();
                    if (!$user->city) $cities->prepend('全部地区', 0);
                    return $cities;
                }
            });

            // 商品分类
            $categories = Category::get(['id', DB::raw('title as text')])->when($user->category_id, function ($items, $value) {
                return $items->filter(function ($item) use ($value) {
                    return $item->id == $value;
                });
            })->mapWithKeys(function ($item) {
                return [$item->id => $item->text];
            })->toArray();
            if (!$user->category_id) array_unshift($categories, '所有');

            $form->select('category_id', '可发布的商品类别')->options($categories);
            // 品牌
            $brands = Brand::get(['id', DB::raw('title as text')])->when($user->brand_id, function ($items, $value) {
                return $items->filter(function ($item) use ($value) {
                    return $item->id == $value;
                });
            })->mapWithKeys(function ($item) {
                return [$item->id => $item->text];
            })->toArray();
            if (!$user->brand_id) array_unshift($brands, '所有');

            $form->select('brand_id', '可发布的商品品牌')->options($brands);

            $form->multipleSelect('roles', trans('admin.roles'))->options(Role::all()->pluck('name', 'id'));
            $form->multipleSelect('permissions', trans('admin.permissions'))->options(Permission::all()->pluck('name', 'id'));

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });
        });
    }
}
