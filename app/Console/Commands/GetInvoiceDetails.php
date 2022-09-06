<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;
use App\Http\Traits\ETAAuthenticator;

class GetInvoiceDetails extends Command
{
    use ETAAuthenticator;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:details {uuid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get details of invoice using uuid';

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
        $uuid = $this->argument('uuid');
        $urlbase = env("ETA_URL")."/documents/%s/raw";
        $url = sprintf($urlbase, $uuid);
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
