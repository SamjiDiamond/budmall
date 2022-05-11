<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('logo')->nullable();
            $table->string('cover')->nullable();
            $table->time('opens_at')->nullable();
            $table->time('closed_at')->nullable();
            $table->string('address')->nullable();
            $table->text('social_links')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
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
        Schema::dropIfExists('shops');
    }
}
