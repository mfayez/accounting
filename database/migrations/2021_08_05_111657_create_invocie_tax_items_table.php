<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvocieTaxItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invocie_tax_items', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('invoice_id');
			$table->string('tax_type');
			$table->unsignedDecimal('amount', $precision = 9, $scale = 3);
            $table->timestamps();

			$table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invocie_tax_items');
    }
}
