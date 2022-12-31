<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ETA\ETAItem;
use App\Models\ETA\ETAInvoice;
use App\Models\ETA\Invoice;
use App\Http\Controllers\ETAController;
use App\Http\Controllers\APIController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceivedInvoiceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ETAArchiveController;
use App\Http\Controllers\ETAInvoiceController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SalesBuzzController;

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

Route::middleware(['auth:sanctum', 'verified', 'ETASettings'])->group(function () {
	Route::get('/user', function (Request $request) {
    	return $request->user();
	});

	Route::get('/invoices/pending', function (Request $request) {
		$temp = Invoice::with([
			'issuer', 'receiver', 'issuer.address', 'receiver.address',
			'invoicelines', 'invoicelines.unitvalue', 'invoicelines.taxableitems',
			'taxtotals',
		])->where(function($query) {
			$query->where('status', '=', 'approved');
	//			  ->orWhereNull('status');
		})->take(10)->get();
		return response()->json($temp);//->setEncodingOptions(JSON_NUMERIC_CHECK);
	});

	Route::get('/configurations', function (Request $request) {
		$temp = [
			"client_id" => SETTINGS_VAL('ETA Settings', 'client_id', ''),
			"client_secret" => SETTINGS_VAL('ETA Settings', 'client_secret1', ''),
			"production" => strpos(SETTINGS_VAL("ETA Settings", "login_url", "https://id.eta.gov.eg/connect/token"), "preprod") > 1 ? False : True
		];
		return response()->json($temp);//->setEncodingOptions(JSON_NUMERIC_CHECK);
	});

	Route::post('/invoices/upload', [ETAController::class, 'UploadInvoice']);

	Route::post('/invoices/update', [APIController::class, 'updateInvoices']);

	Route::get('/json/branches', [BranchController::class, 'index_json']);
	Route::get('/json/ActivityCodes.json', [SettingsController::class, 'indexActivityCodes_json']);

	Route::get('/json/sb/branches/map', [SalesBuzzController::class, 'indexBranchesMap_json']);
	Route::post('/bin/sb/invoices/upload', [SalesBuzzController::class, 'UploadInvoice']);
});

