<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $guarded = ['id'];
    protected $dates = ['last_used_at'];
    protected $appends = ['full_Address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // 省份
    public function province()
    {
        return $this->belongsTo(ChinaArea::class,'province_id');
    }
    // 城市
    public function city()
    {
        return $this->belongsTo(ChinaArea::class,'city_id');
    }
    // 地区
    public function district()
    {
        return $this->belongsTo(ChinaArea::class,'district_id');
    }

    public function getFullAddressAttribute()
    {
        return "{$this->province->name}{$this->city->name}{$this->district->name}{$this->address}";
    }

    // 用户收货地址
    public function user_address()
    {
        return $this->hasOne(User::class);
    }
}
