<?php

namespace App\Repositories;

use App\Traits\brandTrait;
use App\Traits\ProductCatgoryTrait;
use App\Traits\ProductTrait;

class ProductRepo
{
    use brandTrait;
    use ProductTrait;
    use ProductCatgoryTrait;

    /**
     * 商品价格分组
     * @param int $category_id
     * @param int $brand_id
     * @param int $price_num
     * @return array
     */
    public function getProductPrices(int $category_id = 0, int $brand_id = 0, int $price_num = 6)
    {
        $product_prices = $this->productPricesQuery($category_id, $brand_id)->pluck('price');

        return $this->getGoodsPrice($product_prices->min(), $product_prices->max(), $price_num);
    }

    /**
     * 商品价格分组
     * @param $min
     * @param $max
     * @param int $show_price_num
     * @return array
     */
    public function getGoodsPrice($min, $max, $show_price_num = 5)
    {
        $good_prices = compact('min','max');

        if ($good_prices['min'] == null && $good_prices['max'] == null) {
            return [];
        }
        //计算商品价格区间
        $per_price = ceil(($good_prices['max'] - $good_prices['min']) / $show_price_num);

        //返回数据组装    
        $result = [];

        if ($per_price > 0) {
            $result = ['0-' . $per_price]; //定义第一个区间 
            $step_price = $per_price;

            for ($add_price = $step_price + 1; $add_price < $good_prices['max'];) {
                if (count($result) == $show_price_num) break;

                //下个区间结束值    
                $step_price = $add_price + $per_price;
                //除去首个数字外 剩下所有为 9     效果 2999
                $step_price = substr(intval($step_price), 0, 1) . str_repeat('9', (strlen(intval($step_price)) - 1));

                $result[] = $add_price . '-' . $step_price; //下个区间段

                $add_price = $step_price + 1; // 下个区间开始 当前最大值 +1 
            }
            //置换max价格  把数据最后的 值 替换为 最大值
            $result[count($result) - 1] = str_replace("-" . $step_price, "-" . ceil($good_prices['max']), $result[count($result) - 1]);
        }
        return $result;
    }

}