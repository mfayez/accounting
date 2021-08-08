<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\InvoiceTaxItem;
use App\Models\InvoiceItem;
use App\Models\User;

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
		
		for ($i = 0; $i < 1000; $i++) {
			$item = new Customer();
			$item->type = mt_rand(1,100) > 150 ? "B" : "I";
			$item->name = "Customer " . $i;
			$item->registration_number = $i+5;
			$item->address_country = 'Address '. $i;
			$item->address_governate = 'Tanta';
			$item->address_regionCity = 'Tanta City Center';
			$item->address_street = 'Big Stree ' . $i;
			$item->address_building_number = 'Building '.$i;
			$item->save();
		}
		for ($i = 0; $i < 1000; $i++) {
			$item = new Item();
			$item->internal_code = $i;
			$item->description = "item number " . $i;
			$item->gs1_code = "";
			$item->egs_code = 'EGS093'. ($i * mt_rand(1000, 9999));
			$item->unit_type = mt_rand(1,100) > 150 ? "KG" : "Litre";
			$item->unit_value = mt_rand(20, 100);
			$item->save();
		}
    }
}
