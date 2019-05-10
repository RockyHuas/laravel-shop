<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\ProductViewDetail;
use App\Models\User;
use App\Models\UserLoginDetail;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class UserLoginDetailController extends Controller
{
    use ModelForm;

    /**
     * banner
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('用户登录详情');
            $content->body($this->grid());
        });
    }

    /**
     * Make a grid builder.
     *
     */
    protected function grid()
    {
        return Admin::grid(UserLoginDetail::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->user()->name('会员名称');
            $grid->column('会员所属省份')->display(function () {
                $user=User::whereKey($this->user_id)->first();
                $user->loadMissing('province');
                return data_get($user,'province.name');
            });
            $grid->column('会员所属城市')->display(function () {
                $user=User::whereKey($this->user_id)->first();
                $user->loadMissing('city');
                return data_get($user,'city.name');
            });
            $grid->ip('访问来源');
            $grid->user()->phone('手机号码');
            $grid->created_at('登录时间');
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
