<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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

use App\Http\Traits\ExcelWrapper;

class CustomerController extends Controller
{
    use ExcelWrapper;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('name', 'LIKE', "%{$value}%")
					->orWhere('receiver_id', 'LIKE', "%{$value}%")
					->orWhere('code', 'LIKE', "%{$value}%");
            });
        });

        $customers = QueryBuilder::for(Receiver::class)
			->with('address')
        	->defaultSort('name')
            ->allowedSorts(['Id', 'name', 'receiver_id', 'type'])
            ->allowedFilters(['name', 'receiver_id', 'type', 'code', $globalSearch])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'name' => __('Name'),
                'receiver_id' => __('Tax Registration Number'),// ID/National ID',
				'code' => __('Internal Code'),
            ])->addColumns([
				'Id' => __('ID'),
                'name' => __('Name'),
                'code' => __('Internal Code'),
                'receiver_id' => __('Tax Registration Number'),
				'type' => __('Customer Type')
            ]);
        });
    }
	
	public function index_json()
	{
		// if (Auth::user()->isAdmin)
			return Receiver::with('address')->get();
		// return Auth::user()->receivers()->with('address')->get()->toArray();
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		//return Inertia::render('Items/Create');;        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$request->validate([
            'name' 							=> ['required', 'string', 'max:255'],
            'receiver_id' 					=> ['required', 'integer'],
            'type' 							=> ['required',  'string', Rule::in(['B', 'P'])],
            'code'				 			=> ['nullable', 'string', 'max:255'],
			'address.country' 				=> ['required', 'string', Rule::in(['EG'])],
			'address.governate' 			=> ['required', 'string', Rule::in(['Alexandria', 'Assiut', 'Aswan',
												'Beheira', 'Bani Suef', 'Cairo', 'Daqahliya', 'Damietta', 'Fayyoum', 
												'Gharbiya', 'Giza', 'Helwan', 'Ismailia', 'Kafr El Sheikh', 'Luxor', 
												'Marsa Matrouh', 'Minya', 'Monofiya', 'New Valley', 'North Sinai',
												'Port Said', 'Qalioubiya', 'Qena', 'Red Sea', 'Sharqiya', 'Sohag',
												'South Sinai', 'Suez', 'Tanta'])],
			'address.regionCity' 			=> ['required', 'string'],
			'address.street' 				=> ['required', 'string'],
			'address.buildingNumber' 		=> ['required', 'integer'],
			'address.postalCode' 			=> ['nullable', 'integer'],
			'address.additionalInformation' => ['nullable', 'string'],
        ]);

		
        $item2 = new Address();
		$item2->branchID = 0;
        $item2->country = $request->input('address.country');
        $item2->governate = $request->input('address.governate');
        $item2->regionCity = $request->input('address.regionCity');
        $item2->street = $request->input('address.street');
        $item2->buildingNumber = $request->input('address.buildingNumber');
		$item2->postalCode = $request->input('address.postalCode');
		$item2->additionalInformation = $request->input('address.additionalInformation');
        $item2->save();
        $item = new Receiver();
        $item->type = $request->input('type');
        $item->name = $request->input('name');
		$item->receiver_id = $request->input('receiver_id');
		$item->code = $request->input('code');
        $item2->receiver()->save($item);

        return \redirect()->route('customers.index')->with('success' , __('Record Created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$data = $request->validate([
            'name' 							=> ['required', 'string', 'max:255'],
            'receiver_id' 					=> ['required', 'integer'],
            'type' 							=> ['required',  'string', Rule::in(['B', 'P'])],
            'code'				 			=> ['nullable', 'string', 'max:255'],
			'address.country' 				=> ['required', 'string', Rule::in(['EG'])],
			'address.governate' 			=> ['required', 'string', Rule::in(['Alexandria', 'Assiut', 'Aswan',
												'Beheira', 'Bani Suef', 'Cairo', 'Daqahliya', 'Damietta', 'Fayyoum', 
												'Gharbiya', 'Giza', 'Helwan', 'Ismailia', 'Kafr El Sheikh', 'Luxor', 
												'Marsa Matrouh', 'Minya', 'Monofiya', 'New Valley', 'North Sinai',
												'Port Said', 'Qalioubiya', 'Qena', 'Red Sea', 'Sharqiya', 'Sohag',
												'South Sinai', 'Suez', 'Tanta'])],
			'address.regionCity' 			=> ['required', 'string'],
			'address.street' 				=> ['required', 'string'],
			'address.buildingNumber' 		=> ['required', 'integer'],
			'address.postalCode' 			=> ['nullable', 'integer'],
			'address.additionalInformation' => ['nullable', 'string'],
        ]);

		$item = Receiver::findOrFail($id);
		$item->update($data);
		$item2 = $item->address;
		$item2->update($data['address']);
        
		return $item;
		//return \redirect()->route('customers.index')->with('success' , __('Record Was Updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Receiver::findOrFail($id);
		$customer->delete(); 

        return $customer;
    }

    public function downloadExcel() {
        $customers = Receiver::all();
		
		//render excel file now
		$reader = IOFactory::createReader('Xlsx');
		$file = $reader->load('./ExcelTemplates/CustomerUpload.xlsx');
		$rowIdx = 3;
		foreach($customers as $row){
			$file->getActiveSheet()->setCellValue($this->index2(1, $rowIdx), $row->receiver_id);
            $file->getActiveSheet()->setCellValue($this->index2(2, $rowIdx), $row->name);
			$file->getActiveSheet()->setCellValue($this->index2(3, $rowIdx), $row->code);
			$file->getActiveSheet()->setCellValue($this->index2(4, $rowIdx), $row->address->country);
			$file->getActiveSheet()->setCellValue($this->index2(5, $rowIdx), $row->address->governate);
			$file->getActiveSheet()->setCellValue($this->index2(6, $rowIdx), $row->address->regionCity);
			$file->getActiveSheet()->setCellValue($this->index2(7, $rowIdx), $row->address->street);
			$file->getActiveSheet()->setCellValue($this->index2(8, $rowIdx), $row->address->buildingNumber);
            $file->getActiveSheet()->setCellValue($this->index2(9, $rowIdx), $row->address->postalCode);
            $file->getActiveSheet()->setCellValue($this->index2(10,$rowIdx), $row->address->floor);
            $file->getActiveSheet()->setCellValue($this->index2(11,$rowIdx), $row->address->room);
            $file->getActiveSheet()->setCellValue($this->index2(12,$rowIdx), $row->address->landmark);
            $file->getActiveSheet()->setCellValue($this->index2(13,$rowIdx), $row->address->additionalInformation);
            $file->getActiveSheet()->setCellValue($this->index2(14,$rowIdx), $row->Id);
            
			$rowIdx++;
		}
		$writer = IOFactory::createWriter($file, 'Xlsx');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="CustomersData.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
    }
}
