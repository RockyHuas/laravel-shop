<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\URL;

/**
 * 全局上传数据按钮
 */

class GlobalUploadButton extends AbstractTool
{
    protected $url;
    public function __construct(string $url)
    {
        $this->url = URL::current().'/'.$url;
    }


    public function render()
    {
        $options = [
            $this->url   => '导入数据',
        ];

        return view('tools.GlobalUploadButton', compact('options'));
    }
}