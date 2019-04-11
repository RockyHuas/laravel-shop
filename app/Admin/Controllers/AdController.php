<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdCategory;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use \DB;

class AdController extends Controller
{
    use ModelForm;

    /**
     * 广告
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('广告列表');
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
            $content->header('创建广告');
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
            $content->header('编辑广告');
            $content->body($this->form()->edit($id));
        });
    }

    public function delete($id)
    {
        Ad::whereKey($id)->firstOrFail()->delete();

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
        return Admin::grid(Ad::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('广告名称');
            $grid->link('广告链接');
            $grid->image('广告图片')->image(\Storage::disk('public')->url('/'), 50, 50);
            $grid->ad_category()->title('广告分类');
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
        return Admin::form(Ad::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });

            $form->text('title', '广告名称')->rules('required');
            $form->text('link', '广告链接')->rules('required');

            // 商品分类
            $categories = AdCategory::get(['id', DB::raw('title as text')])->mapWithKeys(function ($item) {
                return [$item->id => $item->text];
            })->toArray();

            $form->select('ad_category_id', '广告分类')->options($categories)->rules('required');

            $form->image('image', '广告图片')->rules('required');
            $form->image('app_image', '移动端广告图片');

        });
    }

}
