<?php

use Illuminate\Database\Seeder;

class ProductSearchTitleSeeder extends Seeder
{
    public $description="批量调整产品搜索字段";
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       \App\Models\Product::chunk(100, function ($products) {
           $products->each(function($product){
              $product->update([
                'search_title'=>trim(str_replace(' ', '', $product->title))
              ]);
           });
       });
    }
}
