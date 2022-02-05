<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ItemGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		 Schema::create('Group', function (Blueprint $table) {
            $table->unsignedBigInteger('Id')->unique();
			$table->string('name', 255);
        });
		 Schema::create('ItemGroup', function (Blueprint $table) {
            $table->string('itemCode', 255)->unique();
			$table->unsignedBigInteger('groupId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Group');
        Schema::dropIfExists('ItemGroup');
    }
}
