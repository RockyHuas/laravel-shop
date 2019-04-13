<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Pay;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class PayController extends Controller
{
    use ModelForm;

    /**
     * 支付方式
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('支付方式列表');
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
            $content->header('创建支付方式');
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
            $content->header('编辑支付方式');
            $content->body($this->form()->edit($id));
        });
    }

    public function delete($id)
    {
        Pay::whereKey($id)->firstOrFail()->delete();

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
        return Admin::grid(Pay::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('支付方式名称');
            $grid->logo('LOGO')->image(\Storage::disk('public')->url('/'), 100, 100);
            $grid->created_at('创建时间');
            $grid->actions(function ($actions) {
                $actions->disableView();
            });

            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableExport();

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
        return Admin::form(Pay::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });

            $form->text('title', '支付方式名称')->rules('required');

            $form->image('logo', '支付方式LOGO')->rules('required');

            $form->editor('description', '支付信息')->rules('required');
        });
    }

}
