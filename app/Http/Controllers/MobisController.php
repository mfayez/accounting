<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

use CasperBiering\Dotnet\BinaryXml\Decoder;
use CasperBiering\Dotnet\BinaryXml\SoapDecoder;
use CasperBiering\Dotnet\BinaryXml\Encoder;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
use App\Models\MSItemMap;
use App\Models\MSBranchMap;

use App\Http\Traits\MobisAuthenticator;
use App\Http\Traits\ExcelWrapper;
use App\Http\Traits\XMLWrapper;

class MobisController extends Controller
{
    use MobisAuthenticator;
	use ExcelWrapper;
	use XMLWrapper;
    
	public function uploadInvoices(Request $request)
	{
		$data = $request->validate([
			'issuer'				=> 'required',
			'taxpayerActivityCode'	=> 'required',
			'file1'					=> 'required|file',
			'file2'					=> 'required|file',
		]);

		$issuer = $data['issuer'];
		$activity = $data['taxpayerActivityCode'];
		$temp1 = [];//summary
		$temp2 = [];//details
		$extension1 = $data['file1']->extension();
		$extension2 = $data['file2']->extension();
		$date1 = Carbon::now();
		$lstOfInvoices = [];
		$lastInv = null;
		
		if ($extension1 == 'xls' && $extension2 == 'xls')
		{
			$temp1 = $this->xlsxToArrayEx($request->file1, $extension1, 7, false);
			$temp2 = $this->xlsxToArrayEx($request->file2, $extension2, 7, false);
		}
		else
		{
			return json_encode(["Error" => true, "Message" => __("Unsupported File Type!")]);
		}

		foreach($temp1 as $invoice) {
			if (!isset($invoice['Invoice ID']))
				continue;
			$invoice_lines = array_filter($temp2, function($item) use ($invoice) {
				return $item['Invoice ID'] == $invoice['Invoice ID'];
			});
			$invoice2 = Invoice::firstWhere(['internalID' => $invoice['Invoice No']]);
			if ($invoice2 && $invoice2->status == 'In Review')
			{
				foreach($invoice2->invoiceLines as $line) {
					$line->discount()->delete();
					$line->taxableItems()->delete();
					$line->delete();
					$line->unitValue()->delete();
				}
				$invoice2->taxTotals()->delete();
				$invoice2->delete();
				$invoice2 = null;
			}
			if (!$invoice2)
			{
				$invoice2 = $this->AddMissingInvoice($invoice, $invoice_lines, $issuer, $activity);
				array_push($lstOfInvoices, $invoice2->internalID);
			}
			$lastInv = $invoice["Invoice No"];
			if ($invoice2){
				if ($invoice2->dateTimeIssued < $date1)
					$date1 = $invoice2->dateTimeIssued;
				//process discount
				$totalDiscount = abs($invoice["Display Discount"]);
				if ($totalDiscount > 0){
					$invoice2->extraDiscountAmount = $totalDiscount;
					$invoice2->totalAmount = $invoice2->totalAmount - $totalDiscount;
					$invoice2->save();
				}
			}
		}

		return ["message" => 'success', 'lastDate' => $date1];	
	}

	private function AddMissingInvoice($ms_invoice, $ms_invoice_lines, $issuer, $activity)
	{
		$invoice = new Invoice();
		$receiver = Receiver::firstWhere(['code' => $ms_invoice['Customer No']]);
		
		if (!$receiver) {
			$item2 = new Address();
			$item2->country = "EG";
			$item2->governate = "Cairo";
			$item2->regionCity = "Cairo";
			$item2->street = "stree 1";
			$item2->buildingNumber = "1";
			$item2->postalCode = "12345";
			$item2->save();
			$item = new Receiver();
			$item->code = $ms_invoice['Customer No'];
			$item->name = $ms_invoice['Title'];
			$item->receiver_id = "0";
			$item->type = "P";
			$item2->receiver()->save($item);
			$receiver = $item;
		}
		
		$invoice->issuer_id = $issuer;
		$invoice->receiver_id = $receiver->Id;
		$invoice->statusreason = "تحميل الفاتورة من MOBIS";
		//TODO document type may be 'C' if document kind is 'مردودات'
		$invoice->documentType = "I";
		$invoice->documentTypeVersion = SETTINGS_VAL('application settings', 'invoiceVersion', '1.0');;
		$invoice->totalDiscountAmount = 0;
		$invoice->totalSalesAmount = 0;
		$invoice->netAmount = 0;
		$invoice->totalAmount = 0;
		$invoice->extraDiscountAmount = 0;
		$invoice->totalItemsDiscountAmount = 0;
		$invoice->status = "In Review";	
		$invoice->internalID = $ms_invoice['Invoice No'];
		$keys = array_keys($ms_invoice_lines);
		$invoice->dateTimeIssued = $this->excelDateToDatetime($ms_invoice_lines[$keys[0]]['Inv. Date']);
		$invoice->taxpayerActivityCode = $activity;
		$invoice->save();

		foreach($ms_invoice_lines as $line) {
			if (!isset($line['SKU No']))
				continue;
			$mapItem = MSItemMap::find($line['SKU No']);
			if (!$mapItem) {
				$mapItem = new MSItemMap();
				$mapItem->MSCode = $line['SKU No'];
				$mapItem->ItemNameA = $line['SKU'];
				$mapItem->ItemNameE = $line['SKU'];
				$mapItem->save();
				continue;
			}
			//if there is not map for this item ignore it
			if (is_null($mapItem->ETACode))
				continue;
			if ($line['Quantity'] == 0)
				continue;

			$unitValue = new Value(['currencySold' => "EGP", 
				'amountEGP' => $line['Unit Price']
			]);
			$unitValue->save();
			
			$invoiceline = new InvoiceLine((array)$line);
			$invoiceline->description = $line['SKU'];
			$invoiceline->itemType = "GS1";
			$invoiceline->itemCode = $mapItem->ETACode;
			if ($line["Unit"] == "BO")
				$invoiceline->unitType = "BOX";
			else if ($line["Unit"] == "CA")
				$invoiceline->unitType = "CT";
			else if ($line["Unit"] == "PI")
				$invoiceline->unitType = "EA";
			else
				$invoiceline->unitType = "EA";
			$invoiceline->quantity = $line['Quantity'];
			$invoiceline->internalCode = $line['SKU No'];
			$invoiceline->salesTotal = $unitValue->amountEGP * $invoiceline->quantity;
			$invoiceline->netTotal = $unitValue->amountEGP * $invoiceline->quantity;
			$invoiceline->itemsDiscount = $line["Total Discount"];
			$invoiceline->total = $invoiceline->netTotal - $invoiceline->itemsDiscount;	//done
			if ($invoiceline->total < 0)
			{
				$invoiceline->total = 0;
				$invoiceline->itemsDiscount = $invoiceline->netTotal;
			}
			$invoiceline->valueDifference = 0;
			$invoiceline->totalTaxableFees = 0;
			$invoiceline->invoice_id = $invoice->Id;
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->save();
		}

		$invoice->normalize();
		$invoice->updateTaxTotals();
		$invoice->save();
		return $invoice;
	}

	public function UploadItemsMap(Request $request)
	{
		$temp = [];
		$extension = $request->file->extension();
		if ($extension == 'xlsx' || $extension == 'xls')
			$temp = $this->xlsxToArray($request->file, $extension);
		else if ($extension == 'csv')
			$temp = $this->csvToArray($request->file);
		else
			return json_encode(["Error" => true, "Message" => __("Unsupported File Type!")]);

		foreach	($temp as $map)
		{
			MSItemMap::updateOrCreate(
				['MSCode' => $map['MOBIS Code']],
				['MSCode' => $map['MOBIS Code'],
				 'ETACode' => $map['ETACode'], 
				 'ItemNameA' => $map['ItemNameA'],
				 'ItemNameE' => $map['ItemNameE'],
				]
			);
			
		}
	}

	public function indexItemsMap(Request $request)
	{
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('MSCode', 'LIKE', "%{$value}%")
					->orWhere('ETACode', 'LIKE', "%{$value}%")
					->orWhere('ItemNameA', 'LIKE', "%{$value}%")
					->orWhere('ItemNameE', 'LIKE', "%{$value}%");
            });
        });

        $items = QueryBuilder::for(MSItemMap::class)
			->defaultSort('MSCode')
            ->allowedSorts(['MSCode', 'ETACode', 'ItemNameA', 'ItemNameE'])
            ->allowedFilters(['MSCode', 'ETACode', 'ItemNameA', 'ItemNameE', $globalSearch])
		//	->with('itemTax')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Mobis/IndexItemsMap', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addColumns([
                'MSCode' 	=> __('MOBIS Code'),
                'ETACode' 	=> __('ETA Code'),
                'ItemNameA' => __('Arabic Name'),
				'ItemNameE' => __('English Name')
            ]);
        });
	}

	public function updateItem(Request $request)
	{
		$map = $request->validate([
			'MSCode' => 'required',
			'ETACode' => 'required',
			'ItemNameA' => 'required',
			'ItemNameE' => 'required',
		]);
		MSItemMap::updateOrCreate(
			['MSCode' => $map['MSCode']],
			['MSCode' => $map['MSCode'],
			 'ETACode' => $map['ETACode'], 
			 'ItemNameA' => $map['ItemNameA'],
			 'ItemNameE' => $map['ItemNameE'],
			]
		);
	}

	public function deleteItem(Request $request)
	{
		$map = $request->validate([
			'MSCode' => 'required'
		]);
		MSItemMap::findOrFail($map['MSCode'])->delete();
	}

	//Not Used
	public function indexBranchesMap(Request $request)
	{
		$data = DB::select("SELECT t1.Id as BID, t1.Name as BName, t2.sb_url as SBUrl, t2.sb_bu as SBBU
			from Issuer t1 left outer join
				sb_branches_map t2 on t1.Id = t2.branch_id"
		);

		return Inertia::render('Mobis/IndexBranchesMap', [
            'items' => $data,
        ]);
    }

	//Not Used
	public function updateBranchesMap(Request $request){
		$data = $request->validate([
			'BID' => 'required',
			'SBUrl' => 'required',
			'SBBU' => 'required',
		]);

		$branch_map = MSBranchMap::updateOrCreate(
			['branch_id' => $data['BID']],
			['branch_id' => $data['BID'],
			 'sb_url' => $data['SBUrl'],
			 'sb_bu' => $data['SBBU']
			]
		);
	}

	//Not Used
	public function indexBranchesMap_json()
	{
		return MSBranchMap::all();
	}

	public function DownloadItemsMap()
	{
		$customers = MSItemMap::all();
		
		//render excel file now
		$reader = IOFactory::createReader('Xlsx');
		$file = $reader->load('./ExcelTemplates/ItemsMapMobis.xlsx');
		
		$rowIdx = 3;
		foreach($customers as $row){
			$file->getActiveSheet()->setCellValue($this->index2(1, $rowIdx), $row->MSCode);
            $file->getActiveSheet()->setCellValue($this->index2(2, $rowIdx), $row->ItemNameA);
			$file->getActiveSheet()->setCellValue($this->index2(3, $rowIdx), $row->ItemNameE);
			$file->getActiveSheet()->setCellValue($this->index2(4, $rowIdx), $row->ETACode);

			$rowIdx++;
		}
		$writer = IOFactory::createWriter($file, 'Xlsx');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Map.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}


}


