<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>订货单</title>
    <style>
        .order {
            width: 750px;
        }
        .order h2{ text-align: center;}
        .order p{border: 1px solid #ccc; margin: 0; line-height: 38px; border-bottom: 0; display: flex; padding: 0 10px; box-sizing: border-box;}
        .order p span:nth-of-type(1){ min-width: 40%;}
        .order p span:nth-of-type(2){ min-width: 30%; text-align: center; border-left: 1px solid #ccc;}
        .order p span:nth-of-type(3){ min-width: 30%; text-align: center; border-left: 1px solid #ccc;}
        .order table{ width: 100%; border-collapse: collapse; text-align: center; line-height: 40px;}
        .order table tr th,.order table tr td{ border: 1px solid #ccc;}
        .order table tr.spe td{ text-align: right; padding-right: 50px;}
    </style>
</head>
<body>
<div class="order">
    <h2>订货单</h2>
    <p><span>单号：{{$order->no}}</span><span>客户名称：{{ $order->user->name }}</span><span>制单时间：{{now()->toDateString()}}</span></p>
    <p><span>收货信息：{{ $order->address['address'] }}  {{ $order->address['contact_name'] }} {{ $order->address['contact_phone'] }}</span></p>
    <table>
        <thead>
        <tr>
            <th>序号</th><th >商品名称</th><th>数量</th><th>单价</th><th>小计</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $key=>$item)
            <tr>
                <td>{{$key+1}}</td><td style="text-align: left;">{{ $item->product->title }}</td><td>{{ $item->amount}}</td><td>{{ $item->price}}</td><td>￥{{ $item->amount *$item->price}}</td>
            </tr>
        @endforeach
        <tr class="spe">
            <td colspan="8">合计金额：￥{{ $order->pay_amount ?:$order->total_amount  }}</td>
        </tr>
        </tbody>
        <p><span>备注：{{ $order->note }}</span></p>
    </table>

</div>
</body>
</html>