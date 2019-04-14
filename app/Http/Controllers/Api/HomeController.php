<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Repositories\HomeRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    protected $repo;

    public function __construct(HomeRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 文章分类
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArticleCategory(ApiRequest $request)
    {
        $result = $this->repo->articleCategoryGet();

        return ok($result);
    }

    /**
     * banner
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBanners(ApiRequest $request)
    {
        $result = $this->repo->bannerQuery();

        return ok($result);
    }

    /**
     * 首页广告
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAds(ApiRequest $request)
    {
        $result = $this->repo->adHomeFind();

        return ok($result);
    }

    /**
     * 推荐首页品牌
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecBrands(ApiRequest $request)
    {
        $result = $this->repo->brandQuery(1);

        return ok($result);
    }

    /**
     * 推荐产品
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecProducts(ApiRequest $request)
    {
        $result = $this->repo->productRecQuery();

        return ok($result);
    }

    /**
     * 劲爆产品
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecHotducts(ApiRequest $request)
    {
        $result = $this->repo->productHotQuery();

        return ok($result);
    }
}
