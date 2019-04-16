<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Product;

trait ProductCatgoryTrait
{

    /**
     * 产品分类查询
     * @return mixed
     */
    public function productCatgoryQuery()
    {
        return Category::get();
    }
}