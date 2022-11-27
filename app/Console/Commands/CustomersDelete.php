<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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