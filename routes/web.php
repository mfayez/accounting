<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ETAController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

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
        'users' => UserController::class,
    ]);

    Route::get('/json/branches', [BranchController::class, 'index_json'])->name("json.branches");
    Route::get('/json/customers', [CustomerController::class, 'index_json'])->name("json.customers");
    Route::get('/json/eta/items', [ETAController::class, 'indexItems_json'])->name("json.eta.items");

    Route::post('/ETA/Items/Upload', [ETAController::class, 'UploadItem'])->name("eta.items.upload");
    Route::post('/ETA/Items/Sync', [ETAController::class, 'SyncItems'])->name("eta.items.sync");
    Route::post('/ETA/Items/Add', [ETAController::class, 'AddItem'])->name("eta.items.store");
    Route::get('/ETA/Items', [ETAController::class, 'indexItems'])->name("eta.items.index");
    #Route::post('/ETA/Invoices/Sync/Received', [ETAController::class, 'SyncReceivedInvoices'])->name("eta.invoices.sync.received");
    Route::post('/ETA/Invoices/Sync/Issued', [ETAController::class, 'SyncIssuedInvoices'])->name("eta.invoices.sync.issued");
    Route::post('/ETA/Invoices/Add', [ETAController::class, 'AddInvoice'])->name("eta.invoices.store");
    Route::post('/ETA/Invoices/Cancel', [ETAController::class, 'CancelInvoice'])->name("eta.invoices.cancel");
    Route::post('/ETA/Invoices/Delete', [ETAController::class, 'DeleteInvoice'])->name("eta.invoices.delete");
    Route::post('/ETA/Invoices/Approve', [ETAController::class, 'ApproveInvoice'])->name("eta.invoices.approve");
    Route::post('/ETA/Invoices/Upload', [ETAController::class, 'UploadInvoice'])->name("eta.invoices.upload");
    Route::post('/ETA/Invoices/Upload/Cancel', [ETAController::class, 'CancelUpload'])->name("eta.invoices.upload.cancel");
    Route::get('/ETA/Invoices/Excel/Review', [ETAController::class, 'indexExcel'])->name("invoices.excel.review");
#todo mfayez change the controller method and implement it later
    Route::get('/ETA/Invoices/Received/Index', [ETAController::class, 'indexInvoices'])->name("eta.invoices.received.index");
    Route::get('/ETA/Invoices/Issued/Index/{upload_id?}', [ETAController::class, 'indexIssued'])->name("eta.invoices.sent.index");
#excel exports
	Route::get('/excel/items', function() {
		return App\Models\ETAItem::get()
			->downloadExcel("items.xlsx", $writerType = null, $headings = true);
	})->name('excel.items');
	Route::get('/excel/customers', function() {
		return App\Models\Receiver::get()
			->downloadExcel("customers.xlsx", $writerType = null, $headings = true);
	})->name('excel.customers');

#charts data
    Route::post('/json/top/items', [ChartsController::class, 'topItems'])->name("json.top.items");
    Route::post('/json/top/receivers', [ChartsController::class, 'topReceivers'])->name("json.top.receivers");
#pdf stuff
    Route::get('/pdf/invoice/{Id}', [PDFController::class, 'previewInvoice'])->name('pdf.invoice.preview');
});

Route::middleware(['web'])->group(function () {
    Route::get('/language/{language}', function ($language) {
        header("Refresh:0");
        Session()->put('locale', $language);
        return redirect()->back();
    })->name('language');
});
