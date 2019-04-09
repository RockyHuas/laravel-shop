<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Repositories\UsersRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    protected $repo;

    public function __construct(UsersRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 用户注册
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ApiRequest $request)
    {
        $params = $request->fields([
            'phone',
            'password',
            'shop_name',
            'province_id',
            'city_id',
            'district_id'
        ]);

        $result = $this->repo->userCreate($params);

        return ok($result);
    }
}
