<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\ProductViewDetail;
use App\Models\User;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ProductViewDetailController extends Controller
{
    use ModelForm;

    /**
     * banner
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('产品浏览详情');
            $content->body($this->grid());
        });
    }

    /**
     * Make a grid builder.
     *
     */
    protected function grid()
    {
        return Admin::grid(ProductViewDetail::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->user()->name('会员名称');
            $grid->column('省份')->display(function () {
                $user=User::whereKey($this->user_id)->first();
                if($user){
                    $user->loadMissing('province');
                    return data_get($user,'province.name');
                } else {
                    return '';
                }

            });
            $grid->column('城市')->display(function () {
                $user=User::whereKey($this->user_id)->first();
                if($user){
                    $user->loadMissing('city');
                    return data_get($user,'city.name');
                } else {
                    return '';
                }

            });
            $grid->ip('访问来源');
            $grid->user()->phone('手机号码');
            $grid->product()->title('浏览的产品');
            $grid->product()->review_count('产品浏览总数')->sortable();
            $grid->created_at('浏览时间');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();
            });

            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                $filter->where(function ($query) {
                    $query->whereHas('user',function($user_query){
                        $user_query->where('name', 'like', "%{$this->input}%")->orWhere('phone', 'like', "%{$this->input}%");
                    });
                }, '用户名或手机号码');

                $filter->where(function ($query) {
                    $query->whereHas('product',function($user_query){
                        $user_query->where('title', 'like', "%{$this->input}%");
                    });
                }, '产品名称');

                // 添加标题过滤
                $filter->like('ip', 'IP 地址');

                $filter->between('created_at', '访问时间')->datetime();
            });

            $grid->model()->orderBy('id', 'desc');

            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });

            $grid->disableActions();
            $grid->disableExport();
            $grid->disableCreateButton();
        });
    }


}
