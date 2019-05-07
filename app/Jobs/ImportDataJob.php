<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Excel;

class ImportDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file_path;
    protected $repo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $file_path,$repo)
    {
        $this->file_path=$file_path;
        $this->repo=$repo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //获取当前文本编码格式
        $content = file_get_contents($this->file_path);
        $fileType = mb_detect_encoding($content, array('UTF-8', 'GBK', 'LATIN1', 'BIG5'));

        app(Excel::class)->load($this->file_path, function ($reader) {
            $rows = $reader->all();
            // 处理导入的数据
            $this->repo->handleUploadData($rows);
        }, $fileType);//以指定的编码格式打开文件
    }
}
