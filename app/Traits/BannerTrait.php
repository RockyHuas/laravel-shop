<?php

namespace App\Traits;


use App\Models\Banner;

trait BannerTrait
{

    /**
     * Banner 列表
     * @return mixed
     */
    public function bannerQuery()
    {
        return Banner::orderBy('sort', 'ASC')->get();
    }
}