<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;



class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            '商品１'=>['本です。',2000,1],
            '商品２'=>['本です。',3000,1],
            '商品３'=>['本です。',4000,1]
        ];

        foreach($array as $key=>$vals){
            Product::create([
                'name' => $key,
                'description' => $vals[0],
                'price' => $vals[1],
                'category_id' => $vals[2]
            ]);
        }
    }
}
