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
            ->where('on_sale',1)
            ->orderBy('sort', 'ASC')->get(['id','title','price','category_id','image','app_image']);
    }

    /**
     * 劲爆产品查询
     * @return mixed
     */
    public function productHotQuery()
    {
        return Product::City()
            ->where('is_hot', 1)
            ->where('on_sale',1)
            ->orderBy('sort', 'ASC')->get(['id','title','price','category_id','image','app_image']);
    }

    /**
     * 获取商品品牌
     * @param int $category_id
     * @return mixed
     */
    public function productBrandsQuery(int $category_id = 0)
    {
        return Product::City()->with('brand')
            ->where('on_sale',1)
            ->when($category_id, function ($query, $value) {
                $query->whereCategoryId($value);
            })->get()
            ->map(function ($product) {
                return $product->brand;
            })->filter()->unique('id')->sortBy('sort')->values();
    }

    /**
     * 商品价格
     * @param int $category_id
     * @param int $brand_id
     * @return mixed
     */
    public function productPricesQuery(int $category_id = 0, int $brand_id = 0)
    {
        return Product::City()
            ->where('on_sale',1)
            ->when($category_id, function ($query, $value) {
                $query->whereCategoryId($value);
            })->when($brand_id, function ($query, $value) {
                $query->whereBrandId($value);
            })->get(['id', 'price']);
    }


    /**
     * 查询商品
     * @param int $size
     * @param string $keywords
     * @param int $category_id
     * @param int $brand_id
     * @param string $min_price
     * @param string $max_price
     * @param int $sort
     * @param string $sort_order
     * @return mixed
     */
    public function productQuery($size = 8, $keywords = '', int $category_id = 0, int $brand_id = 0, string $min_price = '',
                                 string $max_price = '', int $sort = 0, string $sort_order = 'asc')
    {
        return Product::City()
            ->where('on_sale',1)
            ->when($keywords, function ($query, $value) {
                $query->where('search_title', 'like', "%".
                    trim(str_replace(' ', '', $value))."%");
            })
            ->when($category_id, function ($query, $value) {
                $query->whereCategoryId($value);
            })->when($brand_id, function ($query, $value) {
                $query->whereBrandId($value);
            })->when($min_price, function ($query, $value) {
                $query->where('price', '>=', $value);
            })->when($max_price, function ($query, $value) {
                $query->where('price', '<=', $value);
            })->orderBy($sort == 0 ? 'id' : ($sort == 1 ? 'price' : 'sold_count'), $sort_order)
            ->paginate($size,['id','title','price','category_id']);
    }

}