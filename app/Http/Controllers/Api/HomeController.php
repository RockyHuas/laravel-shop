<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Models\Article;
use App\Models\Pay;
use App\Models\SystemSetting;
use App\Models\UserLoginDetail;
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

        // 在这里记录用户的登录
        if(\Auth::check()){
            UserLoginDetail::create([
                'user_id' => \Auth::id(),
                'ip' => $request->ip()
            ]);    
        }

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
    public function getHotProducts(ApiRequest $request)
    {
        $result = $this->repo->productHotQuery();

        return ok($result);
    }

    /**
     * 获取系统设置
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWebSiteSetting(ApiRequest $request)
    {
        $result = SystemSetting::whereKey(1)->first();

        return ok($result);
    }

    /**
     * 导航文章
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNavArticles(ApiRequest $request)
    {
        $result = Article::where('is_rec', 1)->orderBy('nav_sort', 'asc')->get();

        return ok($result);
    }

    /**
     * 获取支付方式
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayments(ApiRequest $request)
    {
        $result = Pay::get();

        return ok($result);
    }

    /**
     * 文章详情
     * @param ApiRequest $request
     * @param Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArticleInfo(ApiRequest $request, Article $article)
    {
        return ok($article);
    }
}
