<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ProductBrandController extends Controller
{
    use ModelForm;

    /**
     * 商品品牌
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('商品品牌列表');
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
            $content->header('创建商品品牌');
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
            $content->header('编辑商品品牌');
            $content->body($this->form()->edit($id));
        });
    }

    public function delete($id)
    {
        Brand::whereKey($id)->firstOrFail()->delete();

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
        return Admin::grid(Brand::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('商品品牌名称');
            $grid->image('商品品牌图片')->image(\Storage::disk('public')->url('/'), 50, 50);
            $grid->is_rec('推荐首页')->editable('select', [1 => '是', 0 => '否']);
            $grid->summary('简介');
            $grid->sort('排序')->editable('textarea');;
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
        return Admin::form(Brand::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });

            $form->text('title', '商品品牌名称')->rules('required');
            // 创建一个选择图片的框
            $form->image('image', '封面图')->rules('required|image');
            // 创建一个选择图片的框，移动端图片
            $form->image('app_image', '移动端封面图')->rules('nullable|image');
            $form->text('summary', '简介');

            $form->radio('is_rec', '推荐首页')->options(['1' => '是', '0' => '否'])->default('0');

            $form->text('sort', '排序（数字越小越靠前）')->default(0);
        });
    }

}
