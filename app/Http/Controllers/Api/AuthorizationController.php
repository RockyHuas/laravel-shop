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
        [$name, $password,$open_id,$mini_id] = $request->fields([$this, 'name', 'password','open_id','mini_id'], true);

        $result = $this->repo->login($name, $password,$open_id,$mini_id);

        return ok($result);
    }

    /**
     * 退出登录
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ApiRequest $request)
    {
        $result = $this->repo->delete_token();

        return ok($result);
    }
}
