<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pay extends Model
{
    protected $guarded=['id'];
    protected $appends=['logo_url','scan_url'];

    // LOGO
    public function getLogoUrlAttribute()
    {
        return $this->imageUrLConvert($this->attributes['logo']);
    }

    public function getScanUrlAttribute()
    {
        return $this->attributes['scan'] ?
        $this->imageUrLConvert($this->attributes['scan'])
        :'';
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
