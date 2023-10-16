<?php

use App\Http\Controllers\admin\DegreeController;
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
Route::resource('salary-type', \App\Http\Controllers\Admin\SalaryTypeController::class);
Route::resource('job-post', \App\Http\Controllers\Admin\JobPostController::class);
Route::resource('skill', SkillController::class);
Route::resource('exp', ExpController::class);
Route::resource('experience', ExperienceController::class);
Route::resource('major', \App\Http\Controllers\Admin\MajorController::class);
Route::resource('working-form', \App\Http\Controllers\Admin\WorkingFormController::class);
Route::resource('job_position', \App\Http\Controllers\Admin\JobPositionController::class);

<<<<<<< Updated upstream
Route::group(['middleware' => ['auth:company']], function () {
    Route::post('register', [\App\Http\Controllers\Company\LoginController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\Company\LoginController::class, 'login']);
});
=======
Route::group([
    'prefix' => 'company'
], function () {
    Route::post('login', [\App\Http\Controllers\Company\AuthController::class, 'login']);
    Route::group([
        'middleware' => 'auth:apicompany'
    ], function () {
        Route::get('user', [\App\Http\Controllers\Company\AuthController::class, 'user']);
        Route::delete('logout', [\App\Http\Controllers\Company\AuthController::class, 'logout']);


        // Route::resource('job_position', \App\Http\Controllers\Admin\JobPositionController::class);

    });
});
>>>>>>> Stashed changes
