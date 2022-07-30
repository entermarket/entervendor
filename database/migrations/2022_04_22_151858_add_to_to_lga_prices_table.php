<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToToLgaPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lga_prices', function (Blueprint $table) {
            $table->string('to');
            $table->foreignId('to_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lga_prices', function (Blueprint $table) {
            $table->dropColumn('to','to_id');
        });
    }
}
