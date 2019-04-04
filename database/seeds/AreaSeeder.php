<?php

use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{

    public $description="初始化地址库";
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $area_data=json_decode(app(\Illuminate\Filesystem\Filesystem::class)->get(database_path('seeds').'/AreaData.json'),true);

        collect($area_data)->each(function($item,$key){
            collect($item)->each(function($item2,$key2)use($key){
                // 开始创建地址库
                \App\Models\ChinaArea::create([
                    'id'=>$key2,
                    'name'=>$item2,
                    'parent_id'=>$key
                ]);
            });
        });
    }
}
