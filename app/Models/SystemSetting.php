<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SystemSetting extends Model
{
    protected $guarded=['id'];
    protected $appends=['official_url','mini_url','logo_url'];

    // 公众号图片
    public function getOfficialUrlAttribute()
    {
        return $this->imageUrLConvert($this->attributes['gongzhonghao']);
    }

    // 小程序图片
    public function getMiniUrlAttribute()
    {
        return $this->imageUrLConvert($this->attributes['xiaochengxu']);
    }

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
