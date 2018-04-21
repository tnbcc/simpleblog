<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    /**
     * 秒杀入口
     *
     * @return \Illuminate\Http\Response
     */
    public function spike(Request $request)
    {
        /*
         * 1、将用户id放进user:1的列表list
         * 2、将库存数放进goods:1的列表list
         * 3、将抢购结果放进order:1的集合set
         */
        $user_id = random_int(1,9999); // 模拟的用户id
        //$goods_id = isset($request->goods_id) ? (int)$request->goods_id : 0; // 商品id
          $goods_id = 1;
        // 获取并储存商品详情的库存信息到redis
        Redis::del('goods_num:'.$goods_id); // 删除key,正常情况下需注释
        if (!Redis::get('goods_num:'.$goods_id)) {
            $goods_num = DB::table('goods')->where('id', $goods_id)->select('stock')->first();
            if ($goods_num) Redis::set('goods_num:'.$goods_id, $goods_num->stock);
        }

        // 测试输出，但实际不输出
        echo Redis::get('goods_num:'.$goods_id)."<br>";

        // 储存用户信息到redis
        Redis::lpush('user_id:'.$goods_id, $user_id);

        // 测试输出，但实际不输出
        $length = Redis::llen('user_id:'.$goods_id);
        for($i = 1; $i <= $length; $i++){
            echo Redis::lindex('user_id:'.$goods_id, $i)."<br>";
        }
    }

    /*
     * 执行队列，处理秒杀订单
     */
    public function run(Request $request)
    {
        //$goods_id = isset($request->goods_id) ? (int)$request->goods_id : 0; // 商品id
        $goods_id = 1;
		$goods_num = (int)Redis::get('goods_num:'.$goods_id);
        //Redis::del('order_num:'.$goods_id); // 删除key,正常情况下需注释

        while ($user_id = Redis::lpop('user_id:'.$goods_id))
        {
            $order_num = (int)Redis::scard('order_num:'.$goods_id);
            echo ($goods_num - $order_num)."<br>";

            // 判断库存是否够用
            if (($goods_num - $order_num) <= 0) {
                echo '库存不够！';
                break;
            }

            // 判断用户是否重复抢购
            if (Redis::sismember('order_num:'.$goods_id, $user_id)) {
                echo '该用户已经秒杀过了，不能重复秒杀！';
                break;
            }

            // 通过事务处理商品库存和生成订单

            DB::transaction(function () use ($goods_id, $user_id)
            {
                // 修改商品库存
                DB::table('goods')->where('id',$goods_id)->decrement('stock');
                // 生成新的订单
                DB::table('orders')->insert(
                    [
                        'order_number' => $this->build_order_no(),
                        'user_id' => $user_id,
                        'price_product' => 100,
                        'price_actual' => 100,
                        'order_status' => 1,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time()),
                    ]
                );
                // 将该用户id放入order:1的集合set
                Redis::sadd('order_num:'.$goods_id, $user_id);
            });
        }
    }

    /*
     * 生成唯一订单号
     */
    protected function build_order_no()
    {
        return date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}
