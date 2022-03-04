<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportSummaryExport implements FromArray , WithStartRow , WithHeadings , WithEvents
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(app()->getLocale() == 'en' ? false : true);
            }
        ];
    }
}
