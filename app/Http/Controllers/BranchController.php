<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

class BranchController extends Controller
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
                $query->where('name', 'LIKE', "%{$value}%")->orWhere('issuer_id', 'LIKE', "%{$value}%");
            });
        });

        $branches = QueryBuilder::for(Issuer::class)
			->with('address')
        	->defaultSort('name')
            ->allowedSorts(['name', 'issuer_id', 'type'])
            ->allowedFilters(['name', 'issuer_id', 'type', $globalSearch])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Branches/Index', [
            'branches' => $branches,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'name' => 'Name',
                'issuer_id' => 'Tax Registration ID/National ID',
            ])->addColumns([
                'name' => 'Name',
                'issuer_id' => 'Branch Identifier',
				'type' => 'Branch Type'
            ]);
        });
    }

	public function index_json()
	{
		return Issuer::with('address')->get()->toArray();
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
            'issuer_id' 					=> ['required', 'integer'],
            'type' 							=> ['required',  'string', Rule::in(['B', 'I'])],
            'address.branchId' 				=> ['required', 'integer'],
			'address.country' 				=> ['required', 'string', Rule::in(['EG'])],
			'address.governate' 			=> ['required', 'string', Rule::in(['Cairo', 'Giza'])],
			'address.regionCity' 			=> ['required', 'string'],
			'address.street' 				=> ['required', 'string'],
			'address.buildingNumber' 		=> ['required', 'integer'],
			'address.postalCode' 			=> ['required', 'integer'],
			'address.additionalInformation' => ['nullable', 'string'],
        ]);

		
        $item2 = new Address();
		$item2->branchId = $request->input('address.branchId');
        $item2->country = $request->input('address.country');
        $item2->governate = $request->input('address.governate');
        $item2->regionCity = $request->input('address.regionCity');
        $item2->street = $request->input('address.street');
        $item2->buildingNumber = $request->input('address.buildingNumber');
		$item2->postalCode = $request->input('address.postalCode');
		$item2->additionalInformation = $request->input('address.additionalInformation');
        $item2->save();
        $item = new Issuer();
        $item->type = $request->input('type');
        $item->name = $request->input('name');
		$item->issuer_id = $request->input('issuer_id');
        $item2->issuer()->save($item);

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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
