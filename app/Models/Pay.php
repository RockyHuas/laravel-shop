<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pay extends Model
{
    protected $guarded=['id'];
    protected $appends=['logo_url'];

    // LOGO
    public function getLogoUrlAttribute()
    {
        return $this->imageUrLConvert($this->attributes['logo']);
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
