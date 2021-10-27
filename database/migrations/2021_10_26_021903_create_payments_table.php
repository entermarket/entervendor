<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('reference')->nullable();
            $table->integer('amount');
            $table->string('status');
            $table->string('network')->nullable();
            $table->string('number')->nullable();
            $table->string('service_id')->nullable();
            $table->text('token');
            $table->string('transactionRef')->nullable();
            $table->string('message')->default('initiated');
            $table->integer('service')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
