<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SbOrderStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sb_order_status', function (Blueprint $table) {
            $table->id();
            $table->integer('order_status');
            $table->string('description');
            $table->timestamps();
        });
        $item = new App\Models\SBOrderStatus(["order_status" => 0,"description" => "مفتوح"]);
		$item->save();
        $item = new App\Models\SBOrderStatus(["order_status" => 2,"description" => "مؤكد"]);
        $item->save();
        $item = new App\Models\SBOrderStatus(["order_status" => 4,"description" => "مفوتر"]);
        $item->save();
        $item = new App\Models\SBOrderStatus(["order_status" => 6,"description" => "معلق"]);
        $item->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
