<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\Address;
use App\Models\Delivery;
use App\Models\Discount;
use App\Models\InvoiceLine;
use App\Models\Invoice;
use App\Models\Issuer;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Receiver;
use App\Models\TaxableItem;
use App\Models\TaxTotal;
use App\Models\TeamInvitation;
use App\Models\Team;
use App\Models\User;
use App\Models\Value;

class CustomerController extends Controller
{
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
		if (Auth::user()->isAdmin)
			return Receiver::with('address')->get();
		return Auth::user()->receivers()->with('address')->get()->toArray();
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
			'address.governate' 			=> ['required', 'string', Rule::in(['Cairo', 'Giza', 'Gharbia'])],
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
		Auth::user()->receivers()->attach($item->Id);

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
			'address.governate' 			=> ['required', 'string', Rule::in(['Cairo', 'Giza', 'Gharbia'])],
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
}
