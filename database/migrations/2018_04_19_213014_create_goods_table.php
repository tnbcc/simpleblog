<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('goods', function (Blueprint $table) {
          $table->increments('id');
          $table->string('title')->default('')->comment('产品标题');
          $table->text('description')->comment('产品描述');
          $table->decimal('price', 12, 2)->default(0)->comment('价格');
          $table->integer('stock')->default(0)->comment('库存');
          $table->tinyInteger('status')->default(0)->comment('状态：１、正常；2、下架；3、禁用');
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
        Schema::dropIfExists('goods');
    }
}
