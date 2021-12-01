<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ETAController;
use App\Http\Controllers\ChartsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/generate/models', '\\Jimbolino\\Laravel\\ModelBuilder\\ModelGenerator5@start');

//Route::get('/', function () {
//    //return Inertia::render('Welcome', [
//    return Inertia::render('Auth/Login', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
	Route::get('/', function () {
		$sql = "select count(*) as invoicesCount,
			sum(totalSalesAmount) totalSalesAmount,
			sum(totalAmount) totalAmount,
			ifnull(sum(t2.amount), 0) taxTotal,
			ifnull(Status, 'pending') as Status
		from Invoice t1 left outer join 
			 (select invoice_id, sum(amount) as amount from TaxTotal group by invoice_id) t2  on t1.Id = t2.invoice_id
		group by Status";
		$data = DB::select($sql);
		//Session()->flash('flash.banner', 'Yay it works!');
		//Session()->flash('flash.bannerStyle', 'danger');
    	return Inertia::render('Dashboard', [
			'statistics' => $data
		]);
	})->name('dashboard');
	
	Route::get('/dashboard2', function () {
		return Inertia::render('index');
	})->name('dashboard2');
	
	Route::get('/dashboard3', function () {
    	return Inertia::render('Main');
	})->name('dashboard3');

	Route::resources([
		'invoices' => InvoiceController::class,
		'customers' => CustomerController::class,
		'branches' => BranchController::class,
		'items' => ItemController::class,
	]);
	
	Route::get ('/json/branches' , [BranchController::class, 'index_json'])->name("json.branches");
	Route::get ('/json/customers', [CustomerController::class,'index_json'])->name("json.customers");
	Route::get ('/json/eta/items', [ETAController::class,'indexItems_json'])->name("json.eta.items");

	Route::post('/ETA/Items/Upload' ,[ETAController::class, 'UploadItem'])->name("eta.items.upload");
	Route::post('/ETA/Items/Sync'	,[ETAController::class, 'SyncItems'])->name("eta.items.sync");
	Route::post('/ETA/Items/Add' 	,[ETAController::class, 'AddItem'])->name("eta.items.store");
	Route::get ('/ETA/Items'     	,[ETAController::class, 'indexItems'])->name("eta.items.index");
	#Route::post('/ETA/Invoices/Sync/Received', [ETAController::class, 'SyncReceivedInvoices'])->name("eta.invoices.sync.received");
	Route::post('/ETA/Invoices/Sync/Issued', [ETAController::class, 'SyncIssuedInvoices'])->name("eta.invoices.sync.issued");
	Route::post('/ETA/Invoices/Add' , [ETAController::class, 'AddInvoice'])->name("eta.invoices.store");
	Route::post('/ETA/Invoices/Upload' , [ETAController::class, 'UploadInvoice'])->name("eta.invoices.upload");
#todo mfayez change the controller method and implement it later
	Route::get ('/ETA/Invoices/Received/Index' , [ETAController::class, 'indexInvoices'])->name("eta.invoices.received.index");
	Route::get ('/ETA/Invoices/Issued/Index'   , [ETAController::class, 'indexIssued'])->name("eta.invoices.sent.index");

#charts data
	Route::get('/json/top/items', [ChartsController::class, 'topItems'])->name("json.top.items");
});

Route::get('/language/{language}', function ($language) {
    Session()->put('locale', $language);
    return redirect()->back();
})->name('language');

