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
                $query->where('name', 'LIKE', "%{$value}%")->orWhere('receiver_id', 'LIKE', "%{$value}%");
            });
        });

        $customers = QueryBuilder::for(Receiver::class)
			->with('address')
        	->defaultSort('name')
            ->allowedSorts(['Id', 'name', 'receiver_id', 'type'])
            ->allowedFilters(['name', 'receiver_id', 'type', $globalSearch])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'name' => __('Name'),
                'receiver_id' => __('Tax Registration Number'),// ID/National ID',
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
		$data = $request->validate([
            'name' 			=> ['required', 'string', 'max:255'],
            'code' 			=> ['required', 'string', 'max:255'],
            'receiver_id' 	=> ['required', 'integer'],
            'type' 			=> ['required',  'string', Rule::in(['B', 'P'])],
        ]);

		$item2 = new Address();
		$item2->country = 'EG';
		$item2->governate = 'Cairo';
		$item2->regionCity = 'City';
		$item2->street = 'Street';
		$item2->buildingNumber = '0';
		$item2->save();
        $item = new Receiver($data);
        $item->save();
		$item2->receiver()->save($item);
        return $item;
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
		$customer = Receiver::findOrFail($id);
		$data = $request->validate([
            'name' 			=> ['required', 'string', 'max:255'],
            'code' 			=> ['required', 'string', 'max:255'],
            'receiver_id' 	=> ['required', 'integer'],
            'type' 			=> ['required',  'string', Rule::in(['B', 'P'])],
        ]);
		
		$customer->update($data);
		return $customer;
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
    }
}
