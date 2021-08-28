<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ETAItem;

class ETAController extends Controller
{
	protected $token = '';
	protected $token_expires_at = null;

	public function SyncItems(Request $request)
	{
		$url = "https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/my";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"Ps" => "10",
			"Pn" => $request->input("value")
		]);
		//$collection = ETAItem::hydrate($response['result']);
		$collection = $response['result'];
		//$collection->transform(function ($item, $key) {
    	//$collection->each(function ($item) {
		foreach($collection as $item) {
			$item2 = new ETAItem($item);
			$item2->ownerTaxpayerrin = $item['ownerTaxpayer']['rin'];
            $item2->ownerTaxpayername = $item['ownerTaxpayer']['name'];
            $item2->ownerTaxpayernameAr = $item['ownerTaxpayer']['nameAr'];
            $item2->requesterTaxpayerrin = $item['requesterTaxpayer']['rin'];
            $item2->requesterTaxpayername = $item['requesterTaxpayer']['name'];
            $item2->requesterTaxpayernameAr = $item['requesterTaxpayer']['nameAr'];
            $item2->codeCategorizationlevel1id = $item['codeCategorization']['level1']['id'];
            $item2->codeCategorizationlevel1name = $item['codeCategorization']['level1']['name'];
            $item2->codeCategorizationlevel1nameAr = $item['codeCategorization']['level1']['nameAr'];
            $item2->codeCategorizationlevel2id = $item['codeCategorization']['level2']['id'];
            $item2->codeCategorizationlevel2name = $item['codeCategorization']['level2']['name'];
            $item2->codeCategorizationlevel2nameAr = $item['codeCategorization']['level2']['nameAr'];
            $item2->codeCategorizationlevel3id = $item['codeCategorization']['level3']['id'];
            $item2->codeCategorizationlevel3name = $item['codeCategorization']['level3']['name'];
            $item2->codeCategorizationlevel3nameAr = $item['codeCategorization']['level3']['nameAr'];
            $item2->codeCategorizationlevel4id = $item['codeCategorization']['level4']['id'];
            $item2->codeCategorizationlevel4name = $item['codeCategorization']['level4']['name'];
            $item2->codeCategorizationlevel4nameAr = $item['codeCategorization']['level4']['nameAr'];
			$item2->save();
		};
		//$collection = $collection->flatten();
		//foreach($response['result'] as $item) {
		//	$dbItem = new ETAItem
		//}
		//dd($collection);
		//DB::transaction (function () use ($collection) {
    	//	$collection->each(function ($item) {
		//		$temp = new ETAItem($item);
		//		$temp->save();
    	//	});
		//});
		return $response['metadata'];
	}

	public function AddItem(Request $request)
	{
		$url = "https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/codes";
		$request->validate([
			'codeType'		=> ['required', 'string', Rule::in(['EGS', 'GS1'])],
			'parentCode'	=> ['required', 'integer'],
			'itemCode'		=> ['required', 'regex:EGS-[0-9]+-[0-9]+'],
			'codeName'		=> ['required', 'string', 'max:255'],
			'codeNameAr'	=> ['required', 'string', 'max:255'],
			'activeFrom'	=> ['required', 'date'],
			'activeTo'		=> ['required', 'date'],
			'description'	=> ['required', 'string', 'max:255'],
			'descriptionAr'	=> ['required', 'string', 'max:255'],
			'requestReason'	=> ['required', 'string', 'max:255']
        ]);
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->post($url, ["items" => array($request->validated())]);
		dd($response);
		return $response;
	}

	private function AuthenticateETA(Request $request)
	{
		$this->token = $request->session()->get('eta_token', null);
		$this->token_expires_at = $request->session()->get('eta_token_expires_at', null);
		if ($this->token == null || $this->token_expires_at == null || $this->token_expires_at < Carbon::now()) {
			$url = "https://id.preprod.eta.gov.eg/connect/token";
			$response = Http::asForm()->post($url, [
				"grant_type" => "client_credentials",
				"scope" => "InvoicingAPI",
				"client_id" => env("CLIENT_ID"),
				"client_secret" => env("CLIENT_SECRET") 
			]);
			$this->token = $response['access_token'];
			$this->token_expires_at = Carbon::now()->addSeconds($response['expires_in']-10);
			$request->session()->put('eta_token', $this->token);
			$request->session()->put('eta_token_expires_at', $this->token_expires_at);
			$request->session()->flash('status', 'Task was successful!');
		}
		else {
		}
	}

    public function indexItems()
    {
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('itemCode', 'LIKE', "%{$value}%")
                    ->orWhere('codeNamePrimaryLang', 'LIKE', "%{$value}%")
                    ->orWhere('codeNameSecondaryLang', 'LIKE', "%{$value}%")
                    ->orWhere('descriptionPrimaryLang', 'LIKE', "%{$value}%")
					->orWhere('descriptionSecondaryLang', 'LIKE', "%{$value}%")
					->orWhere('parentCodeID', '=', "{$value}")
					->orWhere('parentItemCode', '=', "{$value}")
					->orWhere('parentLevelName', 'LIKE', "%{$value}%")
					->orWhere('levelName', 'LIKE', "%{$value}%")
					->orWhere('requestCreationDateTimeUtc', '>', "{$value}")
					->orWhere('codeCreationDateTimeUtc', '>', "{$value}")
					->orWhere('activeFrom', '>', "{$value}")
					->orWhere('activeTo', '>', "{$value}")
					->orWhere('active', '=', "{$value}")
					->orWhere('status', '=', "{$value}")
					->orWhere('statusReason', 'LIKE', "%{$value}%");
					
            });
        });

		$items = QueryBuilder::for(ETAItem::class)
            ->defaultSort('id')
            ->allowedSorts(['codeTypeName', 'codeID', 'itemCode', 'codeNamePrimaryLang', 'codeNameSecondaryLang', 'descriptionPrimaryLang',     
							  'descriptionSecondaryLang', 'parentCodeID', 'parentItemCode', 'parentCodeNamePrimaryLang', 'parentCodeNameSecondaryLang',
							  'parentLevelName', 'levelName', 'requestCreationDateTimeUtc', 'codeCreationDateTimeUtc', 'activeFrom',
							  'activeTo', 'active', 'status', 'statusReason'])
            ->allowedFilters(['global', 'codeTypeName', 'codeID', 'itemCode', 'codeNamePrimaryLang', 'codeNameSecondaryLang', 'descriptionPrimaryLang',     
							  'descriptionSecondaryLang', 'parentCodeID', 'parentItemCode', 'parentCodeNamePrimaryLang', 'parentCodeNameSecondaryLang',
							  'parentLevelName', 'levelName', 'requestCreationDateTimeUtc', 'codeCreationDateTimeUtc', 'activeFrom',
							  'activeTo', 'active', 'status', 'statusReason'])
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('ETA/Items/Index', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
				'codeID'	=>	'Code ID'
			])->addColumns([
                'codeTypeName'	=>	'codeTypeName'	,
				'codeID'	=>	'codeID'	,
				'itemCode'	=>	'itemCode'	,
				'codeNamePrimaryLang'	=>	'codeNamePrimaryLang'	,
				'codeNameSecondaryLang'	=>	'codeNameSecondaryLang'	,
				'descriptionPrimaryLang'	=>	'descriptionPrimaryLang'	,
				'descriptionSecondaryLang'	=>	'descriptionSecondaryLang'	,
				'parentCodeID'	=>	'parentCodeID'	,
				'parentItemCode'	=>	'parentItemCode'	,
				'parentCodeNamePrimaryLang'	=>	'parentCodeNamePrimaryLang'	,
				'parentCodeNameSecondaryLang'	=>	'parentCodeNameSecondaryLang'	,
				'parentLevelName'	=>	'parentLevelName'	,
				'levelName'	=>	'levelName'	,
				'requestCreationDateTimeUtc'	=>	'requestCreationDateTimeUtc'	,
				'codeCreationDateTimeUtc'	=>	'codeCreationDateTimeUtc'	,
				'activeFrom'	=>	'activeFrom'	,
				'activeTo'	=>	'activeTo'	,
				'active'	=>	'active'	,
				'status'	=>	'status'	,
				'statusReason'	=>	'statusReason'	,
            ]);
        });
    }

}
