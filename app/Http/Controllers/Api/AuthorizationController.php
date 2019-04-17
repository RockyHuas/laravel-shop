<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Repositories\AuthorizationsRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthorizationController extends Controller
{
    protected $repo;

    public function __construct(AuthorizationsRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 用户登录
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ApiRequest $request)
    {
        [$name, $password] = $request->fields([$this,'name', 'password'],true);

        $result = $this->repo->login($name, $password);

        return ok($result);
    }
}
