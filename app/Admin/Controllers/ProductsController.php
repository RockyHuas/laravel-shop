<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\CopyProduct;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
            $grid->title('商品名称')->editable('textarea');
            $grid->image('商品名称')->image(\Storage::disk('public')->url('/'), 50, 50);

            $grid->price('价格')->editable('textarea');;
            $grid->stock('剩余库存')->editable('textarea');
            $grid->on_sale('已上架')->editable('select', [1 => '是', 0 => '否']);
            $grid->is_hot('热卖产品')->editable('select', [1 => '是', 0 => '否']);
            $grid->is_rec('推荐首页')->editable('select', [1 => '是', 0 => '否']);
            $grid->sort('排序')->editable('textarea');;

            $grid->actions(function ($actions) {
                $actions->disableView();

            });

            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->add('批量复制产品', new CopyProduct());
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
            $form->tab('商品基本信息', function ($form) {
                // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
                $form->text('title', '商品名称')->rules('required');
                // 商品分类
                $categories = Category::get(['id', DB::raw('title as text')])->mapWithKeys(function ($item) {
                    return [$item->id => $item->text];
                })->toArray();

                $form->select('category_id', '分类')->options($categories)->rules('required');
                // 品牌
                $brands = Brand::get(['id', DB::raw('title as text')])->mapWithKeys(function ($item) {
                    return [$item->id => $item->text];
                })->toArray();
                $form->select('brand_id', '品牌')->options($brands)->rules('required');
                // 创建一个选择图片的框
                $form->image('image', '封面图')->rules('required|image');
                // 创建一个选择图片的框，移动端图片
                $form->image('app_image', '移动端封面图')->rules('required|image');
                // 价格
                $form->text('price', '单价')->rules('required|numeric|min:0.01');

                $form->text('stock', '剩余库存')->rules('required|integer|min:0');
                // 创建一组单选框
                $form->radio('on_sale', '上架')->options(['1' => '是', '0' => '否'])->default('0');

            })->tab('详细信息', function ($form) {
                $form->multipleImage('images', '滑动图')->removable();
                $form->multipleImage('app_images', '移动端滑动图')->removable();
                // 创建一个富文本编辑器
                $form->editor('description', '商品描述')->rules('required');
                // 创建一个富文本编辑器，移动端
                $form->editor('app_description', '移动端商品描述')->rules('required');
            })->tab('其他', function ($form) {

                $form->select('province', '产品所属省份')->options('/admin/area/province')->load('city', '/admin/area/city');
                $form->select('city', '产品所属地区');

                $form->radio('is_hot', '热卖产品')->options(['1' => '是', '0' => '否'])->default('0');

                $form->radio('is_rec', '推荐首页')->options(['1' => '是', '0' => '否'])->default('0');

                $form->text('sort', '排序（数字越小越靠前）')->default(0);
            });


        });
    }

    // 批量复制产品
    public function copy(Request $request)
    {
        $product_ids = $request->get('ids');

        if ($product_ids) {
            foreach (Product::find($request->get('ids')) as $product) {
                $new_product = $product->replicate();
                $new_product->save();
            }
        }
    }
}
