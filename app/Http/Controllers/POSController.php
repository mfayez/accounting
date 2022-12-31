<?php

namespace App\Http\Controllers;

use App\Models\ETA\POS;

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\General\TeamInvitation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class POSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $POSes = QueryBuilder::for(POS::class)
            ->with("issuer")
			->defaultSort('name')
            ->allowedSorts(['id', 'name', 'serial'])
            ->allowedFilters(['name', 'serial'])
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('POS/Index', [
            'poses' => $POSes,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'name' => __('Name'),
                'serial' => __('Serial'),
            ])->addColumns([
                'id' => __('ID'),
                'name' => __('Name'),
                'serial' => __('Serial'),
                'grant_type' => __('Grant Type'),
                'issuer.name' => __('Branch'),
            ]);
        });
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
            'name'          => ['required', 'string', 'max:255'],
            'serial'		=> ['required', 'string'],
            'os_version'	=> ['required', 'string'],
            'model' 		=> ['required', 'string'],
			'grant_type' 	=> ['required', 'string', Rule::in(['client_credentials'])],
			'activity_code' => ['required', 'string'],
            'client_id' 	=> ['required', 'string'],
			'client_secret' => ['required', 'string'],
            'pos_key'	    => ['string'],
            'issuer_id'     => ['required']
        ]);
        $item = new POS($data);
        $item->save();

        return $item;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\POS  $pOS
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, POS $pOS)
    {
        $data = $request->validate([
            'id'            => ['required', 'integer'],
            'name'          => ['required', 'string', 'max:255'],
            'serial'		=> ['required', 'string'],
            'os_version'	=> ['required', 'string'],
            'model' 		=> ['required', 'string'],
			'grant_type' 	=> ['required', 'string', Rule::in(['client_credentials'])],
            'activity_code' => ['required', 'string'],
			'client_id' 	=> ['required', 'string'],
			'client_secret' => ['required', 'string'],
            'pos_key'	    => ['string'],
            'issuer_id'     => ['required']
        ]);
        $pos = POS::findOrFail($data['id']);
        $pos->update($data);
        return $pos;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\POS  $pOS
     * @return \Illuminate\Http\Response
     */
    public function destroy(POS $po)
    {
        $po->delete();
    }
}
