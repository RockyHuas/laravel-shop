<?php

namespace App\Admin\Controllers;

use App\Models\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProductsController extends Controller
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
            $content->header('商品列表');
            $content->body($this->grid());
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
            $content->header('编辑商品');
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
            $content->header('创建商品');
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
        return Admin::grid(Product::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('商品名称');
            $grid->on_sale('已上架')->display(function ($value) {
                return $value ? '是' : '否';
            });
            $grid->price('价格');
            $grid->stock('剩余库存');
            $grid->is_hot('热卖产品')->display(function ($value) {
                return $value ? '是' : '否';
            });
            $grid->is_rec('推荐首页')->display(function ($value) {
                return $value ? '是' : '否';
            });
            $grid->sort('排序');

            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
            });
            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
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
        return Admin::form(Product::class, function (Form $form) {
            // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
            $form->text('title', '商品名称')->rules('required');
            // 创建一个选择图片的框
            $form->image('image', '封面图')->rules('required|image');
            // 创建一个选择图片的框，移动端图片
            $form->image('app_image', '移动端封面图')->rules('required|image');

            // 创建一个富文本编辑器
            $form->editor('description', '商品描述')->rules('required');
            // 创建一个富文本编辑器，移动端
            $form->editor('app_description', '移动端商品描述')->rules('required');
            // 创建一组单选框
            $form->radio('on_sale', '上架')->options(['1' => '是', '0' => '否'])->default('0');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            //            // 直接添加一对多的关联模型
//            $form->hasMany('skus', function (Form\NestedForm $form) {
//                $form->text('title', 'SKU 名称')->rules('required');
//                $form->text('description', 'SKU 描述')->rules('required');
//                $form->text('price', '单价')->rules('required|numeric|min:0.01');
//                $form->text('stock', '剩余库存')->rules('required|integer|min:0');
//            });
        });
    }
}
