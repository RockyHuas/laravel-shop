<?php

namespace App\Models;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded=['id'];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
    ];

    // PC 端图片
    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }

    // 移动端图片
    public function getAppImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['app_image'], ['http://', 'https://'])) {
            return $this->attributes['app_image'];
        }
        return \Storage::disk('public')->url($this->attributes['app_image']);
    }

    // 关联的品牌
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // 关联的产品分类
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
