<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedDecimal('quantity', $precision = 9, $scale = 3);
			$table->unsignedDecimal('unit_value', $precision = 9, $scale = 3);
			$table->unsignedDecimal('sales_total', $precision = 9, $scale = 3);
			$table->unsignedDecimal('total', $precision = 9, $scale = 3);
			$table->unsignedDecimal('value_difference', $precision = 9, $scale = 3);
			$table->unsignedDecimal('total_taxable_fees', $precision = 9, $scale = 3);
			$table->unsignedDecimal('net_total', $precision = 9, $scale = 3);
			$table->unsignedDecimal('items_discount', $precision = 9, $scale = 3);
			$table->unsignedDecimal('items_discount_rate', $precision = 9, $scale = 3)->nullable();
			$table->unsignedDecimal('items_discount_amount', $precision = 9, $scale = 3)->nullable();
            $table->timestamps();

			$table->foreign('invoice_id')->references('id')->on('invoices');
			$table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
}
