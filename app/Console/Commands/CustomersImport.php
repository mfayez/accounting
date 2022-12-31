<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\General\Address;
use App\Models\ETA\Receiver;

use App\Http\Traits\ExcelWrapper;

class CustomersImport extends Command
{
    use ExcelWrapper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:import {excel_file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import customers from excel file';

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
        $excelFile = $this->argument('excel_file');
        if ($this->confirm('This will import all customers from excel file, are you sure?')) {
            $this->info('Importing customers from excel file: ' . $excelFile);
        }
        $data = $this->xlsxToArray($excelFile);
        $update_counter = 0;
        $insert_counter = 0;
        $total = count($data);
        foreach ($data as $row) {
            $receiver = Receiver::where('code', $row['code'])->first();
            if ($receiver) {
                $receiver->name = $row['name'];
                $receiver->receiver_id = $row['RIN'];
                //if length of receiver_id is 9 then this is a B type
                if (strlen($receiver->receiver_id) == 9) {
                    $receiver->type = 'B';
                } else {
                    $receiver->receiver_id = $row['NSN'];
                    $receiver->type = 'P';
                }
                $receiver->address->governate = $row['Governate'];
                $receiver->address->regionCity = $row['city'];
                $receiver->address->street = $row['street'];
                $receiver->address->buildingNumber = $row['building'];
                $receiver->address->postalCode = $row['postal'];
                $receiver->address->save();
                $receiver->save();
                $update_counter++;
            } else {
                $receiver = new Receiver();
                $address = new Address();
                $receiver->name = $row['name'];
                $receiver->code = $row['code'];
                $receiver->receiver_id = $row['RIN'];
                //if length of receiver_id is 9 then this is a B type
                if (strlen($receiver->receiver_id) == 9) {
                    $receiver->type = 'B';
                } else {
                    $receiver->receiver_id = $row['NSN'];
                    $receiver->type = 'P';
                }
                $address->governate = $row['Governate'];
                $address->regionCity = $row['city'];
                $address->street = $row['street'];
                $address->buildingNumber = $row['building'];
                $address->postalCode = $row['postal'];
                $address->save();
                $address->receiver()->save($receiver);
                $insert_counter++;
            }
        }
        $this->info('Total: ' . $total . ' Updated: ' . $update_counter . ' Inserted: ' . $insert_counter);
        return 0;
    }
}
