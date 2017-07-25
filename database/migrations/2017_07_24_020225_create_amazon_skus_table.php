<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmazonSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amazon_skus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku', 16)->default('')->unique();
            $table->string('title', 255)->default('');
            $table->integer('count')->default(0);
            $table->timestamp('last_fetch');
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
        Schema::dropIfExists('amazon_skus');
    }
}
