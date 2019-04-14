<?php

namespace App\Traits;


use App\Models\Banner;
use App\Models\Brand;

trait brandTrait
{

    /**
     * 品牌列表查询
     * @return mixed
     */
    public function brandQuery($rec=0)
    {
        return Brand::when($rec,function ($query,$value){
            $query->where('is_rec', 1);
        })->orderBy('sort', 'ASC')->get();
    }
}