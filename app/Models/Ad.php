<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $guarded=['id'];

    // 广告分类
    public function ad_category()
    {
        return $this->belongsTo(AdCategory::class);
    }
}
