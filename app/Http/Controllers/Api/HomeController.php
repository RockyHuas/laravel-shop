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
     *
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArticleCategory(ApiRequest $request)
    {
        $result = $this->repo->articleCategoryGet();

        return ok($result);
    }
}
