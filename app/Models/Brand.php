<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $guarded=['id'];
    protected $appends=['image_url','app_image_url'];

    // 关联的产品
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // PC 端图片
    public function getImageUrlAttribute()
    {
        return $this->imageUrLConvert($this->attributes['image']);
    }

    // 移动端图片
    public function getAppImageUrlAttribute()
    {
        return $this->attributes['app_image']
            ? $this->imageUrLConvert($this->attributes['app_image'])
            : $this->imageUrLConvert($this->attributes['image']);
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

}
