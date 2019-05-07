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
            'shop_name',
            'phone',
            'province_id',
            'city_id',
            'district_id',
            'password' => ['rule' => 'required|string|min:6|confirmed'],
            'open_id'
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

    /**
     * 获取用户信息
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(ApiRequest $request)
    {
        $user = \Auth::user();

        $user->addHidden('password');

        return ok($user);
    }

    public function updatePassword(ApiRequest $request)
    {
        [$name, $phone, $password] = $request->fields([$this,
            'name',
            'phone',
            'password' => ['rule' => 'required|string|min:6|confirmed']
        ], true);

        $result = $this->repo->userPasswordUpdate($name, $phone, $password);

        return ok($result);
    }

    /**
     * 绑定微信
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bindWeChat(ApiRequest $request)
    {
        [$open_id] = $request->fields([$this,
            'open_id' => ['rule' => 'required|string']
        ], true);

        $user = \Auth::user();

        throw_on($user->open_id == $open_id, '该微信已被绑定');

        $user->update(['open_id' => $open_id]);

        return ok($user);
    }

    /**
     * 更新用户资料
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(ApiRequest $request)
    {
        $params = $request->fields([$this,
            'name',
            'shop_name',
            'province_id',
            'city_id',
            'district_id',
        ]);

        $result = $this->repo->userProfileUpdate(\Auth::id(),$params);

        return ok($result);
    }
}
