<?php

use App\Http\Controllers\Admin\CandidatesController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyManagementController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\ExpController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Candidate\CandidateInformationController;
use App\Http\Controllers\Candidate\CandidateApplyController;
use App\Http\Controllers\Client\JobPostApplyController;
use App\Models\JobPostApply;
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
        Route::resource('level', LevelController::class);
        Route::get('user', [\App\Http\Controllers\Admin\LoginController::class, 'user']);
        Route::resource('candidates', CandidatesController::class);
        Route::resource('company-management', CompanyManagementController::class);
        Route::resource('candidate', CandidatesController::class);
        Route::resource('company', CompanyController::class);
    });
});
// Route::resource('company', CompanyController::class);
//Candidates
Route::group([
    'prefix' => 'candidate'
], function () {
    Route::post('register', [\App\Http\Controllers\Candidate\Auth\RegisterCandidateController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\Candidate\Auth\LoginController::class, 'login']);
    Route::group([
        'middleware' => 'auth:candidate-api'
    ], function () {
        Route::resource('candidate_apply', CandidateApplyController::class);
        Route::resource('candidate_information', CandidateInformationController::class);
        Route::get('user', [\App\Http\Controllers\Candidate\Auth\LoginController::class, 'user']);
        Route::delete('logout', [\App\Http\Controllers\Candidate\Auth\LoginController::class, 'logout']);
    });
});
Route::get('job_list', [\App\Http\Controllers\Client\JobListController::class, 'job_list']);
Route::get('job_detail/{id}', [\App\Http\Controllers\Client\JobListController::class, 'job_detail']);

//Company
Route::group([
    'prefix' => 'company'
], function () {
    Route::post('register', [\App\Http\Controllers\Company\Auth\RegisterCompanyController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\Company\Auth\LoginController::class, 'login']);
    Route::group([
        'middleware' => 'auth:company-api'
    ], function () {
        Route::resource('company_information', \App\Http\Controllers\Company\CompanyInformationController::class);
        Route::get('user', [\App\Http\Controllers\Company\Auth\LoginController::class, 'user']);
        Route::delete('logout', [\App\Http\Controllers\Company\Auth\LoginController::class, 'logout']);
    });
});
Route::resource('job_post', \App\Http\Controllers\Company\JobPostController::class);
Route::get('job_post_select', [\App\Http\Controllers\Company\JobPostController::class, 'job_post_select']);
Route::get('list_candidate_apply_job/{id}', [\App\Http\Controllers\Company\JobPostController::class, 'list_candidate_apply_job']);



//client/company
Route::resource('list_company', \App\Http\Controllers\Client\ListCompanyController::class);
