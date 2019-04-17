<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
        'images' => 'json',
        'app_images' => 'json'
    ];
    protected $appends = ['image_url', 'app_image_url', 'images_url', 'app_images_url'];

    /**
     * 只查询本地方的产品
     * @param $query
     * @return mixed
     */
    public function scopeCity($query)
    {
        $user = \Auth::user();
        return $query->where(function ($query2) use ($user) {
            $query2->whereIn('province', [0, $user->province_id])
                ->whereIn('city', [0, $user->city_id]);
        });
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

    // PC 端多图片
    public function getImagesUrlAttribute()
    {
        $images = $this->getAttribute('images') ?: Arr::wrap($this->getAttribute('image'));

        return collect($images)->map(function ($image) {
            return $this->imageUrLConvert($image);
        });
    }

    // 移动端多图片
    public function getAppImagesUrlAttribute()
    {
        $images = $this->getAttribute('app_images');

        return $images
            ? collect($images)->map(function ($image) {
                return $this->imageUrLConvert($image);
            })
            : $this->getImagesUrlAttribute();
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

    public function getAppDescriptionAttribute($value)
    {
        return $value ?: $this->description;
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
