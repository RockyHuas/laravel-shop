<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $guarded=['id'];
    protected $dates = ['last_used_at'];
    protected $appends=['full_Address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }

    // 用户收货地址
    public function user_address()
    {
        return $this->hasOne(User::class);
    }
}
