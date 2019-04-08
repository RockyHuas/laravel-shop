<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ArticleCategoryController extends Controller
{
    use ModelForm;

    /**
     * 文章分类
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('文章分类列表');
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
            $content->header('创建文章分类');
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
            $content->header('编辑文章分类');
            $content->body($this->form()->edit($id));
        });
    }

    public function delete($id)
    {
        ArticleCategory::whereKey($id)->firstOrFail()->delete();

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
        return Admin::grid(ArticleCategory::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('文章分类名称');
            $grid->summary('简介');
            $grid->actions(function ($actions) {
                $actions->disableView();
            });

            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableFilter();
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
        return Admin::form(ArticleCategory::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });

            $form->text('title', '文章分类名称')->rules('required');
            $form->text('summary', '简介');
        });
    }

}
