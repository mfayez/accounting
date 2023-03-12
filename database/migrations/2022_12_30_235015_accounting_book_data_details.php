<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AccountingBookDataDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_book_data_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_book_data_id');
            $table->foreignId('accounting_chart_id');
            $table->decimal('debit', 11, 3);
            $table->decimal('credit', 11, 3);
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
        Schema::dropIfExists('accounting_book_data_details');
    }
}
