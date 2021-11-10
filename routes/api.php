<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ETAItem;
use App\Models\Invoice;

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
	])->first();
	return response()->json($temp);//->setEncodingOptions(JSON_NUMERIC_CHECK);
});

Route::middleware('auth:sanctum')->get('/invoices/update', function (Request $request) {
	$invoice = Invoice::find($request->internal_id);
	$invoice->status = $request->status;
	$invoice->statusreason = $request->reason;
	$invoice->save();
});
