<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\SystemSetting;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class SystemSettingController extends Controller
{
    use ModelForm;

    /**
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('网站设置');
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
            $content->header('创建');
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
            $content->header('编辑');
            $content->body($this->form()->edit($id));
        });
    }

    public function delete($id)
    {
        SystemSetting::whereKey($id)->firstOrFail()->delete();

        return response()->json([
            'status' => true,
            'message' => '',
        ]);
    }

    /**
     * Make a grid builder.
     *
     */
    protected function grid()
    {
        return Admin::grid(SystemSetting::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('网站名称');
            $grid->logo('网站LOGO')->image(\Storage::disk('public')->url('/'), 100, 100);
            $grid->gongzhonghao('公众号二维码')->image(\Storage::disk('public')->url('/'), 100, 100);
            $grid->xiaochengxu('小程序二维码')->image(\Storage::disk('public')->url('/'), 100, 100);
            $grid->service('在线客服');
            $grid->icp('ICP 备案信息');
            $grid->created_at('创建时间');
            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
            });

            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableCreateButton();

            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();

                });
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
        return Admin::form(SystemSetting::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });
            $form->text('title', '网站名称')->rules('required');
            $form->image('logo', '网站LOGO')->rules('required');
            $form->image('gongzhonghao', '公众号二维码');
            $form->image('xiaochengxu', '小程序二维码');
            $form->text('service', '在线客服');
            $form->text('icp', 'ICP 备案信息');
        });
    }

}
