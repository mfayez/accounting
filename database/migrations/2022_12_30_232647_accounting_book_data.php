<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AccountingBookData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_book_data', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('reference_code');
            $table->foreignId('accounting_book_id')->constrained('accounting_book');
            $table->dateTime('transaction_date');
            $table->decimal('debit', 11, 3);
            $table->decimal('credit', 11, 3);
            $table->string('approved_by')->nullable();
            $table->string('accepted_by')->nullable();
            $table->string('rejected_by')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->enum('status', ['In Review', 'Approved', 'Accepted', 'Rejected']);
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
        Schema::dropIfExists('accounting_book_data');
    }
}
