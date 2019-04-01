<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $guarded=['id'];

    // 分类下的文章
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
