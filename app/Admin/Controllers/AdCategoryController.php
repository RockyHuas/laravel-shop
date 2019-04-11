<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdCategory;
use App\Models\Banner;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class AdCategoryController extends Controller
{
    use ModelForm;

    /**
     * 广告分类
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('广告分类列表');
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
            $content->header('创建广告分类');
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
            $content->header('编辑广告分类');
            $content->body($this->form()->edit($id));
        });
    }

    public function delete($id)
    {
        AdCategory::whereKey($id)->firstOrFail()->delete();

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
        return Admin::grid(AdCategory::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('广告分类名称');
            $grid->created_at('创建时间');
            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
            });

            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });

            // 去掉`删除`按钮
            $grid->disableExport();

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
        return Admin::form(AdCategory::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });
            $form->text('title', '广告分类名称')->rules('required');

        });
    }

}
