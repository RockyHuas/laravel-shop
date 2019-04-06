<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded=['id'];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
        'images'=>'json',
        'app_images'=>'json'
    ];

    // PC 端图片
    public function getImageUrlAttribute()
    {
        return $this->imageUrLConvert($this->attributes['image']);
    }

    // 移动端图片
    public function getAppImageUrlAttribute()
    {
        return $this->imageUrLConvert($this->attributes['app_image']);
    }

    // PC 端多图片
    public function getImagesUrlAttribute()
    {
        $images=$this->getAttribute('images');

        return collect($images)->map(function($image){
            return $this->imageUrLConvert($image);
        });
    }

    // 移动端多图片
    public function getAppImagesUrlAttribute()
    {
        $images=$this->getAttribute('app_images');

        return collect($images)->map(function($image){
            return $this->imageUrLConvert($image);
        });
    }


    /**
     * 转换图片
     * @param string $url
     * @return string
     */
    protected function imageUrLConvert(string $url)
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }
        return \Storage::disk('public')->url($url);
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
