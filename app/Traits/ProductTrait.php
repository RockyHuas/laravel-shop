<?php

namespace App\Traits;

use App\Models\Product;

trait ProductTrait
{

    /**
     * 推荐产品查询
     * @return mixed
     */
    public function productRecQuery()
    {
        return Product::City()
            ->where('is_rec', 1)
            ->orderBy('sort', 'ASC')->get();
    }

    /**
     * 劲爆产品查询
     * @return mixed
     */
    public function productHotQuery()
    {
        return Product::City()
            ->where('is_hot', 1)
            ->orderBy('sort', 'ASC')->get();
    }
}