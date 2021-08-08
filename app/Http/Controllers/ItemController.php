<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\InvoiceTaxItem;
use App\Models\InvoiceItem;
use App\Models\User;

class ItemController extends Controller
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
                $query->where('description', 'LIKE', "%{$value}%")
					->orWhere('internal_code', 'LIKE', "%{$value}%")
					->orWhere('gs1_code', 'LIKE', "%{$value}%")
					->orWhere('egs_code', 'LIKE', "%{$value}%");
            });
        });

        $items = QueryBuilder::for(Item::class)
            ->defaultSort('id')
            ->allowedSorts(['id', 'description', 'internal_code', 'gs1_code', 'egs_code'])
            ->allowedFilters(['description', 'internal_code', 'gs1_code', 'egs_code', $globalSearch])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Items/Index', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'description' => 'Item Description',
                'internal_code' => 'Internal Code',
                'gs1_code' => 'Global Standard Code',
                'egs_code' => 'Egyptian Standard Code',
            ])->addColumns([
                'description' => 'Item Description',
				'internal_code' => 'Internal Code',
				'gs1_code' => 'Global Standard Code',
				'egs_code' => 'Egyptian Standard Code',
				'unit_type' => 'Unit Type',
				'unit_value' => 'Unit Price',
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
		return Inertia::render('Items/Create');;        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request);
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
