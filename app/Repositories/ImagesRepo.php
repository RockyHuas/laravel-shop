<?php

namespace App\Repositories;

use App\Extensions\Libs\ImageUploadExtension;
use App\Traits\Common\ImageCreateTrait;
use Illuminate\Http\UploadedFile;
use Auth;

class ImagesRepo
{
    protected $uploader;


    public function __construct(ImageUploadExtension $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * 上传图片
     * @param UploadedFile $image 文件
     * @param int $type 图片类型 1-头像，0-其他
     * @return string
     */
    public function uploadImage(UploadedFile $image, int $type)
    {
        // 获取上传图片的用户
        $user_id = Auth::id() ?: 0;

        // 如果是头像类型的图片，则需要进行裁剪
        $size = $type == 1 ? 362 : false;

        // 根据 type 不同，设置不同文件夹名称
        $folder = $type == 1 ? 'avatar' : 'others';

        // 获取图片保存的路径
        $path = $this->uploader->save($image, str_plural($folder), $user_id, $size);

        return $path;
    }
}