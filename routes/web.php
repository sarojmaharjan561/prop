<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
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
});