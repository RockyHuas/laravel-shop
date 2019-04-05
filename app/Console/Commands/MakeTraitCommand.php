<?php

namespace App\Console\Commands;

use App\Console\Commands\Common\NameOverideTrait;
use App\Console\Commands\Common\TypeSetTrait;
use Illuminate\Console\GeneratorCommand;

class MakeTraitCommand extends GeneratorCommand
{
    use TypeSetTrait, NameOverideTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:base_trait {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建trait';

    protected $trait_type=['Query', 'Create', 'Update', 'Delete'];

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->setType();

        $choice = $this->choice('请选择需要生成的trait', array_merge(['Exit','All'], $this->trait_type), 1);

        $this->traitFactory($choice);
    }

    /**
     * 根据类型产生不同的trait
     * @param $choice
     */
    public function traitFactory($choice)
    {
        if ($choice == 'Exit') {
            $this->info('退出执行');
        } elseif ($choice == 'All') {
            array_map([$this,'genetatorTrait'], $this->trait_type);
        } else {
            $this->genetatorTrait($choice);
        }
    }

    /**
     * 开始生成trait
     * @param string $type
     */
    public function genetatorTrait(string $type)
    {
        $name = $this->getNameInput() . ucfirst($type) . 'Trait';

        $this->call('make:class', ['name' => $name,'--stub'=>$this->getStub()]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/trait.stub';
    }
}
