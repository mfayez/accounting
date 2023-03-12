<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InventoryItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('arabic_name');
            $table->string('english_name');
            $table->decimal('sell_price', 11, 3);
            $table->decimal('min_sell_price', 11, 3);
            $table->decimal('purchase_price', 11, 3);
            $table->boolean('sellable')->default(true);
            $table->boolean('buyable')->default(true);
            $table->enum('storage_identifier', ['product_id', 'lot_id', 'serial_number']);
            $table->string('measurement_unit');
            $table->decimal('weight');
            $table->string('weight_unit');
            $table->decimal('dim_length');
            $table->decimal('dim_width');
            $table->decimal('dim_height');
            $table->string('dim_unit');
            $table->decimal('area');
            $table->string('area_unit');
            $table->decimal('volume');
            $table->string('volume_unit');
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
        Schema::dropIfExists('inventory_items');
    }
}
