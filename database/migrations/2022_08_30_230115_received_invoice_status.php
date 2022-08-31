<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReceivedInvoiceStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ETAInvoices', function (Blueprint $table) {
            $table->foreignId('receiver_id')->index()->nullable();
            $table->string('reviewer');
            $table->string('comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ETAInvoices', function (Blueprint $table) {
            $table->dropColumn('receiver_id')->index()->nullable();
            $table->dropColumn('reviewer');
            $table->dropColumn('comment');
        });
    }
}
