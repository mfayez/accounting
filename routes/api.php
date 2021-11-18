<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ETAItem;
use App\Models\ETAInvoice;
use App\Models\Invoice;
use App\Http\Controllers\ETAController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/invoices/pending', function (Request $request) {
    $temp = Invoice::with([
		'issuer', 'receiver', 'issuer.address', 'receiver.address',
		'invoicelines', 'invoicelines.unitvalue', 'invoicelines.taxableitems',
		'taxtotals',
	])->where(function($query) {
		$query->where('status', '=', 'pending')
			  ->orWhereNull('status');
	})->first();
	return response()->json($temp);//->setEncodingOptions(JSON_NUMERIC_CHECK);
});

Route::middleware('auth:sanctum')->post('/invoices/upload', [ETAController::class, 'UploadInvoice']);

Route::middleware('auth:sanctum')->post('/invoices/update', function (Request $request) {
	$data = $request->all();
	foreach($data["acceptedDocuments"] as $document) {
		$invoice = Invoice::where('internalID', '=', $document["internalId"])
						->where(function ($query) {
							$query->where('status', '=', 'pending')
							      ->orWhereNull('status');
					    })->firstOrFail();
		$invoice->status = 'processing';
		$invoice->statusreason = 'Accepted for processing';
		$invoice->uuid = $document["uuid"];
		$invoice->submissionUUID = $data["submissionId"];
		$invoice->longId = $document["longId"];
		$invoice->save();
	}
	foreach($data["rejectedDocuments"] as $document) {
		$invoice = Invoice::where('internalID', '=', $document["internalId"])
						->where(function ($query) {
							$query->where('status', '=', 'pending')
							      ->orWhereNull('status');
					    })->firstOrFail();
		$invoice->status = 'rejected';
		$invoice->statusreason = json_encode($document["error"]);
		$invoice->save();
	}
	return response()->json(["status" => "ok"]);
});
