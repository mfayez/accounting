<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

trait ExcelWrapper {
    private function index($col, $row)
	{
		$col1 = Coordinate::stringFromColumnIndex($col);
		return $col1.$row;
	}

	private function xlsxToArray($filename = '', $extension = 'xlsx')
	{
		$extension = $extension == 'xlsx' ? 'Xlsx' : 'Xls';
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
		$reader->setReadDataOnly(TRUE);
		$spreadsheet = $reader->load($filename);

		$worksheet = $spreadsheet->getActiveSheet();
    	$header_en = null;
	    $header_ar = null;
    	$data = array();
	    
		$rows = $worksheet->toArray();

		foreach($rows as $key => $row) {
			if (!$header_en){
				$header_en = array_map('trim', $row); //trim trilling spaces
			}
			else if (!$header_ar)
				$header_ar = $row;
			else
				$data[] = array_combine($header_en, $row);
		};
		
    	return $data;
	}

	private function csvToArray($filename = '', $delimiter = ',')
	{
    	if (!file_exists($filename) || !is_readable($filename))
        	return false;

	    $header_en = null;
	    $header_ar = null;
    	$data = array();
	    if (($handle = fopen($filename, 'r')) !== false)
    	{
        	while (($row = fgetcsv($handle, 10000, $delimiter)) !== false)
	        {
    	        if (!$header_en){
					if (count($row) == 1){
						$delimiter = ';';
						$row = str_getcsv($row[0], $delimiter);
					}
					foreach($row as $key=>$item){
						$row[$key] = trim(iconv('UTF-8', 'ASCII//IGNORE', $item));
					}
					$header_en = array_map('trim', $row); //trim trilling spaces
				}
    	        else if (!$header_ar)
        	        $header_ar = $row;
            	else
                	$data[] = array_combine($header_en, $row);
        	}
	        fclose($handle);
    	}

    	return $data;
	}
}
