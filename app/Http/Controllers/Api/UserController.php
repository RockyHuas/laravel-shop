<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Repositories\UsersRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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
        $params = $request->fields([$this,
            'name',
            'phone',
            'password' => ['rule' => 'required|string|min:6|confirmed'],
            'shop_name',
            'province_id',
            'city_id',
            'district_id'
        ]);

        $result = $this->repo->userCreate($params);

        return ok($result);
    }

    /**
     * 修改用户密码
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ApiRequest $request)
    {
        [$password, $old_password] = $request->fields([$this,
            'password' => ['rule' => 'required|string|min:6|confirmed'],
            'old_password',
        ], true);

        $user = \Auth::user();

        throw_on(!Hash::check($old_password, $user->password), '旧密码不正确');

        $user->update(['password' => bcrypt($password)]);

        return ok(true);
    }
}
