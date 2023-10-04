<?php

use App\Http\Controllers\Packages\AuthController;
use App\Models\Packages;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('layout.main');
});
Route::get('/package', [AuthController::class, 'index'])->name('package.index');
Route::match(['GET', 'POST'], '/package/add', [AuthController::class, 'store'])->name('package.add');
Route::match(['GET', 'POST'], '/package/edit/{id}', [AuthController::class, 'edit'])->name('package.edit');
Route::get('/package/delete/{id}', [AuthController::class, 'destroy'])->name('package.delete');
