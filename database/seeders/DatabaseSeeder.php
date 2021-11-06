<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\Address;
use App\Models\Delivery;
use App\Models\Discount;
use App\Models\InvoiceLine;
use App\Models\Invoice;
use App\Models\Issuer;
use App\Models\Payment;
use App\Models\Receiver;
use App\Models\TaxableItem;
use App\Models\TaxTotal;
use App\Models\User;
use App\Models\Value;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
		$item = new User();
		$item->name = "Mahmoud";
		$item->email = "MahmoudFayez@gmail.com";
		$item->password = Hash::make("12345678");
		$item->save();
		
		for ($i = 0; $i < 100; $i++) {
			$item2 = new Address();
			$item2->country = 'EG';
			$item2->governate = 'Tanta';
			$item2->regionCity = 'Tanta City Center';
			$item2->street = 'Big Stree ' . $i;
			$item2->buildingNumber = 'Building '.$i;
			$item2->save();
			$item = new Receiver();
			$item->type = mt_rand(1,100) > 150 ? "P" : "P";
			$item->name = "Customer " . $i;
			$item->receiver_id = '28604170103298';//mt_rand(1,100000) * 100+$i+5;
			//$item->address()->save($item2);
			$item2->receiver()->save($item);
		}/*
		for ($i = 0; $i < 1000; $i++) {
			$item = new Item();
			$item->internal_code = $i;
			$item->description = "item number " . $i;
			$item->gs1_code = "";
			$item->egs_code = 'EGS093'. ($i * mt_rand(1000, 9999));
			$item->unit_type = mt_rand(1,100) > 150 ? "KG" : "Litre";
			$item->unit_value = mt_rand(20, 100);
			$item->save();
		}*/
    }
}
