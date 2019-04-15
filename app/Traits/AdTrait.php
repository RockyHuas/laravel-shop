<?php

namespace App\Traits;

use App\Models\Ad;

trait AdTrait
{

    /**
     * Banner 列表
     * @return mixed
     */
    public function adHomeFind()
    {
        return Ad::where('ad_category_id', 1)->first();
    }
}