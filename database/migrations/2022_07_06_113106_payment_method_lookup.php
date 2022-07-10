<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PaymentMethodLookup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rc_payment_methods', function (Blueprint $table) {
            $table->string("key", 5)->primary();
            $table->string("value");
        });
        $data = [
            ['key'=>'C', 'value'=> 'Cash'],
            ['key'=>'V', 'value'=> 'Visa'],
            ['key'=>'CC', 'value'=> 'Cash with contractor'],
            ['key'=>'VC', 'value'=> 'Visa with contractor'],
            ['key'=>'VO', 'value'=> 'Vouchers'],
            ['key'=>'PR', 'value'=> 'Promotion'],
            ['key'=>'GC', 'value'=> 'Gift Card'],
            ['key'=>'P', 'value'=> 'Points'],
            ['key'=>'O', 'value'=> 'Others']
        ];
        DB::table('rc_payment_methods')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rc_payment_methods');
    }
}
