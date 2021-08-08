<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('customer_id');
			$table->string('taxpayer_activity_code');
			$table->string('internal_id');
			$table->string('purchase_order_reference')->nullable();
			$table->string('purchase_order_description')->nullable();
			$table->string('sales_order_reference')->nullable();
			$table->string('sales_order_description')->nullable();
			$table->string('proforma_invoice_number')->nullable();
			$table->unsignedDecimal('total_sales_amount', $precision = 9, $scale = 3);
			$table->unsignedDecimal('total_discount_amount', $precision = 9, $scale = 3);
			$table->unsignedDecimal('net_amount', $precision = 9, $scale = 3);
			$table->unsignedDecimal('extra_discount_amount', $precision = 9, $scale = 3);
			$table->unsignedDecimal('total_amount', $precision = 9, $scale = 3);
			$table->foreign('customer_id')->references('id')->on('customers');
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
        Schema::dropIfExists('invoices');
    }
}
