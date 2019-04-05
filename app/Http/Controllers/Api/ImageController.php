<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Repositories\ImagesRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    protected $repo;

    public function __construct(ImagesRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 上传图片
     * @param ApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ApiRequest $request)
    {
        [$image] = $request->fields([$this, 'image'], true);

        $paths = collect(array_wrap($image))->map(function ($item) {
            return $this->repo->uploadImage($item, 0);
        });
        return ok($paths);
    }
}
