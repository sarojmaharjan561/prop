<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\BillTypeController;
use App\Http\Controllers\ItemTypeController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Route for Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Route for Unit Module
    Route::get('/unit', [UnitController::class, 'create'])->name('unit');
    Route::post('/unit', [UnitController::class, 'store'])->name('unit.store');    
    Route::get('/get-units', [UnitController::class, 'getUnits'])->name('unit.getUnits');    
    Route::get('/get-unit', [UnitController::class, 'getUnit'])->name('unit.getUnit');    
    Route::post('/update-unit', [UnitController::class, 'update'])->name('unit.updateUnit');    
    Route::delete('/delete-unit', [UnitController::class, 'destroy'])->name('unit.destroyUnit');    
    
    // Route for Bill Type Module
    Route::get('/bill-type', [BillTypeController::class, 'create'])->name('billtype');
    Route::post('/bill-type', [BillTypeController::class, 'store'])->name('billtype.store');    
    Route::get('/get-bill-types', [BillTypeController::class, 'getBillTypes'])->name('billtype.getBillTypes');    
    Route::get('/get-bill-type', [BillTypeController::class, 'getBillType'])->name('billtype.getBillType');    
    Route::post('/update-bill-type', [BillTypeController::class, 'update'])->name('billtype.updateBillType');    
    Route::delete('/delete-bill-type', [BillTypeController::class, 'destroy'])->name('billtype.destroyBillType'); 
    
    // Route for Item Type Module
    Route::get('/item-type', [ItemTypeController::class, 'create'])->name('itemtype');
    Route::post('/item-type', [ItemTypeController::class, 'store'])->name('itemtype.store');    
    Route::get('/get-item-types', [ItemTypeController::class, 'getItemTypes'])->name('itemtype.getItemTypes');    
    Route::get('/get-item-type', [ItemTypeController::class, 'getItemType'])->name('itemtype.getItemType');    
    Route::post('/update-item-type', [ItemTypeController::class, 'update'])->name('itemtype.updateItemType');    
    Route::delete('/delete-item-type', [ItemTypeController::class, 'destroy'])->name('itemtype.destroyItemType');
    
    // Route for Item Module
    Route::get('/item', [ItemController::class, 'create'])->name('item');
    Route::post('/item', [ItemController::class, 'store'])->name('item.store');    
    Route::get('/get-items', [ItemController::class, 'getItems'])->name('item.getItems');    
    Route::get('/get-item', [ItemController::class, 'getItem'])->name('item.getItem');    
    Route::post('/update-item', [ItemController::class, 'update'])->name('item.updateItem');    
    Route::delete('/delete-item', [ItemController::class, 'destroy'])->name('item.destroyItem');

    // Route for Bill Module
    Route::get('/bill', [BillController::class, 'index'])->name('bill');
    Route::get('/add-bill/{id?}', [BillController::class, 'create'])->name('createBill');
    Route::post('/bill', [BillController::class, 'store'])->name('bill.store');    
    Route::get('/get-bills', [BillController::class, 'getBills'])->name('bill.getBills');    
    Route::get('/get-bill', [BillController::class, 'getBill'])->name('bill.getbill');    
    Route::post('/update-bill', [BillController::class, 'update'])->name('bill.updateBill');    
    Route::delete('/delete-bill', [BillController::class, 'destroy'])->name('bill.destroyBill');
});