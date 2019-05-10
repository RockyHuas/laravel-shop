<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class BannerController extends Controller
{
    use ModelForm;

    /**
     * banner
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('banner列表');
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
            $content->header('创建banner');
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
            $content->header('编辑banner');
            $content->body($this->form()->edit($id));
        });
    }

    public function delete($id)
    {
        Banner::whereKey($id)->firstOrFail()->delete();

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
        return Admin::grid(Banner::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('banner名称');
            $grid->link('PC 端链接');
            $grid->mini_link('小程序链接');
            $grid->sort('banner排序');
            $grid->created_at('创建时间');
            $grid->actions(function ($actions) {
                $actions->disableView();
            });

            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 添加标题过滤
                $filter->like('title', 'banner名称');
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
        return Admin::form(Banner::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });

            $form->text('title', 'banner名称')->rules('required');
            $form->text('link', 'PC 端链接')->rules('required');
            $form->text('mini_link', '小程序链接');

            $form->image('image', 'banner图片')->rules('required');
            $form->image('app_image', '移动端banner图片');

            $form->text('sort', '排序（数字越小越靠前）')->default(0);
        });
    }

}
