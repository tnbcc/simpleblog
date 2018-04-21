<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('orders', function (Blueprint $table) {
          $table->increments('id');
          $table->string('order_number',255)->unique()->default('')->comment('订单号');
          $table->integer('user_id')->default(0)->comment('用户id');
          $table->decimal('price_product', 12, 2)->default(0)->comment('产品总价');
          $table->decimal('price_actual', 12, 2)->default(0)->comment('实际付款');
          $table->tinyInteger('order_status', false)->default(0)->comment('订单状态：0 无效订单；1 已下单；2 已支付；3 已发货；4 已收货；5 已取消；6 正在退款中...；7 已退款；8 正在退货中...；9 已退货；10 正在换货中...；11 已换货');
          $table->softDeletes();
          $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
