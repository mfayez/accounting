<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SbAddBuToBranchesMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sb_branches_map', function (Blueprint $table) {
            $table->string('sb_bu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sb_branches_map', function (Blueprint $table) {
            $table->dropColumn('sb_bu');
        });
    }
}
