<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source', 32)->default('')->comment('来源：jd|amazon');
            $table->string('sku', 32)->default('');
            $table->string('title', 128)->default('')->comment('商品标题');
            $table->string('img', 512)->default('')->comment('商品图片');
            $table->integer('count')->default(0)->comment('已抓取次数');
            $table->timestamp('last_fetch')->default('2011-09-01 00:00:00')->comment('最后一次抓取时间');
            $table->timestamps();
            $table->unique(['source', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skus');
    }
}
