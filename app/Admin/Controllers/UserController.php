<?php

namespace App\Admin\Controllers;

use App\Models\User;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class UserController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            // 页面标题
            $content->header('用户列表');
            $content->body($this->grid());
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
            $content->header('创建文章');
            $content->body($this->form());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('编辑文章');
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * 删除用户
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        User::whereKey($id)->firstOrFail()->delete();

        return response()->json([
            'status' => true,
            'message' => '',
        ]);
    }

    protected function grid()
    {
        // 根据回调函数，在页面上
        return Admin::grid(User::class, function (Grid $grid) {
            // 创建一个列名为 ID 的列，内容是用户的 id 字段，并且可以在前端页面点击排序
            $grid->id('ID')->sortable();
            // 创建一个列名为 用户名 的列，内容是用户的 name 字段。下面的 email() 和 created_at() 同理
            $grid->name('用户名');
            $grid->phone('手机号码');
            $grid->union_id('是否绑定微信')->display(function ($value) {
                return $value ? '是' : '否';
            });
            $grid->shop_name('店名');
            $grid->province()->name('省份');
            $grid->city()->name('城市');
            $grid->status('审核状态')->editable('select', [1 => '审核通过', 0 => '未审核', 2 => '审核拒绝']);
            $grid->created_at('注册时间');
            // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建用户
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableView();
                // 不在每一行后面展示编辑按钮
                $actions->disableEdit();
            });
            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });

            $grid->filter(function ($filter) {

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 添加标题过滤
                $filter->like('phone', '手机号码');

                $filter->in('province_id', '产品所属省份')->multipleSelect('/admin/area/province')->load('city', '/admin/area/city');

                $filter->in('city_id', '产品所属地区')->multipleSelect('/admin/area/city');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        // 创建一个表单
        return Admin::form(User::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });

            // 创建一组单选框
            $form->radio('status', '激活状态')->options(['1' => '是', '0' => '否'])->default('0');

        });
    }
}
