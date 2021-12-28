<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixWidth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::unprepared("alter table Invoice change totalSalesAmount  totalSalesAmount decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change netAmount netAmount  decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalAmount totalAmount decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalDiscountAmount totalDiscountAmount decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change extraDiscountAmount extraDiscountAmount decimal(11,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalItemsDiscountAmount totalItemsDiscountAmount decimal(11,3) NOT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		DB::unprepared("alter table Invoice change totalSalesAmount  totalSalesAmount decimal(9,3) NOT NULL;");
		DB::unprepared("alter table Invoice change netAmount netAmount  decimal(9,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalAmount totalAmount decimal(9,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalDiscountAmount totalDiscountAmount decimal(9,3) NOT NULL;");
		DB::unprepared("alter table Invoice change extraDiscountAmount extraDiscountAmount decimal(9,3) NOT NULL;");
		DB::unprepared("alter table Invoice change totalItemsDiscountAmount totalItemsDiscountAmount decimal(9,3) NOT NULL;");
    }
}
