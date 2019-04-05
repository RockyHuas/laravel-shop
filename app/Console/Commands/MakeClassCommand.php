<?php

namespace App\Console\Commands;

use App\Console\Commands\Common\NameOverideTrait;
use App\Console\Commands\Common\TypeSetTrait;
use Illuminate\Console\GeneratorCommand;

class MakeClassCommand extends GeneratorCommand
{
    use TypeSetTrait,NameOverideTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:class 
                                    {name : 文件路径} 
                                    {--S|stub= : 模板内容}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建一个新的类';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->setType();

        parent::handle();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($stub=$this->option('stub')) {
            return $stub;
        }

        return __DIR__.'/stubs/common.stub';
    }
}
