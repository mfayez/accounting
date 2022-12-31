<?php

namespace App\Console\Commands;

use File;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\General\Address;
use App\Models\ETA\Delivery;
use App\Models\ETA\Discount;
use App\Models\ETA\InvoiceLine;
use App\Models\ETA\Invoice;
use App\Models\ETA\Issuer;
use App\Models\Membership;
use App\Models\General\Payment;
use App\Models\ETA\Receiver;
use App\Models\ETA\TaxableItem;
use App\Models\ETA\TaxTotal;
use App\Models\General\TeamInvitation;
use App\Models\Team;
use App\Models\User;
use App\Models\ETA\Value;
use App\Models\General\Archive;

use App\Http\Traits\ETAAuthenticator;

class PrepareArchives extends Command
{
    use ETAAuthenticator;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archives:execute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute users archive requests found in the archives table';

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
        $items = Archive::where("status", "=", "Submitted")->get();
        foreach($items as $item) {
            $this->info($item);
            $this->processRequest($item);
            $item->status = "Ready";
            if($item->issuer_id == -1)
                $item->issuer_id = null;
            if($item->receiver_id == -1)
                $item->receiver_id = null;
            $item->save();
        }
        return 0;
    }

    protected function processRequest($item)
    {
        if(is_null($item->issuer_id))
            $item->issuer_id = -1;
        if(is_null($item->receiver_id))
            $item->receiver_id = -1;

        $strSqlStmt1 = "select t1.uuid as uuid
                        from Invoice t1
                        where (t1.issuer_id = ? or ? = -1)
                            and (t1.receiver_id = ? or ? = -1)
                            and t1.dateTimeIssued between ? and DATE_ADD(?, INTERVAL 1 DAY) and t1.status = 'Valid'";
        
        //DB::enableQueryLog();
        $data = DB::select($strSqlStmt1, [$item->issuer_id, $item->issuer_id, $item->receiver_id, $item->receiver_id, $item->start_date, $item->end_date]);
        //dd(DB::getQueryLog());
        $zip = new \ZipArchive();
        $fileName = $item->id.'.zip';
        $this->info(storage_path($fileName));
        if ($zip->open(storage_path($fileName), \ZipArchive::CREATE)== TRUE)
        {
            foreach($data as $inv) {
                $url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/".$inv->uuid."/pdf";
                $this->AuthenticateETA2();
                $file_data = Http::withToken($this->token)->get($url)->body();
                
                $zip->addFromString($inv->uuid.'.pdf', $file_data);
            }
            $zip->close();
        }
    }
}
