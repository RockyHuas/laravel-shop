<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ArticleController extends Controller
{
    use ModelForm;

    /**
     * 文章
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('文章列表');
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

    public function delete($id)
    {
        Article::whereKey($id)->firstOrFail()->delete();

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
        return Admin::grid(Article::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('文章名称');
            $grid->article_category()->title('文章分类');
            $grid->sort('文章排序');
            $grid->created_at('创建时间');
            $grid->actions(function ($actions) {
                $actions->disableView();
            });

            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 添加标题过滤
                $filter->like('title', '文章名称');

                // 文章分类
                $article_categories = ArticleCategory::get(['id', \DB::raw('title as text')])->mapWithKeys(function ($item) {
                    return [$item->id => $item->text];
                })->toArray();
                $filter->equal('article_category_id', '文章分类')->select($article_categories);
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
        return Admin::form(Article::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });

            $form->text('title', '文章名称')->rules('required');

            // 文章分类
            $article_categories = ArticleCategory::get(['id', \DB::raw('title as text')])->mapWithKeys(function ($item) {
                return [$item->id => $item->text];
            })->toArray();

            $form->select('article_category_id', '文章分类')->options($article_categories)->rules('required');
            $form->editor('description', '文章详情')->rules('required');
            $form->editor('app_description', '移动端文章详情');

            $form->text('sort', '排序（数字越小越靠前）')->default(0);
        });
    }

}
