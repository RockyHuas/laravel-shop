<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Models\Product;
use App\Models\ProductViewDetail;
use App\Repositories\ProductRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    protected $repo;

    public function __construct(ProductRepo $repo)
    {
        $this->repo = $repo;
    }


    /**
     * 产品分类
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories(ApiRequest $request)
    {
        $result = $this->repo->productCatgoryQuery();

        return ok($result);
    }

    /**
     * 商品详细
     * @param ApiRequest $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ApiRequest $request, Product $product)
    {
        $product->loadMissing(['brand', 'category']);

        //  产品访问数量加 1
        $product->increment('review_count', 1);

        // 保存访问记录
        ProductViewDetail::create([
            'user_id' => \Auth::id(),
            'product_id' => $product->id,
            'ip' => $request->ip()
        ]);

        return ok($product);
    }

    /**
     * 获取产品品牌
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBrands(ApiRequest $request)
    {
        [$category_id] = $request->fields([$this,
            'category_id' => ['default' => 0]
        ], true);

        $reuslt = $this->repo->productBrandsQuery($category_id);

        return ok($reuslt);
    }

    /**
     * 获取商品价格
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrices(ApiRequest $request)
    {
        [$category_id, $brand_id, $price_num] = $request->fields([$this,
            'category_id' => ['default' => 0],
            'brand_id' => ['default' => 0],
            'price_num' => ['default' => 6],
        ], true);

        $reuslt = $this->repo->getProductPrices($category_id, $brand_id, $price_num);

        return ok($reuslt);
    }

    /**
     * 获取产品列表
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(ApiRequest $request)
    {
        [$size, $keywords, $category_id, $brand_id, $min_price, $max_price, $sort, $sort_order] = $request->fields([$this,
            'size' => ['default' => 8],
            'keywords',
            'category_id' => ['default' => 0],
            'brand_id' => ['default' => 0],
            'min_price',
            'max_price',
            'sort',
            'sort_order'
        ], true);

        $reuslt = $this->repo->productQuery($size, $keywords, $category_id, $brand_id, $min_price, $max_price, $sort, $sort_order);

        return ok($reuslt);
    }
}
