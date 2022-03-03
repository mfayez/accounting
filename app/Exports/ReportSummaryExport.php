<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ReportSummaryExport implements FromArray , WithStartRow , WithHeadings
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        // foreach($this->data as $row) {
        //     return [
        //         __('Invoice Number') => $row->Id,
        //         __('Month') => $row->Month,
        //         __('Date') => $row->Date,
        //         __('Tax Total') => $row->TaxTotal,
        //         __('Client Name') => $row->Client,
        //         __('Total Amount') => $row->Total
        //     ];
        // }  

        return $this->data;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function headings(): array
    {
        return [
            __('Invoice Number'),
            __('Month'),
            __('Date'),
            __('Tax Total'),
            __('Client Name'),
            __('Total Amount')
        ];
    }
}
