<?php
/**
 * Created by PhpStorm.
 * Users: Huangzb
 * Date: 2018/12/14
 * Time: 16:10
 */

namespace App\Console\Commands\Common;

trait TypeSetTrait
{
    /**
     * 设置类型
     */
    protected function setType()
    {
        if (!$this->type) {
            $name = $this->qualifyClass($this->getNameInput());

            $path = $this->getPath($name);

            $this->type=$path;
        }
    }
}
