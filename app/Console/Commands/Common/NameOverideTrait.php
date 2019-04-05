<?php
/**
 * Created by PhpStorm.
 * Users: Huangzb
 * Date: 2018/12/14
 * Time: 16:31
 */

namespace App\Console\Commands\Common;

trait NameOverideTrait
{
    /**
     * 获取输出
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name=$this->argument('name');
        return implode('/', array_map('studly_case', array_map('trim', array_filter(explode('/', $name)))));
    }
}
