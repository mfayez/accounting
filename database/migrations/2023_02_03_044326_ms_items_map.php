<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MsItemsMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_items_map', function (Blueprint $table) {
            $table->string("MSCode", 30)->primary();
            $table->string('ItemNameA')->nullable();
            $table->string('ItemNameE')->nullable();
            $table->string('ETACode')->nullable();
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
        Schema::dropIfExists('ms_items_map');
    }
}