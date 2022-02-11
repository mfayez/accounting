<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixMissingWidthColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::unprepared("alter table Discount change amount amount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table ETAInvoices change totalSales totalSales decimal(15,5) NOT NULL;");
		DB::unprepared("alter table ETAInvoices change totalDiscount totalDiscount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table ETAInvoices change netAmount netAmount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table ETAInvoices change total total decimal(15,5) NOT NULL;");
		DB::unprepared("alter table Value change amountEGP amountEGP decimal(15,5) DEFAULT NULL;");
		DB::unprepared("alter table Value change  amountSold amountSold decimal(15,5) DEFAULT NULL;");
		DB::unprepared("alter table Value change  currencyExchangeRate currencyExchangeRate decimal(15,5) DEFAULT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		DB::unprepared("alter table Discount change amount amount decimal(9,0) NOT NULL;");
		DB::unprepared("alter table ETAInvoices change totalSales totalSales decimal(12,2) NOT NULL;");
		DB::unprepared("alter table ETAInvoices change totalDiscount totalDiscount decimal(12,2) NOT NULL;");
		DB::unprepared("alter table ETAInvoices change netAmount netAmount decimal(12,2) NOT NULL;");
		DB::unprepared("alter table ETAInvoices change total total decimal(12,2) NOT NULL;");
		DB::unprepared("alter table Value change amountEGP amountEGP decimal(9,3) DEFAULT NULL;");
		DB::unprepared("alter table Value change  amountSold amountSold decimal(9,3) DEFAULT NULL;");
		DB::unprepared("alter table Value change  currencyExchangeRate currencyExchangeRate decimal(9,3) DEFAULT NULL;");
    }
}
