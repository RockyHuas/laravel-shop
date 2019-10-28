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

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::saving(function ($model) {
            if ($model->title) {
                $model->search_title = trim(str_replace(' ', '', $model->title));
            }
        });
    }

    /**
     * 只查询本地方的产品
     * @param $query
     * @return mixed
     */
    public function scopeCity($query)
    {
        if(\Auth::guard('api')->check()){
            $user = \Auth::user();
            return $query->where(function ($query2) use ($user) {
                $query2->where(function ($province_query) use ($user) {
                    $province_query->whereNull('province')
                        ->orWhereIn('province', [0, $user->province_id]);
                })->where(function ($city_query) use ($user) {
                    $city_query->whereNull('city')
                        ->orWhereIn('city', [0, $user->city_id]);
                });
            });    
        } else {
            return $query;
        }
        
    }

    // PC 端图片
    public function getImageUrlAttribute()
    {
        return data_get($this,'image') ? $this->imageUrLConvert($this->attributes['image']) : null;
    }

    // 移动端图片
    public function getAppImageUrlAttribute()
    {
        return data_get($this,'app_image')
            ? $this->imageUrLConvert($this->attributes['app_image'])
            : $this->getImageUrlAttribute();
    }

    // PC 端多图片
    public function getImagesUrlAttribute()
    {
        $images = data_get($this,'images') ?: Arr::wrap($this->getAttribute('image'));

        return collect($images)->map(function ($image) {
            return $image ? $this->imageUrLConvert($image) : null;
        })->filter();
    }

    // 移动端多图片
    public function getAppImagesUrlAttribute()
    {
        $images = data_get($this,'app_images');

        return $images
            ? collect($images)->map(function ($image) {
                return $this->imageUrLConvert($image);
            })->filter()
            : $this->getImagesUrlAttribute();
    }


    /**
     * 转换图片
     * @param string $url
     * @return string
     */
    protected function imageUrLConvert($url)
    {
        if (!$url) return $url;
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
