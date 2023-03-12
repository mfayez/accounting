<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValDiffSbItemMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sb_items_map', function (Blueprint $table) {
            $table->decimal('Val_Diff', 11, 3)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sb_items_map', function (Blueprint $table) {
            $table->dropColumn('Val_Diff');
        });
    }
}
