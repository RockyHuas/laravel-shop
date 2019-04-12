<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $guarded = ['id'];

    // 所属的文章分类
    public function article_category()
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    public function getAppDescriptionAttribute($value)
    {
        return $value ? :$this->description;
    }
}
