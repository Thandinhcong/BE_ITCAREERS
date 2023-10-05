<?php

use App\Http\Controllers\Packages\AuthController;
use App\Http\Controllers\Skill\AuthController as SkillAuthController;
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

Route::get('/skill', [SkillAuthController::class, 'index'])->name('skill.index');
Route::match(['GET', 'POST'], '/skill/add', [SkillAuthController::class, 'store'])->name('skill.add');
Route::match(['GET', 'POST'], '/skill/edit/{id}', [SkillAuthController::class, 'edit'])->name('skill.edit');
Route::get('/skill/delete/{id}', [SkillAuthController::class, 'destroy'])->name('skill.delete');
