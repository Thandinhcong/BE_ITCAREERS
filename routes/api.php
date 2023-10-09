<?php

use App\Http\Controllers\Admin\ExpController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\SkillController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::resource('major', \App\Http\Controllers\Admin\MajorController::class);
Route::resource('skill', SkillController::class);
Route::resource('exp', ExpController::class);
Route::resource('experience', ExperienceController::class);
Route::resource('major', \App\Http\Controllers\Admin\MajorController::class);
Route::resource('working-form', \App\Http\Controllers\Admin\WorkingFormController::class);
Route::resource('job_position', \App\Http\Controllers\Admin\JobPositionController::class);
