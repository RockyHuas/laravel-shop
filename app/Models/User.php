<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
//    protected $fillable=['status'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // 用户地址
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
    // 省份
    public function province()
    {
        return $this->belongsTo(ChinaArea::class);
    }

    // 市
    public function city()
    {
        return $this->belongsTo(ChinaArea::class);
    }

    // 区
    public function district()
    {
        return $this->belongsTo(ChinaArea::class);
    }

    // 购物车详情
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'user_favorite_products')
            ->withTimestamps()
            ->orderBy('user_favorite_products.created_at', 'desc');
    }
}
