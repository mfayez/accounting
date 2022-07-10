<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            //$table->date(
            $table->date("dateTimeIssued");
            $table->string("receiptNumber");
            $table->string("uuid")->default("");
            $table->string("previousUUID")->default("");
            $table->string("referenceOldUUID")->nullable();
            $table->string("currency")->default("EGP");
            $table->unsignedDecimal("exchangeRate")->nullable()->default(1);
            $table->string("sOrderNameCode")->nullable();
            $table->string("orderdeliveryMode")->nullable();
            $table->unsignedDecimal("grossWeight")->nullable();
            $table->unsignedDecimal("netWeight")->nullable();
            $table->foreignId('pos_id');
            $table->string("buyer_type");
            $table->string("buyer_id");
            $table->string("buyer_name");
            $table->string("buyer_mobileNumber")->nullable();
            $table->string("buyer_paymentNumber")->nullable();
            $table->unsignedDecimal("totalSales")->default(0);
            $table->unsignedDecimal("totalCommercialDiscount")->default(0);
            $table->unsignedDecimal("totalItemsDiscount")->default(0);
            $table->unsignedDecimal("netAmount")->default(0);
            $table->unsignedDecimal("feesAmount")->default(0);
            $table->unsignedDecimal("totalAmount")->default(0);
            $table->string("paymentMethod");
            $table->unsignedDecimal("adjustment")->default(0);
            $table->string("status");
            $table->string("statusReason");
            $table->timestamps();
        });

        Schema::create('rc_taxTotal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id');
            $table->string("taxType");
            $table->unsignedDecimal("amount");
            $table->timestamps();
        });

        Schema::create('rc_receiptItem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id');
            $table->string("internalCode");
            $table->string("description");
            $table->string("itemType");
            $table->string("itemCode");
            $table->string("unitType");
            $table->unsignedDecimal("quantity");
            $table->unsignedDecimal("unitPrice");
            $table->unsignedDecimal("netSale");
            $table->unsignedDecimal("totalSale");
            $table->unsignedDecimal("total");
            $table->timestamps();
        });

        Schema::create('rc_taxableItem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiptItem_id');
            $table->string("taxType");
            $table->string("subType");
            $table->unsignedDecimal("amount");
            $table->unsignedDecimal("rate");
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
        Schema::dropIfExists('rc_taxableItem');
        Schema::dropIfExists('rc_receiptItem');
        Schema::dropIfExists('rc_taxTotal');
        Schema::dropIfExists('receipts');
        
    }
}
