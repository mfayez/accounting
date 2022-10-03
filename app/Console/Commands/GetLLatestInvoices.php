<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;
use App\Http\Traits\ETAAuthenticator;

class GetLLatestInvoices extends Command
{
    use ETAAuthenticator;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:latest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get latest invoices from ETA';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/recent";
		$this->AuthenticateETA2();
		$response = Http::withToken($this->token)->get($url, [
			"PageSize" => "100",
			"PageNo" => 1
		]);
		//$collection = $response['result'];
        $this->info($response);
        return 0;
    }
}
