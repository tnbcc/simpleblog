<?php

use Illuminate\Database\Seeder;

class GoodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('goods')->insert([
          'title' => '2018学院风夏季棉麻吊带中长款连衣裙刺绣收腰少女小清新两件套裙',
          'description' => '货号: L0082',
          'price' => 100.00,
          'stock' => 10,
          'status' => 1,
          'created_at' => date('Y-m-d H:i:s', time()),
          'updated_at' => date('Y-m-d H:i:s', time()),
      ]);
    }
}
