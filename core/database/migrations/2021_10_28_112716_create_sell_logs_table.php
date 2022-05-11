<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->integer('product_id');
            $table->string('order_id');
            $table->decimal('product_price',18,8);
            $table->decimal('after_commission',18,8);
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
        Schema::dropIfExists('sell_logs');
    }
}
