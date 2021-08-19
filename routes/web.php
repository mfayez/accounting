<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ETAController;

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

Route::get('/generate/models', '\\Jimbolino\\Laravel\\ModelBuilder\\ModelGenerator5@start');

Route::get('/', function () {
    //return Inertia::render('Welcome', [
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
	Route::get('/dashboard', function () {
		//Session()->flash('flash.banner', 'Yay it works!');
		//Session()->flash('flash.bannerStyle', 'danger');
    	return Inertia::render('Dashboard');
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
	
	Route::post('/ETA/Items/Sync', [ETAController::class, 'SyncItems'])->name("eta.items.sync");
});

Route::get('/language/{language}', function ($language) {
    Session()->put('locale', $language);
    return redirect()->back();
})->name('language');

