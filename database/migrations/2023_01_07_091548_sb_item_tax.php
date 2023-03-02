<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SbItemTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sb_item_tax', function (Blueprint $table) {
            $table->string("SBCode", 30);
            $table->string('taxType');
            $table->string('taxSubtype');
            $table->unsignedDecimal('rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sb_item_tax');
    }
}
