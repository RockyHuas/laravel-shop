<?php

namespace App\Traits;

use App\Models\ArticleCategory;

trait ArticleCategoryTrait
{
    /**
     * 列表
     * @return ArticleCategory[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function articleCategoryGet()
    {
        return ArticleCategory::with(['articles' => function ($article_query) {
            $article_query->orderBy('sort', 'ASC');
        }])
            ->orderBy('sort', 'ASC')->get();
    }
}