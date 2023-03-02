<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use CasperBiering\Dotnet\BinaryXml\Decoder;
use CasperBiering\Dotnet\BinaryXml\SoapDecoder;
use CasperBiering\Dotnet\BinaryXml\Encoder;

use App\Models\Address;
use App\Models\ETAItem;
use App\Models\ETAInvoice;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\TaxableItem;
use App\Models\TaxTotal;
use App\Models\Value;
use App\Models\Discount;
use App\Models\Receiver;
use App\Models\Issuer;
use App\Models\Upload;
use App\Models\Settings;
use App\Models\SBItemMap;
use App\Models\SBBranchMap;
use App\Models\SBItemTax;

use App\Http\Traits\SalesBuzzAuthenticator;
use App\Http\Traits\XMLWrapper;

class SBShowSettelments extends Command
{
    use SalesBuzzAuthenticator;
    use XMLWrapper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SB:ShowSettelments {--invoice_id=} {--username=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get settelments for an Invoice from Sales Buzz';

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
        $invoice_id = $this->option('invoice_id');
        $username = $this->option('username');
        $password = $this->option('password');

        $this->info("Invoice SB ID: $invoice_id");
        $inv = Invoice::where("internalID", $invoice_id)->first();
        $this->info("Invoice ID: $inv->Id");
        $branchMap = SBBranchMap::where('branch_id', $inv->issuer_id)->first();
		if (!isset($branchMap)){
            $this->info("No SalesBuzz Branch Map Found for Branch: $inv->issuer->name");
            return;
		}
		$url_base = $branchMap->sb_url;
		$buid = $branchMap->sb_bu;
		
		$this->AuthenticateSB(null, $username, $password, $buid, $url_base);
		if ($this->salezbuzz_cookies == ""){
            $this->info("SB Authentication Failed");
			return;
		}
        $url = "$url_base/ClientBin/BI-SalesBuzz-BackOffice-Web-Services-PromotionHeaderDS.svc/binary/GetSettledTransactions?TransRefID=$invoice_id&TransType=85&\$take=100";
		$decoder = new Decoder();
		$response = Http::withHeaders($this->salezbuzz_headers)
		    ->get($url);
		$xmldata11 = $decoder->decode($response->body());
        $xmldata = preg_replace('~(</?|\s)([a-z0-9_]+):~is', '$1', $xmldata11);
		$simpleXml = simplexml_load_string($xmldata);
		$sb_data2 = $this->xmlToArray($simpleXml);
        $data = json_encode($sb_data2);
        $this->info("SB Response: $data");
    }
}
