<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter
{
    private $title;     //导出表头字段
    private $filename;  //导出的文件名
    private $fields;    //导出的数据库中字段

    protected $repo;


    public function __construct(ExcelDataInterface $repo)
    {
        parent::__construct();

       $this->repo=$repo;
    }

    public function export()
    {
        Excel::create('export', function ($excel) {
            $excel->sheet('Shee1', function ($sheet) {
                // 这段逻辑是从表格数据中取出需要导出的字段
                $rows =$this->repo->exportData(collect($this->getData()));

                $sheet->rows($rows);
            });

        })->export('xls');
    }
}