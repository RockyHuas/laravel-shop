<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelDataInterface;
use App\Admin\Extensions\ExcelExpoter;
use App\Events\OrderShipped;
use App\Exceptions\InternalException;
use App\Models\ChinaArea;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pay;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Http\Requests\Admin\HandleRefundRequest;
use Illuminate\Support\Collection;

class OrdersController extends Controller implements ExcelDataInterface
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('订单列表');
            $content->body($this->grid());
        });
    }

    public function show(Order $order)
    {
        return Admin::content(function (Content $content) use ($order) {
            $content->header('查看订单');
            // body 方法可以接受 Laravel 的视图作为参数

            // 获取支付方式
            $pay_methods = Pay::get();

            $content->body(
                view('admin.orders.show', ['order' => $order, 'pay_methods' => $pay_methods]));
        });
    }

    public function delete($id)
    {
        \DB::transaction(function () use ($id) {
            Order::whereIn('id', explode(',', $id))->get()->each(function ($order) {
                $order->delete();
            });
        });

        return response()->json([
            'status' => true,
            'message' => '',
        ]);
    }

    /**
     * 打印订单
     * @param Order $order
     * @return Content
     */
    public function print(Order $order)
    {
        return Admin::content(function (Content $content) use ($order) {
            $content->header('打印订单');
            // body 方法可以接受 Laravel 的视图作为参数

            // 获取支付方式
            $pay_methods = Pay::get();

            echo view('orders.print', ['order' => $order, 'pay_methods' => $pay_methods]);
            exit;
        });
    }

    public function exportData($data)
    {
        return $data->map(function ($order) {
            $order = Order::with('items.product.brand')->whereKey($order['id'])->firstOrFail();
            return $order->items->map(function ($order_item) use ($order) {
                $no = $order->no;
                $province = '';
                $city = '';
                $brand_name = data_get($order_item, 'product.brand.title');
                $order_item->product->province && $province = ChinaArea::whereKey($order_item->product->province)->first();

                $order_item->product->city && $city = ChinaArea::whereKey($order_item->product)->first();
                $province_name = $province ? $province->name : '全国';
                $city_name = $city ? $city->name : '全部地区';
                // 创建订单
                $user_name = data_get($order, 'user.name');
                $contact_name = data_get($order, 'address.contact_name');
                $address = data_get($order, 'address.address');
                $contact_phone = data_get($order, 'address.contact_phone');
                $product_name = data_get($order_item, 'product.title');
                $product_price = data_get($order_item, 'price');
                $product_amount = data_get($order_item, 'amount');
                $order_total = $order->total_amount;
                $pay_status = $order->paid_at ? '已支付' : '未支付';
                $order_pay_total = $order->pay_amount;
                $note = $order->note;
                $pay_note = $order->pay_note;
                $order_time = $order->created_at->toDateTimeString();
                return compact('no', 'user_name', 'contact_name',
                    'address', 'contact_phone', 'product_name', 'product_price',
                    'product_amount', 'brand_name', 'province_name', 'city_name',
                    'order_total', 'pay_status', 'order_pay_total',
                    'note', 'pay_note', 'order_time');

            });
        })->flatten(1)->prepend(['订单编号', '买家', '收货人', '联系地址', '手机号码',
            '商品名称', '商品价格', '商品数量', '品牌', '省份', '城市',
            '订单总额', '支付状态', '支付金额', '订单备注', '支付备注', '订单创建时间']);
    }

    public function ship(Order $order, Request $request)
    {
        // 判断当前订单是否已支付
        if ($order->paid_at && $order->ship_status == Order::SHIP_STATUS_PENDING) {
            // Laravel 5.5 之后 validate 方法可以返回校验过的值
            $data = $this->validate($request, [
                'express_company' => ['required'],
                'express_no' => ['required'],
            ], [], [
                'express_company' => '物流公司',
                'express_no' => '物流单号',
            ]);
            // 将订单发货状态改为已发货，并存入物流信息
            $order->update([
                'ship_status' => Order::SHIP_STATUS_DELIVERED,
                // 我们在 Order 模型的 $casts 属性里指明了 ship_data 是一个数组
                // 因此这里可以直接把数组传过去
                'ship_data' => $data,
            ]);
        }

        // 返回上一页
        return redirect()->back();
    }


    public function change(Order $order, Request $request)
    {
        $product = $request->product;
        $pay_id = $request->pay_id;
        $address = $request->address ?: '';
        $contact_name = $request->contact_name ?: '';
        $contact_phone = $request->contact_phone ?: '';
        $express_company = $request->express_company;
        $express_no = $request->express_no;
        $pay_amount = $request->pay_amount;
        $note = $request->note;
        $pay_note = $request->pay_note;

        \DB::transaction(function () use (
            $order, $product, $pay_id, $address,
            $contact_name, $contact_phone, $express_company, $express_no,
            $pay_amount, $note, $pay_note
        ) {
            // 如果存在需要更新的产品信息，则更新
            $product && collect($product)->each(function ($item, $key) {
                OrderItem::whereKey($key)->firstOrFail()->update($item);
            });

            $update_data = [];
            $update_data['note'] = $note;
            $update_data['pay_note'] = $pay_note;
            // 如果提交了支付信息
            if ($pay_id) {
                $update_data['pay_id'] = $pay_id;
                $update_data['paid_at'] = now()->toDateTimeString();
            }

            // 如果提交了地址信息
            if ($address || $contact_name || $contact_phone) {
                $update_data['address'] = compact('address', 'contact_phone', 'contact_name');
            }

            // 如果提交了物流信息
            if ($express_no && $express_company) {
                $update_data['ship_data'] = compact('express_no', 'express_company');
                $update_data['ship_status'] = Order::SHIP_STATUS_DELIVERED;
            }

            // 如果提交了支付金额信息
            if ($pay_amount) {
                $update_data['pay_amount'] = $pay_amount;
            }
            // 更新订单信息
            $order->update($update_data);

            // 如果发货了，需要发微信通知
            if ($express_no && $express_company) {
                event(new OrderShipped($order));
            }
        });

        // 返回上一页
        return redirect()->back();
    }


    public function confirm(Order $order)
    {
        $order->update([
            'ship_status' => Order::SHIP_STATUS_RECEIVED
        ]);
        return $order;
    }

    public function cancel(Order $order)
    {
        $order->update([
            'closed' => 1
        ]);
        return $order;
    }

    protected function grid()
    {
        return Admin::grid(Order::class, function (Grid $grid) {
            // 只展示已支付的订单，并且默认按支付时间倒序排序
            $grid->model()->orderBy('created_at', 'desc');

            $grid->no('订单流水号')->modal('编辑订单信息', function ($model) {

                $pay_methods = Pay::get();

                return view('admin.orders.show', ['order' => $model, 'pay_methods' => $pay_methods]);

//                return new Table(['ID', '内容', '发布时间'], $comments->toArray());
            });
            // 展示关联关系的字段时，使用 column 方法
            $grid->column('user.name', '买家');
            $grid->address('收货地址')->display(function ($address) {
                return $address['address'] . '  ' . $address['contact_name'] . '  ' . $address['contact_phone'];
            });
            $grid->total_amount('总金额')->sortable();
            $grid->pay_amount('支付金额')->display(function ($pay_amount) {
                return $pay_amount ?: 0;
            });;
            $grid->paid_at('支付状态')->display(function ($paid_at) {
                return $this->paid_status;
            });
            $grid->ship_status('物流状态')->display(function ($value) {
                return Order::$shipStatusMap[$value];
            });
            // 禁用创建按钮，后台不需要创建订单
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                // 禁用删除和编辑按钮
                $actions->disableEdit();
            });

            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 买家
                $filter->where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('name', 'like', "%{$this->input}%");
                    });

                }, '买家');

                $filter->like('no', '订单号');

                // 产品所属省份
                $filter->where(function ($query) {
                    $query->where('product_province', 'like',
                        "%{$this->input}%")
                        ->orWhere('product_province', '全国');
                }, '产品所属省份');

                // 产品所属城市
                $filter->where(function ($query) {
                    $query->where('producproduct_cityt_province', 'like',
                        "%{$this->input}%")
                        ->orWhere('product_city', '全部地区');
                }, '产品所属地区');


                $filter->like('address', '地址，收货人或手机号');

                // 支付状态
                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'yes':
                            $query->whereNotNull('paid_at');
                            break;
                        case 'no':
                            $query->whereNull('paid_at');
                            break;
                    }
                }, '支付状态', 'name_for_url_shortcut')->radio([
                    '' => '所有',
                    'yes' => '已支付',
                    'no' => '未支付',
                ]);

                $filter->equal('ship_status', '物流状态')->radio(array_merge(['' => '所有'], Order::$shipStatusMap));

            });

            $grid->exporter(new ExcelExpoter($this));
        });
    }
}
