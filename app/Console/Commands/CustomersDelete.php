<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\General\Address;
use App\Models\ETA\ETAItem;
use App\Models\ETA\ETAInvoice;
use App\Models\ETA\Invoice;
use App\Models\ETA\InvoiceLine;
use App\Models\ETA\TaxableItem;
use App\Models\ETA\TaxTotal;
use App\Models\ETA\Value;
use App\Models\ETA\Discount;
use App\Models\ETA\Receiver;
use App\Models\ETA\Issuer;
use App\Models\General\Upload;
use App\Models\General\Settings;

class CustomersDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:delete {--all} {--code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete customers from database';

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
        if ($this->option('all')) {
            if ($this->confirm('This will delete all customers, and their invoices too, are you sure?')) {
                $this->info('Deleting all customers');
                TaxableItem::query()->delete();
                InvoiceLine::query()->delete();
                Invoice::query()->delete();
                TaxTotal::query()->delete();
                Value::query()->delete();
                Discount::query()->delete();
                ETAInvoice::query()->delete();
                Receiver::query()->delete();
            }
        } else {
            $code = $this->option('code');
            if ($code) {
                if ($this->confirm('This will delete customer with code: ' . $code . ', are you sure?')) {
                    $this->info('Deleting customer with code: ' . $code);
                    Receiver::where('code', $code)->delete();
                }
            } else {
                $this->error('Please specify customer code or use --all option');
            }
        }
        Address::doesntHave('receiver')->doesntHave('issuer')->delete();
        return 0;
    }
}