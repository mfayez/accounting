<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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

class UserController extends Controller
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
                $query->where('name', 'LIKE', "%{$value}%")->orWhere('email', 'LIKE', "%{$value}%");
            });
        });

        $branches = QueryBuilder::for(User::class)
        	->defaultSort('name')
            ->allowedSorts(['Id', 'name', 'email'] )
            ->allowedFilters(['name', 'email', $globalSearch])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $branches,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'name' => __('Name'),
                'email' => __('email'),
            ])->addColumns([
                'Id' => __('ID'),
                'name' => __('Name'),
                'email' => __('email')
            ]);
        });
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
            'name' 		=> ['required', 'string', 'max:255'],
			'email' 	=> ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password'	=> ['required', 'min:8'],
        ]);
		
        $item2 = new User($data);
		$item2->password = Hash::make($item2->password);
		$item2->save();

        return $item2;
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
            'name' 		=> ['required', 'string', 'max:255'],
			'email' 	=> ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password'	=> ['required', 'min:8'],
        ]);

		$item = User::findOrFail($id);
		$item->update($data);
		$item->password = Hash::make($item->password);
		$item->save();
        
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
        $branch = User::findOrFail($id);
		$branch->delete(); 
    }
}
