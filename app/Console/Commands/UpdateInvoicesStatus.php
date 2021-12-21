<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ETAController;

class UpdateInvoicesStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve updates regarding invoices from ETA';

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
		$eta = new ETAController();
		$eta->LoadMissingInvoices();
		$eta->UpdateInvoices();
        return 0;
    }
}
