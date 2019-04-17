<?php

namespace App\Traits;

use App\Models\Ad;

trait AdTrait
{

    /**
     * 首页广告
     * @return mixed
     */
    public function adHomeFind()
    {
        return Ad::where('ad_category_id', 1)->first();
    }
}