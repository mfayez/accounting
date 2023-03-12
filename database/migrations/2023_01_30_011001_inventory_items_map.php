<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InventoryItemsMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_items_map', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_item_code');
            $table->string('external_item_code');
            $table->enum('external_table', ['ETAItems', 'sb_items_map', 'ms_items_map']);
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
        Schema::dropIfExists('inventory_items_map');
    }
}
