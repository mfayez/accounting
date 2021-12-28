<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Permissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('user_issuer', function (Blueprint $table) {
			$table->integer('issuer_id');
			$table->foreignId('user_id');
			$table->unique(['issuer_id', 'user_id']);
			$table->foreign('issuer_id')->references('Id')->on('Issuer')
				->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('user_id')->references('id')->on('users')
            	->onDelete('cascade')->onUpdate('cascade');
    	});
		Schema::create('user_receiver', function (Blueprint $table) {
			$table->integer('receiver_id');
			$table->foreignId('user_id');
			$table->unique(['receiver_id', 'user_id']);
			$table->foreign('receiver_id')->references('Id')->on('Receiver')
				->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('user_id')->references('id')->on('users')
            	->onDelete('cascade')->onUpdate('cascade');
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issuer_user');
        Schema::dropIfExists('receiver_user');
    }
}
