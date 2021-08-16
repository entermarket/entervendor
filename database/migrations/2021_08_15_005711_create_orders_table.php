<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('order_no');
            $table->string('status')->default('pending');;
            $table->integer('total_amount');
            $table->integer('tax')->default(0);
            $table->integer('commission')->default(0);
            $table->integer('shipping_charges')->default(0);
            $table->string('promo')->nullable();
            $table->integer('discount')->default(0);
            $table->integer('grand_total');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
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
