<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelDataInterface;
use App\Admin\Extensions\ExcelExpoter;
use App\Admin\Extensions\Tools\GlobalUploadButton;
use App\Models\ChinaArea;
use App\Models\User;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller implements ExcelDataInterface
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            // 页面标题
            $content->header('用户列表');
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

    /**
     * 删除用户
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        User::whereKey($id)->firstOrFail()->delete();

        return response()->json([
            'status' => true,
            'message' => '',
        ]);
    }

    protected function grid()
    {
        // 根据回调函数，在页面上
        return Admin::grid(User::class, function (Grid $grid) {
            // 创建一个列名为 ID 的列，内容是用户的 id 字段，并且可以在前端页面点击排序
            $grid->id('ID')->sortable();
            // 创建一个列名为 用户名 的列，内容是用户的 name 字段。下面的 email() 和 created_at() 同理
            $grid->name('用户名');
            $grid->phone('手机号码');
            $grid->union_id('是否绑定微信')->display(function ($value) {
                return $value ? '是' : '否';
            });
            $grid->shop_name('店名');
            $grid->province()->name('省份');
            $grid->city()->name('城市');
            $grid->status('审核状态')->editable('select', [1 => '审核通过', 0 => '未审核', 2 => '审核拒绝']);
            $grid->created_at('注册时间');

            $grid->actions(function ($actions) {
                $actions->disableView();
                // 不在每一行后面展示编辑按钮
            });

            // 导入商品
            $grid->tools(function ($tools) {
                $tools->append(new GlobalUploadButton('batch/upload'));
            });
            // 导出商品
            $grid->exporter(new ExcelExpoter($this));

            $grid->filter(function ($filter) {

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 添加标题过滤
                $filter->like('phone', '手机号码');

                $filter->in('province_id', '产品所属省份')->multipleSelect('/admin/area/province')->load('city', '/admin/area/city');

                $filter->in('city_id', '产品所属地区')->multipleSelect('/admin/area/city');
            });
        });
    }

    /**
     * 导入用户
     * @param Request $request
     * @return Content|\Illuminate\Http\RedirectResponse
     */
    public function upload(Request $request)
    {
        $method = $request->method();
        //如果是get请求，则返回上传页面
        if ($method == 'GET') {
            return Admin::content(function (Content $content) {

                $content->header('商品管理');
                $content->description('导入数据');
                $content->body(view('admin.common.GlobalUpload', ['list' => route('admin.products.index')]));
            });
        } else {
            // 获取文件路径
            $file_path = storage_path('app/' . $request->file('upfile')->storeAs('upload', 'product.xlsx'));

            //获取当前文本编码格式
            $content = file_get_contents($file_path);
            $fileType = mb_detect_encoding($content, array('UTF-8', 'GBK', 'LATIN1', 'BIG5'));

            app(Excel::class)->load($file_path, function ($reader) {
                $rows = $reader->all();
                // 处理导入的数据
                $this->handleUploadData($rows);
            }, $fileType);//以指定的编码格式打开文件

            $success = new MessageBag([
                'title' => '恭喜',
                'message' => '导入成功',
            ]);

            return redirect()->route('admin.Users.index');
        }
    }

    /**
     * 处理用户的数据导入
     * @param Collection $collection
     */
    public function handleUploadData(Collection $collection)
    {
        $collection->each(function ($item) {

            $user = User::when($item[0], function ($query, $value) {
                $query->whereName($value);
            })->when($item[1], function ($query, $value) {
                $query->wherePhone($value);
            })->first();

            if (!$user && $item[0] && $item[1]) { // 创建用户
                User::create([
                    'name' => $item[0],
                    'phone' => $item[1],
                    'password' => $item[2] ? bcrypt($item[2]) : bcrypt($item[1]),
                    'shop_name' => $item[3] ?: '',
                    'status' => $item[4] ?: 0
                ]);
            }
        });
    }

    public function exportData($data)
    {
        return $data->map(function ($user) {
            $user = User::whereKey($user['id'])->firstOrFail();
            $user_name = $user->name;
            $phone = $user->phone;
            $union_id = $user->union_id ? '是' : '否';
            $province = $user->province->name;
            $city = $user->city->name;
            $shop_name = $user->shop_name;
            $status = $user->status ? ($user->status == 1 ? '审核通过' : '审核拒绝') : '未审核';

            return compact('user_name', 'phone', 'union_id', 'province', 'city', 'shop_name', 'status');
        })->prepend(['用户名', '手机号码', '是否已经绑定微信', '省份', '城市', '店名', '审核状态']);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        // 创建一个表单
        return Admin::form(User::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });
            $form->text('name', '用户名')->rules('required');
            $form->text('phone', '手机号码')->rules('required');
            $form->password('password', '用户密码')->rules('required|confirmed')->default(function ($form) {
                return $form->model()->password;
            });

            $form->password('password_confirmation', '确认密码')->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });

            $form->ignore(['password_confirmation']);

            $form->text('shop_name', '店名');
            $user = Admin::user();
            $form->select('province_id', '产品所属省份')->options(function ($id) use ($user) {
                $provinces = ChinaArea::whereParentId(86)->get()->when($user->province_id, function ($items, $value) {
                    return $items->filter(function ($item) use ($value) {
                        return $item->id == $value;
                    });
                })->unique();
                return $provinces->pluck('name', 'id');
            })->load('city_id', '/admin/area/city');
            $form->select('city_id', '产品所属地区')->options(function ($id) use ($user) {
                if ($id) {
                    $cities = ChinaArea::options($id)->when($user->city_id, function ($items, $value) {
                        return $items->filter(function ($item, $key) use ($value) {
                            return $key == $value;
                        });
                    })->unique();
                    return $cities;
                }
            });
            // 创建一组单选框
            $form->radio('status', '审核状态')->options([0 => '未审核', 1 => '审核通过', 2 => '审核拒绝'])->default('0');

            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });
        });
    }
}
