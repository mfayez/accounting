<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ETAController;

class SetItemsActiveDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:items {from_date} {to_date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send new active date to all items in ETA website';

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
		$activeFrom = $this->argument('from_date'); 
		$activeTo = $this->argument('to_date'); 
		if ($this->confirm('This will update all items in ETA to new active date, are you sure?')) {
			$eta = new ETAController();
			$eta->SetItemsActiveDate($activeFrom, $activeTo);
		}
        return 0;
    }
}
