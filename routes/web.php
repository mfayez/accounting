<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ETAController;
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
    Route::get('/setup/step1', function () {
        return Inertia::render('Setup/Step1');
    })->name('setup.step1');

    Route::get('/setup/step2', function () {
        return Inertia::render('Setup/Step2');
    })->name('setup.step2');

    Route::get('/setup/step3', function () {
        return Inertia::render('Setup/Step3');
    })->name('setup.step3');

    Route::post('/setup/ping_eta', [ETAController::class, 'pingETA'])->name('setup.ping_eta');
    Route::get ('/json/settings', [SettingsController::class, 'index_json'])->name("settings.json");
	Route::post('/json/settings', [SettingsController::class, 'store'])->name("settings.store");
    Route::resources([
        'branches' => BranchController::class,
    ]);
});

Route::middleware(['auth:sanctum', 'verified', 'ETASettings'])->group(function () {
    
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
        'items' => ItemController::class,
        'users' => UserController::class,
        'pos' => POSController::class,
    ]);

    Route::get('/getBranchesImages/{ids}' , [BranchController::class , 'getBranchesimages'])->name('branches.getImages');

    Route::get('/json/branches', [BranchController::class, 'index_json'])->name("json.branches");
    Route::get('/json/customers', [CustomerController::class, 'index_json'])->name("json.customers");
    Route::get('/json/eta/items', [ETAController::class, 'indexItems_json'])->name("json.eta.items");
    Route::get('/json/eta/vendors', [ETAController::class, 'indexVendors_json'])->name("json.eta.vendors");
    Route::get('/json/ActivityCodes.json', [SettingsController::class, 'indexActivityCodes_json'])->name("json.eta.activityCodes");
    
    Route::post('/invoice/copy' , [ETAController::class , 'saveCopy'])->name('invoices.copy');
    Route::post('/ETA/Items/Upload', [ETAController::class, 'UploadItem'])->name("eta.items.upload");
    Route::post('/ETA/Items/Sync', [ETAController::class, 'SyncItems'])->name("eta.items.sync");
    Route::post('/ETA/Items/Requests/Sync', [ETAController::class, 'SyncItemsRequests'])->name("eta.items.requests.sync");
    Route::post('/ETA/Items/Add', [ETAController::class, 'AddItem'])->name("eta.items.store");
    Route::get('/ETA/Items', [ETAController::class, 'indexItems'])->name("eta.items.index");

    Route::post('/ETA/Invoices/Sync/Received', [ETAController::class, 'SyncReceivedInvoices'])->name("eta.invoices.sync.received");
    Route::post('/ETA/Invoices/Sync/Issued', [ETAController::class, 'SyncIssuedInvoices'])->name("eta.invoices.sync.issued");
    Route::post('/ETA/Invoices/Sync/Invoices', [ETAController::class, 'SyncInvoices'])->name("eta.invoices.sync.all");

    Route::post('/ETA/Invoices/Add', [ETAController::class, 'AddInvoice'])->name("eta.invoices.store");
    Route::post('/ETA/Invoices/Credit/Add', [ETAController::class, 'AddCredit'])->name("eta.invoices.credit.store");
    Route::post('/ETA/Invoices/Debit/Add', [ETAController::class, 'AddDebit'])->name("eta.invoices.debit.store");
    Route::post('/ETA/Invoices/Cancel', [ETAController::class, 'CancelInvoice'])->name("eta.invoices.cancel");
    Route::post('/ETA/Invoices/Delete', [ETAController::class, 'DeleteInvoice'])->name("eta.invoices.delete");
    Route::post('/ETA/Invoices/Approve', [ETAController::class, 'ApproveInvoice'])->name("eta.invoices.approve");
    Route::post('/ETA/Invoices/Upload', [ETAController::class, 'UploadInvoice'])->name("eta.invoices.upload");
    Route::post('/ETA/Invoices/Upload/Cancel', [ETAController::class, 'CancelUpload'])->name("eta.invoices.upload.cancel");
    Route::get('/ETA/Invoices/Excel/Review', [ETAController::class, 'indexExcel'])->name("invoices.excel.review");

#Receipt Controller
    Route::get('/ETA/Receipts/Index', [ReceiptController::class, 'Index'])->name("eta.receipts.index");
    Route::post('/ETA/Receipts/Upload', [ReceiptController::class, 'UploadReceipts'])->name("eta.receipts.upload");
    Route::post('/ETA/Receipts/Send', [ReceiptController::class, 'SignAndSendReceipt'])->name("eta.receipts.send");
    Route::post('/ETA/Receipts/SendAll', [ReceiptController::class, 'SignAndSendReceipts'])->name("eta.receipts.send.all");

    Route::get('/ETA/Invoice/Print', [ETAInvoiceController::class, 'downloadPDF'])->name('eta.invoice.download');
#todo mfayez change the controller method and implement it later
    Route::get('/ETA/Invoices/Issued/Index/{upload_id?}', [ETAController::class, 'indexIssued'])->name("eta.invoices.sent.index");

    #recieved invoices (purchases)
    Route::get('/ETA/Invoices/Received/Index', [ReceivedInvoiceController::class, 'indexInvoices'])->name("eta.invoices.received.index");
    Route::post('/ETA/Invoices/Received/Index', [ReceivedInvoiceController::class, 'updateDetails'])->name("eta.invoices.received.update_details");
    
#reports, each report should have 3 functions
    Route::get('/reports/summary', [ReportsController::class, 'summary'])->name("reports.summary.details");
    Route::post('/reports/summary/data', [ReportsController::class, 'summaryData'])->name("reports.summary.details.data");
    Route::post('/reports/summary/download1', [ReportsController::class, 'summaryDownload'])->name("reports.summary.details.download");
    Route::post('/reports/summary/download2', [ReportsController::class, 'summaryDownloadNew'])->name("reports.summary.details.download.new");
    Route::post('/reports/summary/compact', [ReportsController::class, 'summaryDownloadCompact'])->name("reports.summary.details.download.compact");
    Route::post('/reports/summaryOnly/download', [ReportsController::class, 'summaryOnlyData'])->name("reports.summary.summaryOnlyData.download");

    Route::get('/reports/purchase', [ReportsController::class, 'purchase'])->name("reports.summary.purchase");
    Route::post('/reports/purchase/data', [ReportsController::class, 'purchaseData'])->name("reports.summary.purchase.data");
    Route::post('/reports/purchase/download1', [ReportsController::class, 'purchaseDownload'])->name("reports.summary.purchase.download");
    Route::post('/reports/purchase/download2', [ReportsController::class, 'purchaseDownloadSummary'])->name("reports.summary.purchase.download2");

    Route::get('/reports/branches/sales', [ReportsController::class, 'branchesSales'])->name("reports.branches.sales");
    Route::post('/reports/branches/sales/data', [ReportsController::class, 'branchesSalesData'])->name("reports.branches.sales.data");
    Route::post('/reports/branches/sales/download', [ReportsController::class, 'branchesSalesDownload'])->name("reports.branches.sales.download");

    Route::get('/reports/customers/sales', [ReportsController::class, 'customersSales'])->name("reports.customers.sales");
    Route::post('/reports/customers/sales/data', [ReportsController::class, 'customersSalesData'])->name("reports.customers.sales.data");
    Route::post('/reports/customers/sales/download', [ReportsController::class, 'customersSalesDownload'])->name("reports.customers.sales.download");

    Route::get('/reports/invoices/statuses', [ReportsController::class, 'getInvoiceStatus'])->name("reports.invoices.statuses");

#excel exports
    Route::get('/excel/items', function () {
        return App\Models\ETAItem::get()
            ->downloadExcel("items.xlsx", $writerType = null, $headings = true);
    })->name('excel.items');
    Route::get('/excel/customers', function () {
        return App\Models\Receiver::get()
            ->downloadExcel("customers.xlsx", $writerType = null, $headings = true);
    })->name('excel.customers');

#ETA Archives
    Route::get('/ETA/Archives',        [ETAArchiveController::class, 'getArchiveRequests'])->name("archive.getArchiveRequests");
    Route::post('/ETA/Archives/Add',   [ETAArchiveController::class, 'store'])->name("archive.store");
    Route::get('/ETA/Archives/import', [ETAArchiveController::class, 'importArchive'])->name("archive.import");
#Local Archives
    Route::get('/Archives',         [ArchiveController::class, 'index'])->name("archive.index");
    Route::post('/Archives/Add',    [ArchiveController::class, 'store'])->name("archive.request.store");
    Route::get('/Archives/download/{Id}',[ArchiveController::class, 'downloadArchives'])->name("archive.download");
    
#charts data
    Route::post('/json/top/items', [ChartsController::class, 'topItems'])->name("json.top.items");
    Route::post('/json/top/receivers', [ChartsController::class, 'topReceivers'])->name("json.top.receivers");
#pdf stuff
    Route::get('/pdf/invoice/{Id}', [PDFController::class, 'previewInvoice'])->name('pdf.invoice.preview');
    Route::get('/pdf/invoices/{Ids}', [PDFController::class, 'previewInvoices'])->name('pdf.invoices.preview');
    Route::get('/pdf/invoice/download/{id}', [PDFController::class, 'downloadInvoice'])->name('pdf.invoice.download');
    
    Route::get('/reports/branches/purchases/{Ids}', [ETAInvoiceController::class, 'branchesPurchases'])->name("pdf.purchases");
    Route::get('/reports/zip/invoices/{Ids}', [ETAInvoiceController::class, 'archiveInvoices'])->name("zip.invoices");

#salesbuzz stuff
    Route::get('/sb/branches/map', [SalesBuzzController::class, 'indexBranchesMap'])->name("sb.branches.map.index");
    Route::get('/sb/items/map', [SalesBuzzController::class, 'indexItemsMap'])->name("sb.items.map.index");
    Route::post('/sb/items/map/upload', [SalesBuzzController::class, 'UploadItemsMap'])->name("sb.items.map.upload");
    Route::post('/sb/items/map/update', [SalesBuzzController::class, 'updateItem'])->name("sb.items.map.update");
    Route::post('/sb/items/map/delete', [SalesBuzzController::class, 'deleteItem'])->name("sb.items.map.delete");
    
    Route::post('/sb/sync_orders', [SalesBuzzController::class, 'syncSalesOrders'])->name("sb.sync_orders");
    Route::post('/sb/branches/map', [SalesBuzzController::class, 'updateBranchesMap'])->name("sb.branches.map.update");
    
});

Route::middleware(['web'])->group(function () {
    Route::get('/language/{language}', function ($language) {
        header("Refresh:0");
        Session()->put('locale', $language);
        return redirect()->back();
    })->name('language');
});
