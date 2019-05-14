<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING => '未退款',
        self::REFUND_STATUS_APPLIED => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS => '退款成功',
        self::REFUND_STATUS_FAILED => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED => '已收货',
    ];

    protected $guarded = ['id'];
    protected $appends = ['shipping_status', 'paid_status', 'created_time', 'number_status','total_num'];

    protected $casts = [
        'closed' => 'boolean',
        'reviewed' => 'boolean',
        'address' => 'json',
        'payment_data' => 'json',
        'ship_data' => 'json',
        'extra' => 'json',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->no) {
                    return false;
                }
            }
        });

        // 订单删除则关联的 item 也要删除
        static::deleting(function ($model) {
            OrderItem::where('order_id', $model->id)->get()->each(function ($order_item) {
                $order_item->delete();
            });
        });
    }

    // 订单所属用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 支付方式
    public function pay()
    {
        return $this->belongsTo(Pay::class);
    }

    // 订单关联的产品
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class);
    }

    /**
     * 调整订单的生成
     * @return bool|int|mixed|string
     */
    public static function findAvailableNo()
    {
        for ($i = 0; $i < 10; $i++) {
            $order= static::query()
                ->whereDate('created_at', now()->toDateString())
                ->orderBy('id','DESC')->first();
            $no = $order ? ($order->no) + 1 : date('Ymd') . '0000';
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
            usleep(100);
        }
        \Log::warning(sprintf('find order no failed'));

        return false;
    }

    public static function getAvailableRefundNo()
    {
        do {
            // Uuid类可以用来生成大概率不重复的字符串
            $no = Uuid::uuid4()->getHex();
            // 为了避免重复我们在生成之后在数据库中查询看看是否已经存在相同的退款订单号
        } while (self::query()->where('refund_no', $no)->exists());

        return $no;
    }

    /**
     * 发货状态
     * @return mixed
     */
    public function getShippingStatusAttribute()
    {
        if ($this->ship_status) {
            return static::$shipStatusMap[$this->ship_status];
        }
    }

    public function getPaidStatusAttribute()
    {
        return $this->paid_at ? '已支付' : ($this->closed ? '已取消' : '未支付');
    }
    public function getCreatedTimeAttribute()
    {
        return $this->created_at->toDateString();
    }

    public function getTotalNumAttribute()
    {
        $items=$this->items;
        return $items->sum('amount');
    }

    public function getNumberStatusAttribute()
    {
        $status = 0;
        if (!$this->paid_at && $this->closed == 0) $status = 1; // 待支付
        if ($this->paid_at && $this->closed == 0 && $this->ship_status == static::SHIP_STATUS_PENDING) $status = 2; // 待发货
        if ($this->paid_at && $this->closed == 0 && $this->ship_status == static::SHIP_STATUS_DELIVERED) $status = 3; // 已发货
        if ($this->paid_at && $this->closed == 0 && $this->ship_status == static::SHIP_STATUS_RECEIVED) $status = 4; // 已完成
        if ($this->closed == 1) $status = 5; // 已取消

        return $status;
    }

}
