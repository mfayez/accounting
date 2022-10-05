<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Settings;
use App\Models\Issuer;

class TransformSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $firstBranch = Issuer::first();
        $myid = "";
        $myname = "";
        //check if $firstBraqnch is null
        if ($firstBranch){
            $myid = $firstBranch->issuer_id;
            $myname = $firstBranch->name;
        }
        
        
        Settings::updateOrCreate([
                "type" => "Company Settings",
                "name" => "company_name"],
            [
                "value" => $myname
        ]);
        
        Settings::updateOrCreate([
                "type" => "Company Settings",
                "name" => "tax_number"],
            [
                "value" => $myid
        ]);
        Settings::updateOrCreate([
                "type" => "ETA Settings",
                "name" => "client_id"],
            [
                "value" => env("CLIENT_ID", "")
        ]);
        Settings::updateOrCreate([
                "type" => "ETA Settings",
                "name" => "client_secret1"],
            [
                "value" => env("CLIENT_SECRET", "")
        ]);
        Settings::updateOrCreate([
                "type" => "ETA Settings",
                "name" => "client_secret2"],
            [
                "value" => env("CLIENT_SECRET", "")
        ]);
        Settings::updateOrCreate([
                "type" => "ETA Settings",
                "name" => "isProduction"],
            [
                "value" => env("ETA_URL", "") == "https://api.invoicing.eta.gov.eg/api/v1.0" ? "true" : "false"
        ]);
        Settings::updateOrCreate([
                "type" => "ETA Settings",
                "name" => "login_url"],
            [
                "value" => env("LOGIN_URL", "")
        ]);
        Settings::updateOrCreate([
                "type" => "ETA Settings",
                "name" => "eta_url"],
            [
             "value" => env("ETA_URL", "")
        ]);
        Settings::updateOrCreate([
                "type" => "ETA Settings",
                "name" => "preview_url"],
            [
                "value" => env("PREVIEW_URL", "")
        ]);
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
