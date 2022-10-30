<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixRegionCityWidth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("alter table Address change regionCity regionCity varchar(400);");
        DB::unprepared("alter table Address change landmark landmark varchar(400);");
        DB::unprepared("alter table Address change additionalInformation additionalInformation varchar(400);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("alter table Address change regionCity regionCity varchar(50);");
        DB::unprepared("alter table Address change landmark landmark varchar(50);");
        DB::unprepared("alter table Address change additionalInformation additionalInformation varchar(50);");
    }
}
