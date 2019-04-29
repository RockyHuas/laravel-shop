<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">订单流水号：{{ $order->no }}</h3>
        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <form action="{{ route('admin.orders.change', [$order->id]) }}" method="post" class="form-inline">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <table class="table table-bordered">
                <tbody>
                <tr colspan="3">
                    <td>买家：</td>
                    <td colspan="3">{{ $order->user->name }}</td>
                </tr>
                {{--如果支付还没有发货，则可以修改地址信息--}}
                @if($order->ship_status == \App\Models\Order::SHIP_STATUS_PENDING )
                    <tr>
                        <td colspan="4">
                            <div class="form-inline">
                                <div class="form-group ">
                                    <label for="express_company" class="control-label">收货地址</label>
                                    <input type="text" name="address" value="{{ $order->address['address'] }}"
                                           class="form-control" placeholder="输入详细收货地址">
                                </div>
                                <div class="form-group ">
                                    <label for="express_no" class="control-label">联系人</label>
                                    <input type="text" name="contact_name" value="{{ $order->address['contact_name'] }}"
                                           class="form-control"
                                           placeholder="输入联系人">
                                </div>
                                <div class="form-group ">
                                    <label for="express_no" class="control-label">联系电话</label>
                                    <input type="text" name="contact_phone"
                                           value="{{ $order->address['contact_phone'] }}"
                                           class="form-control"
                                           placeholder="输入联系电话">
                                </div>
                            </div>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>收货地址</td>
                        <td colspan="3">{{ $order->address['address'] }}  {{ $order->address['contact_name'] }} {{ $order->address['contact_phone'] }}</td>
                    </tr>
                @endif

                <tr>
                    <td rowspan="{{ $order->items->count() + 1 }}">商品列表</td>
                    <td>商品名称</td>
                    @if($order->ship_status == \App\Models\Order::SHIP_STATUS_PENDING )
                        <td colspan="2">单价及数量</td>
                    @else
                        <td>单价</td>
                        <td>数量</td>
                    @endif
                </tr>

                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->title }}</td>
                        @if($order->ship_status == \App\Models\Order::SHIP_STATUS_PENDING )
                            <td colspan="2">
                                <div method="post" class="form-inline">
                                    <div class="form-group ">
                                        <label for="express_company" class="control-label">单价：</label>
                                        <input type="text" name="product[{{$item->id}}][price]"
                                               value="{{ $item->price  }}"
                                               class="form-control" placeholder="输入详细收货地址">
                                    </div>
                                    <div class="form-group ">
                                        <label for="express_company" class="control-label">&nbsp;&nbsp;数量：</label>
                                        <input type="text" name="product[{{$item->id}}][amount]"
                                               value="{{ $item->amount  }}"
                                               class="form-control" placeholder="输入详细收货地址">
                                    </div>
                                </div>
                            </td>
                        @else
                            <td>￥{{ $item->price  }}</td>
                            <td>{{ $item->amount  }}</td>
                        @endif
                    </tr>
                @endforeach


                <!-- 支付状态 -->
                @if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING )
                    <tr>
                        <td colspan="4">
                            <div class="form-inline">
                                <div class="form-group ">
                                    <label for="express_company" class="control-label">支付方式：</label>
                                    <div class="radio">
                                        <label class="radio-inline">
                                            @foreach($pay_methods as $item)
                                                <input type="radio" name="pay_id" value="{{$item->id}}"
                                                       class="minimal pay_id" {{ $item->id == $order->pay_id?'checked':'' }}>
                                                &nbsp;{{$item->title}}&nbsp;&nbsp;
                                            @endforeach
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class="control-label">&nbsp;&nbsp;支付金额：</label>
                                    <input type="text" name="pay_amount"
                                           value="{{ $order->pay_amount ?:$order->total_amount  }}"
                                           class="form-control" placeholder="支付金额">
                                </div>
                                <button style="margin-left: 20px;" type="submit" class="btn btn-success">支付</button>
                            </div>
                        </td>
                    </tr>
                @else
                    <!-- 展示支付时间以及支付方式 -->
                    <tr>
                        <td rowspan="2">支付信息</td>
                        <td>支付方式</td>
                        <td>支付时间</td>
                        <td>支付金额</td>
                    </tr>
                    <tr>
                        <td>{{ $order->pay->title }}</td>
                        <td>{{ $order->paid_at->format('Y-m-d H:i:s') }}</td>
                        <td>￥{{ $order->pay_amount?$order->pay_amount:$order->total_amount }}</td>
                    </tr>
                @endif

                <tr>
                    <td>发货状态：</td>
                    <td colspan="3">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
                </tr>
                <!-- 订单发货开始 -->
                <!-- 如果订单未发货，展示发货表单 -->
                @if($order->ship_status !== \App\Models\Order::SHIP_STATUS_RECEIVED )
                        <tr>
                            <td colspan="4">
                                <div class="form-inline">
                                    <div class="form-group ">
                                        <label for="express_company" class="control-label">物流公司</label>
                                        <input type="text" id="express_company"
                                               name="express_company"
                                                value="{{ $order->ship_data['express_company'] ? $order->ship_data['express_company'] :'顺丰快递'}}"
                                               class="form-control" placeholder="输入物流公司">
                                    </div>
                                    <div class="form-group ">
                                        <label for="express_no" class="control-label">物流单号</label>
                                        <input type="text" id="express_no" name="express_no"
                                               value="{{ $order->ship_data['express_no'] ? $order->ship_data['express_no'] :''}}"
                                               class="form-control"
                                               placeholder="输入物流单号">
                                    </div>
                                    <button style="margin-left: 20px;" type="submit" class="btn btn-success">发货</button>
                                </div>
                            </td>
                        </tr>
                @else
                    <!-- 否则展示物流公司和物流单号 -->
                    <tr>
                        <td>物流公司：</td>
                        <td>{{ $order->ship_data['express_company'] }}</td>
                        <td>物流单号：</td>
                        <td>{{ $order->ship_data['express_no'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="4">
                        <div class="form-inline">
                            <div class="form-group ">
                                <label for="express_company" class="control-label">订单备注</label>
                                <textarea type="text" id="express_company" name="note" value=""
                                          class="form-control" placeholder="输入订单备注"></textarea>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>

            </table>
            <div style="width:100%;text-align: center">
                <a href="{{ route('admin.orders.print', [$order->id]) }}" target="_blank"
                   class="btn btn-success">打印订单</a>

                @if($order->ship_status == \App\Models\Order::SHIP_STATUS_DELIVERED )

                    <button style="margin-left: 20px;" type="button" id="confirm" class="btn btn-success">完成</button>
                @endif
                @if(!$order->paid_at)
                    <button style="margin-left: 20px;" type="button" id="cancel" class="btn btn-success">作废</button>
                @endif
                @if($order->ship_status != \App\Models\Order::SHIP_STATUS_RECEIVED )

                    <button style="margin-left: 20px;" type="submit" class="btn btn-success">保存</button>
                @endif



            </div>

        </form>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // 同意 按钮的点击事件
        $('#confirm').click(function() {
            swal({
                title: '确认已完成？',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: "确认",
                cancelButtonText: "取消",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return $.ajax({
                        url: '{{ route('admin.orders.confirm', [$order->id]) }}',
                        type: 'POST',
                        data: JSON.stringify({
                            confirm: 1,
                            _token: LA.token,
                        }),
                        contentType: 'application/json',
                    });
                },
                allowOutsideClick: false
            }).then(function (ret) {
                // 如果用户点击了『取消』按钮，则不做任何操作
                if (ret.dismiss === 'cancel') {
                    return;
                }
                swal({
                    title: '操作成功',
                    type: 'success'
                }).then(function() {
                    // 用户点击 swal 上的按钮时刷新页面
                    location.reload();
                });
            });
        });

        $('#cancel').click(function() {
            swal({
                title: '确认取消？',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: "确认",
                cancelButtonText: "取消",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return $.ajax({
                        url: '{{ route('admin.orders.cancel', [$order->id]) }}',
                        type: 'POST',
                        data: JSON.stringify({
                            cancel: 1,
                            _token: LA.token,
                        }),
                        contentType: 'application/json',
                    });
                },
                allowOutsideClick: false
            }).then(function (ret) {
                // 如果用户点击了『取消』按钮，则不做任何操作
                if (ret.dismiss === 'cancel') {
                    return;
                }
                swal({
                    title: '操作成功',
                    type: 'success'
                }).then(function() {
                    // 用户点击 swal 上的按钮时刷新页面
                    location.reload();
                });
            });
        });
    });
</script>