<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class WangEditor extends Field
{
    protected $view = 'admin.wang-editor';

    protected static $css = [
        '/vendor/wangEditor-3.1.1/release/wangEditor.min.css',
    ];

    protected static $js = [
        '/vendor/wangEditor-3.1.1/release/wangEditor.min.js',
    ];

    public function render()
    {
        $name = $this->formatName($this->column);
        $this->script = <<<EOT
var E = window.wangEditor
var editor = new E('#{$this->id}');
editor.customConfig.uploadFileName = 'image[]';
editor.customConfig.uploadImgHeaders = {
    'X-CSRF-TOKEN': $('input[name="_token"]').val()
}
editor.customConfig.zIndex = 0;
editor.customConfig.uploadImgServer = '/api/images';
editor.customConfig.onchange = function (html) {
    $('input[name=$name]').val(html);
}
editor.customConfig.uploadImgHooks = {
    customInsert: function (insertImg, result, editor) {
        var data=result.data.result;
        if (typeof(data.length) != "undefined") {
            for (var i = 0; i <= data.length - 1; i++) {
                var j = i;
                var url = data[i].path;
                insertImg(url);
            }
        }

    }
}
editor.create();
// var editor = new wangEditor('{$this->id}');
//     editor.create();
EOT;
        return parent::render();
    }
}