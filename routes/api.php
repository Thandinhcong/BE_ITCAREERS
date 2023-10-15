<?php

use App\Http\Controllers\Admin\PackageController;
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




// Login google candidate 
Route::get('/auth/google', [\App\Http\Controllers\Client\Auth\LoginGoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [\App\Http\Controllers\Client\Auth\LoginGoogleController::class, 'handleGoogleCallback']);


//Admin
Route::group([
    'prefix' => 'admin'
], function () {
    Route::post('login', [\App\Http\Controllers\Admin\LoginController::class, 'login']);
    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::resource('package', PackageController::class);
        Route::resource('skill', SkillController::class);
        Route::resource('exp', ExpController::class);
        Route::resource('experience', ExperienceController::class);
        Route::resource('major', \App\Http\Controllers\Admin\MajorController::class);
        Route::resource('salary_type', \App\Http\Controllers\Admin\SalaryTypeController::class);
        Route::resource('working-form', \App\Http\Controllers\Admin\WorkingFormController::class);
        Route::resource('job_position', \App\Http\Controllers\Admin\JobPositionController::class);
        Route::resource('job-post', \App\Http\Controllers\Admin\JobPostController::class);
        Route::delete('logout', [\App\Http\Controllers\Admin\LoginController::class, 'logout']);
    });
});
