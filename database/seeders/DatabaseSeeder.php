<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\ETA\Address;
use App\Models\ETA\Delivery;
use App\Models\ETA\Discount;
use App\Models\ETA\InvoiceLine;
use App\Models\ETA\Invoice;
use App\Models\ETA\Issuer;
use App\Models\General\Payment;
use App\Models\ETA\Receiver;
use App\Models\ETA\TaxableItem;
use App\Models\ETA\TaxTotal;
use App\Models\User;
use App\Models\Team;
use App\Models\ETA\Value;
use App\Models\General\Settings;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		$item = new Team(["name" => "Administrtor",   "personal_team" => 0, "user_id" => 1]);
		$item->save();
		$item = new Team(["name" => "Reviewer",   "personal_team" => 0, "user_id" => 1]);
		$item->save();
		$item = new Team(["name" => "Data Entry", "personal_team" => 0, "user_id" => 1]);
		$item->save();
		$item = new Team(["name" => "ETA",   	  "personal_team" => 0, "user_id" => 1]);
		$item->save();
		$item = new Team(["name" => "Viewer",     "personal_team" => 0, "user_id" => 1]);
		$item->save();

		$item1 = new User();
		$item1->name = "Administrator";
		$item1->email = "admin@invoicemaster.com";
		$item1->password = Hash::make("123456789");
		$item1->current_team_id = 1;
		$item1->save();

		$item = new Settings(["name" => "footer","type" =>"invoice settings", "value" =>""]);
		$item->save();
		$item = new Settings(["name" => "showQR","type" =>"invoice settings", "value" =>"0"]);
		$item->save();
    }
}
