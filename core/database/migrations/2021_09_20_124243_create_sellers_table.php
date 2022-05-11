<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 40)->nullable();
            $table->string('lastname', 40)->nullable();
            $table->string('username', 40)->nullable();
            $table->string('email', 40);
            $table->string('country_code', 40);
            $table->string('mobile', 40);
            $table->decimal('balance', 28,8)->default(0);
            $table->string('password');
            $table->string('image')->nullable();
            $table->text('address')->nullable()->comment('Contains full address like country, city, zip, etc.');
            $table->boolean('status')->default(1)->comment('0:banned, 1:active');
            $table->boolean('ev')->default(0)->comment('0:email unverified, 1:email verified');
            $table->boolean('sv')->default(0)->comment('0:sms unverified, 1:sms verified');
            $table->string('remember_token')->nullable();
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
        Schema::dropIfExists('sellers');
    }
}
