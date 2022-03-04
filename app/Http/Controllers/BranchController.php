<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Value;
use App\Models\Issuer;
use App\Models\Address;
use App\Models\Invoice;
use App\Models\Payment;

use App\Models\Delivery;
use App\Models\Discount;
use App\Models\Receiver;
use App\Models\TaxTotal;
use App\Models\Membership;
use App\Models\InvoiceLine;
use App\Models\TaxableItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TeamInvitation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

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
            ->allowedSorts(['Id', 'name', 'issuer_id', 'type'])
            ->allowedFilters(['name', 'issuer_id', 'type', $globalSearch])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Branches/Index', [
            'branches' => $branches,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'name' => __('Name'),
                'issuer_id' => __('Tax Registration ID/National ID'),
            ])->addColumns([
                'Id' => __('ID'),
                'name' => __('Name'),
                'issuer_id' => __('Branch Identifier'),
				'type' => __('Branch Type')
            ]);
        });
    }

	public function index_json()
	{
		if (Auth::user()->isAdmin)
			return Issuer::with('address')->get();
		return Auth::user()->issuers()->with('address')->get()->toArray();
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
            'address.branchID' 				=> ['required', 'integer'],
			'address.country' 				=> ['required', 'string', Rule::in(['EG'])],
			'address.governate' 			=> ['required', 'string', Rule::in(['Cairo', 'Giza', 'Gharbia'])],
			'address.regionCity' 			=> ['required', 'string'],
			'address.street' 				=> ['required', 'string'],
			'address.buildingNumber' 		=> ['required', 'integer'],
			'address.postalCode' 			=> ['required', 'integer'],
			'address.additionalInformation' => ['nullable', 'string'],
            'branchLogo'                    => ['required' , 'image' , 'max:1000']
        ]);

        
        $item2 = new Address();
		$item2->branchID = $request->input('address.branchID');
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

        // store branch logo in the file system
        
        $logo = $request->file('branchLogo');

        $extension = $logo->getClientOriginalExtension();

        $name = $item->Id . '.' . $extension; 

        $logo->storeAs('public/uploads/branchesImages/' . $item->Id , $name);

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
		$data = $request->validate([
            'name' 							=> ['required', 'string', 'max:255'],
            'issuer_id' 					=> ['required', 'integer'],
            'type' 							=> ['required',  'string', Rule::in(['B', 'I'])],
            'address.branchID' 				=> ['required', 'integer'],
			'address.country' 				=> ['required', 'string', Rule::in(['EG'])],
			'address.governate' 			=> ['required', 'string', Rule::in(['Cairo', 'Giza', 'Gharbia'])],
			'address.regionCity' 			=> ['required', 'string'],
			'address.street' 				=> ['required', 'string'],
			'address.buildingNumber' 		=> ['required', 'integer'],
			'address.postalCode' 			=> ['required', 'integer'],
			'address.additionalInformation' => ['nullable', 'string'],
            'branchLogo'                    => ['nullable' , 'image' , 'max:1000']
        ]);

		$item = Issuer::findOrFail($id);
		$item->update($data);
		$item2 = $item->address;
		$item2->update($data['address']);

        if($request->has('branchLogo')) {

            $imageDir = Storage::allFiles('public/uploads/branchesImages/' . $item->Id);

            if(collect($imageDir)->count() > 0) {
                Storage::deleteDirectory('public/uploads/branchesImages/' . $item->Id);
            }

            // store branch logo in the file system
        
            $logo = $request->file('branchLogo');

            $extension = $logo->getClientOriginalExtension();

            $name = $item->Id . '.' . $extension; 

            $logo->storeAs('public/uploads/branchesImages/' . $item->Id , $name);

        }
        
		return $item;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $branch = Issuer::findOrFail($id);
		$address = $branch->address;

        $imageDir = Storage::allFiles('public/uploads/branchesImages/' . $branch->Id);

        if(collect($imageDir)->count() > 0) {
            Storage::deleteDirectory('public/uploads/branchesImages/' . $branch->Id);
        }

		$branch->delete(); 
		$address->delete();
    }

    public function getBranchesimages($branchesIds) {
        
        $ids = explode(',' , $branchesIds);

        $images = [];

        foreach($ids as $id) {
            
            if(count($imageDir = Storage::allFiles('public/uploads/branchesImages/' . $id)) > 0) {
                $images[$id] = Str::of($imageDir[0])->replaceFirst('public/' , '');
            } else {
                $images[$id] = "N/A";
            }
        }

        return $images;
    }
}
