<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use ZipArchive;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

use App\Http\Traits\ETAAuthenticator;
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
use App\Models\Archive;

class ArchiveController extends Controller
{
    use ETAAuthenticator;

    public function index(Request $request)
    {
        $items = QueryBuilder::for(Archive::class)
        	->allowedSorts(['id'])
            ->with('issuer')
            ->with('receiver')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Archives/IndexEx', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->column([
				'id' => __('ID'),
                'issuer.name' => __('Issuer'),
                'receiver.name' => __('Receiver'),
                'start_date' => __('Start Date'),
                'end_date' => __('End Date'),
				'status' => __('Status')
            ]);
        });
    }

    public function downloadArchives($id)
    {
        $fileName = $id.'.zip';
        return response()->download(storage_path($fileName));
    }

    /**
     * Store a newly created resource in ETA.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver.Id' 	=> ['required', 'integer'],
            'issuer.Id' 	=> ['required', 'integer'],
            'start_date' 	=> ['required', 'date'],
            'end_date' 	    => ['required', 'date']
        ]);
        $branchId   = $request->input('issuer')['Id'];
		$customerId = $request->input('receiver')['Id'];
        //$startDate  = $request->input('startDate');
		//$endDate    = $request->input('endDate');

		$item = new Archive($data);
        if ($customerId > -1)
            $item->receiver_id = $customerId;
        if ($branchId > -1)
            $item->issuer_id = $branchId;
        $item->status = "Submitted";
        $item->save();
        
        return $item;
    }
    
}
