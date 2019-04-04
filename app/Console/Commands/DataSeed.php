<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class DataSeed extends Command
{
    protected $signature = 'seed:run';
    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = '运行一个 seeder';

    protected $seeder_path;

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->seeder_path = base_path('database/seeds');


        $files = $this->getSeeders();

        $seeder_file = $this->chooseSeeder($files);
        if (strtolower($seeder_file) === 'exit') {
            $this->line('退出执行。');
            return;
        } else {
            $this->line('开始执行：' . $seeder_file);
        }

        \Artisan::call('db:seed', [
            '--class' => $seeder_file
        ], $this->output);
        $this->info('执行完毕。');
    }

    private function getSeeders()
    {
        $files = ['EXIT'];
        foreach (Finder::create()->files()->name('*.php')->in($this->seeder_path) as $file) {
            $filename = pathinfo($file->getRelativePathname(), PATHINFO_FILENAME);
            $desc = data_get(new $filename, 'description', '');
            $filename .= '   --' . $desc;
            $files[] = $filename;
        }
        return $files;
    }

    private function chooseSeeder(array $seeder_files)
    {
        $choose_file = $this->choice('选择要执行的Seeder?', $seeder_files, false);
        $choose_file = trim(explode('--', $choose_file)[0]);
        return $choose_file;
    }
}
