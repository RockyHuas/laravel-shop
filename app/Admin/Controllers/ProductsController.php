<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelDataInterface;
use App\Admin\Extensions\ExcelExpoter;
use App\Admin\Extensions\Tools\CopyProduct;
use App\Admin\Extensions\Tools\GlobalUploadButton;
use App\Imports\DataImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ChinaArea;
use App\Models\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Excel;

class ProductsController extends Controller implements ExcelDataInterface
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

    public function delete($id)
    {
        Product::whereKey($id)->firstOrFail()->delete();

        return response()->json([
            'status' => true,
            'message' => '',
        ]);
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

    public function exportData($data)
    {
        return $data->map(function ($order) {
            return array_only($order, ['id', 'title', 'price', 'stock', 'sort']);
        })->prepend(['商品ID', '商品名称', '商品价格', '商品库存', '商品排序']);
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
            $grid->image('商品图片')->image(\Storage::disk('public')->url('/'), 50, 50);

            $grid->price('价格')->editable('textarea');
            $grid->stock('剩余库存')->editable('textarea');
            $grid->on_sale('已上架')->editable('select', [1 => '是', 0 => '否']);
            $grid->is_hot('热卖产品')->editable('select', [1 => '是', 0 => '否']);
            $grid->is_rec('劲爆推荐')->editable('select', [1 => '是', 0 => '否']);
            $grid->sort('排序')->editable('textarea');;

            $grid->actions(function ($actions) {
                $actions->disableView();

            });

            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->add('批量复制产品', new CopyProduct());
                });
            });

            // 导出商品
            $grid->exporter(new ExcelExpoter($this));

            // 导入商品
            $grid->tools(function ($tools) {
                $tools->append(new GlobalUploadButton('batch/upload'));
            });
            // 默认过滤条件
            $user = Admin::user();
            // 如果选择了品牌只能更新某个品牌的产品
            if ($user->brand_id) {
                $grid->model()->where('brand_id', $user->brand_id);
            }
            if ($user->category_id) {
                $grid->model()->where('category_id', $user->category_id);
            }

            if ($user->province) {
                $grid->model()->where('province', $user->province);
            }

            if ($user->city) {
                $grid->model()->where('city', $user->city);
            }

            $grid->filter(function ($filter) {

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 添加标题过滤
                $filter->like('title', '商品名称');
                // 过滤上下架
                $filter->equal('on_sale', '是否上架')->radio([
                    '' => '所有',
                    1 => '是',
                    0 => '否',
                ]);

                // 过滤热卖
                $filter->equal('is_hot', '是否热卖')->radio([
                    '' => '所有',
                    1 => '是',
                    0 => '否',
                ]);

                // 过滤劲爆
                $filter->equal('is_rec', '是否劲爆推荐')->radio([
                    '' => '所有',
                    1 => '是',
                    0 => '否',
                ]);

                $filter->in('province', '产品所属省份')->multipleSelect('/admin/area/province')->load('city', '/admin/area/city');

                $filter->in('city', '产品所属地区')->multipleSelect('/admin/area/city');
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
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });
            $user = Admin::user();

            $form->tab('商品基本信息', function ($form) use ($user) {
                // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
                $form->text('title', '商品名称')->rules('required');
                // 商品分类
                $categories = Category::get(['id', DB::raw('title as text')])->when($user->category_id, function ($items, $value) {
                    return $items->filter(function ($item) use ($value) {
                        return $item->id == $value;
                    });
                })->mapWithKeys(function ($item) {
                    return [$item->id => $item->text];
                })->toArray();

                $form->select('category_id', '分类')->options($categories)->rules('required');
                // 品牌
                $brands = Brand::get(['id', DB::raw('title as text')])->when($user->brand_id, function ($items, $value) {
                    return $items->filter(function ($item) use ($value) {
                        return $item->id == $value;
                    });
                })->mapWithKeys(function ($item) {
                    return [$item->id => $item->text];
                })->toArray();
                $form->select('brand_id', '品牌')->options($brands)->rules('required');
                // 创建一个选择图片的框
                $form->image('image', '封面图')->rules('required|image');
                // 创建一个选择图片的框，移动端图片
                $form->image('app_image', '移动端封面图')->rules('nullable|image');
                // 价格
                $form->text('price', '单价')->rules('required|numeric|min:0.01');

                $form->text('stock', '剩余库存')->rules('required|integer|min:0');
                // 创建一组单选框
                $form->radio('on_sale', '上架')->options(['1' => '是', '0' => '否'])->default('1');

            })->tab('详细信息', function ($form) {
                $form->multipleImage('images', '滑动图')->removable();
                $form->multipleImage('app_images', '移动端滑动图')->removable();
                // 创建一个富文本编辑器
                $form->editor('description', '商品描述')->rules('required');
                // 创建一个富文本编辑器，移动端
                $form->editor('app_description', '移动端商品描述');
            })->tab('其他', function ($form) use ($user) {

                $form->select('province', '产品所属省份')->options(function ($id) use ($user) {
                    $provinces = ChinaArea::whereParentId(86)->get()->when($user->province, function ($items, $value) {
                        return $items->filter(function ($item) use ($value) {
                            return $item->id == $value;
                        });
                    })->unique();
                    if (!$user->province) $provinces->prepend(['id' => 0, 'name' => '全国']);
                    return $provinces->pluck('name', 'id');
                })->load('city', '/admin/area/city');
                $form->select('city', '产品所属地区')->options(function ($id) use ($user) {
                    if ($id) {
                        $cities = ChinaArea::options($id)->when($user->city, function ($items, $value) {
                            return $items->filter(function ($item, $key) use ($value) {
                                return $key == $value;
                            });
                        })->unique();
                        if (!$user->city) $cities->prepend('全部地区', 0);
                        return $cities;
                    }
                });

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

            return redirect()->route('admin.products.index');
        }
    }

    /**
     * 处理商品的数据导入
     * @param Collection $collection
     */
    public function handleUploadData(Collection $collection)
    {
        $user = Admin::user();
        $collection->each(function ($item) use ($user) {
            $product_id = $item[0];

            if ($product_id) { // 更新商品数据
                $product = Product::whereKey($product_id)->first();
                $product && $product->update([
                    'title' => $item[1],
                    'price' => $item[2],
                    'stock' => $item[3],
                    'sort' => $item[4],
                ]);
            } else { // 导入商品数据
                $insert_data = [
                    'title' => $item[1],
                    'price' => $item[2],
                    'stock' => $item[3],
                    'sort' => $item[4],
                ];
                if ($user->province) $insert_data['province'] = $user->province;
                if ($user->city) $insert_data['city'] = $user->city;
                // 开始插入数据
                Product::create($insert_data);
            }
        });
    }
}
