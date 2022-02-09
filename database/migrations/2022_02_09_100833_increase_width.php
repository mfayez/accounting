<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseWidth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::unprepared("alter table InvoiceLine change salesTotal salesTotal  decimal(15,5) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change total total  decimal(15,5) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change valueDifference valueDifference  decimal(15,5) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change totalTaxableFees totalTaxableFees  decimal(15,5) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change netTotal netTotal  decimal(15,5) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change itemsDiscount itemsDiscount  decimal(15,5) NOT NULL;");
		DB::unprepared("alter table Invoice change totalSalesAmount  totalSalesAmount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table Invoice change netAmount netAmount  decimal(15,5) NOT NULL;");
		DB::unprepared("alter table Invoice change totalAmount totalAmount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table Invoice change totalDiscountAmount totalDiscountAmount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table Invoice change extraDiscountAmount extraDiscountAmount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table Invoice change totalItemsDiscountAmount totalItemsDiscountAmount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table TaxableItem change amount amount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table TaxableItem change rate rate decimal(15,5) NOT NULL;");
		DB::unprepared("alter table TaxTotal change amount amount decimal(15,5) NOT NULL;");
		DB::unprepared("alter table Receiver change name name varchar(100);");
		DB::unprepared("alter table Issuer change name name varchar(100);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		DB::unprepared("alter table InvoiceLine change salesTotal salesTotal  decimal(11,3) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change total total  decimal(11,3) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change valueDifference valueDifference  decimal(11,3) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change totalTaxableFees totalTaxableFees  decimal(11,3) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change netTotal netTotal  decimal(11,3) NOT NULL;");
		DB::unprepared("alter table InvoiceLine change itemsDiscount itemsDiscount  decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalSalesAmount  totalSalesAmount decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change netAmount netAmount  decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalAmount totalAmount decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalDiscountAmount totalDiscountAmount decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change extraDiscountAmount extraDiscountAmount decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalItemsDiscountAmount totalItemsDiscountAmount decimal(11,3) NOT NULL;");
		DB::unprepared("alter table TaxableItem change amount amount decimal(9,3) NOT NULL;");
		DB::unprepared("alter table TaxableItem change rate rate decimal(9,3) NOT NULL;");
		DB::unprepared("alter table TaxTotal change amount amount decimal(9,3) NOT NULL;");
		DB::unprepared("alter table Receiver change name name varchar(50);");
		DB::unprepared("alter table Issuer change name name varchar(50);");
    }
}
