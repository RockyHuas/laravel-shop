<?php

namespace App\Imports;



use Illuminate\Support\Collection;

/*
 * 如果是导入数据，则需要在具体的 repo 中实现该方法
 * */
interface DataImportInterface
{
    public function importData(Collection $collection);
}
