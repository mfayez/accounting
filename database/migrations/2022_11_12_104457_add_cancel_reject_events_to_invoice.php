<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelRejectEventsToInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Invoice', function (Blueprint $table) {
            $table->dateTime('cancelRequestDate')->nullable();
            $table->dateTime('rejectRequestDate')->nullable();
            $table->dateTime('cancelRequestDelayedDate')->nullable();
            $table->dateTime('rejectRequestDelayedDate')->nullable();
            $table->dateTime('declineCancelRequestDate')->nullable();
            $table->dateTime('declineRejectRequestDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Invoice', function (Blueprint $table) {
            $table->dropColumn('cancelRequestDate');
            $table->dropColumn('rejectRequestDate');
            $table->dropColumn('cancelRequestDelayedDate');
            $table->dropColumn('rejectRequestDelayedDate');
            $table->dropColumn('declineCancelRequestDate');
            $table->dropColumn('declineRejectRequestDate');
        });
    }
}
