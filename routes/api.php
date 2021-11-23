<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ETAItem;
use App\Models\ETAInvoice;
use App\Models\Invoice;
use App\Http\Controllers\ETAController;
use App\Http\Controllers\APIController;

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
	})->get();
	return response()->json($temp);//->setEncodingOptions(JSON_NUMERIC_CHECK);
});

Route::middleware('auth:sanctum')->post('/invoices/upload', [ETAController::class, 'UploadInvoice']);

Route::middleware('auth:sanctum')->post('/invoices/update', [APIController::class, 'updateInvoices']);

