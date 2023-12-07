<?php

use App\Http\Controllers\Admin\CandidatesController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyManagementController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\ExpController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\ManagementWebController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\WebsiteManagementController;
use App\Http\Controllers\Candidate\Auth\RegisterCandidateController;
use App\Http\Controllers\Candidate\CandidateInformationController;
use App\Http\Controllers\Candidate\CandidateApplyController;
use App\Http\Controllers\Candidate\RefreshPasswordCandidateController;
use App\Http\Controllers\Candidate\RefreshPasswordController;
use App\Http\Controllers\Company\RefreshPasswordCompanyController;
use App\Models\Candidate;
use Google\Service\Adsense\Row;
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

// Route::get('/login', function () {
//     return response()->json([
//         'message' => 'Cần đăng nhập',
//     ], 200);
// })->name('login');



// Login google candidate
Route::get('/auth/google', [\App\Http\Controllers\Client\Auth\LoginGoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [\App\Http\Controllers\Client\Auth\LoginGoogleController::class, 'handleGoogleCallback']);



//Admin
Route::group([
    'prefix' => 'admin'
], function () {
    Route::post('login', [\App\Http\Controllers\Admin\LoginController::class, 'login']);
    Route::resource('job-post', \App\Http\Controllers\Admin\JobPostController::class);
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
        Route::resource('man-web', ManagementWebController::class);
        Route::resource('rev-sta', \App\Http\Controllers\Admin\RevenueStatisticsController::class);
    });
});
Route::resource('experience', ExperienceController::class);
Route::resource('major  ', \App\Http\Controllers\Admin\MajorController::class);

//Candidates
Route::group([
    'prefix' => 'candidate'
], function () {
    Route::post('register', [\App\Http\Controllers\Candidate\Auth\RegisterCandidateController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\Candidate\Auth\LoginController::class, 'login']);

    Route::group([
        'middleware' => 'auth:candidate-api'
    ], function () {
        Route::post('candidate_apply/{id}', [\App\Http\Controllers\Candidate\CandidateApplyController::class, 'candidate_apply']);
        Route::resource('candidate_information', CandidateInformationController::class);
        Route::resource('refreshPass', RefreshPasswordCandidateController::class);
        Route::get('user', [\App\Http\Controllers\Candidate\Auth\LoginController::class, 'user']);
        Route::delete('logout', [\App\Http\Controllers\Candidate\Auth\LoginController::class, 'logout']);
        //Việc làm đã ứng tuyển
        Route::get('job_apply', [\App\Http\Controllers\Candidate\CandidateApplyController::class, 'job_apply']);
        //Việc làm đã lưu
        Route::get('show_save_job_post', [\App\Http\Controllers\Candidate\CandidateApplyController::class, 'show_save_job_post']);
        //Lưu việc làm
        Route::post('save_job_post/{id}', [\App\Http\Controllers\Candidate\CandidateApplyController::class, 'save_job_post']);
        //Hủy lưu việc làm
        Route::post('cancel_save_job_post/{id}', [\App\Http\Controllers\Candidate\CandidateApplyController::class, 'cancel_save_job_post']);

        Route::post('find_job', [\App\Http\Controllers\Candidate\CandidateInformationController::class, 'findJob']);
        //Information Find Job
        Route::get('get_data_find_job', [\App\Http\Controllers\Candidate\CandidateInformationController::class, 'getDataInformationFindJob']);
        Route::post('save_info_find_job', [\App\Http\Controllers\Candidate\CandidateInformationController::class, 'saveInformationFindJob']);
        Route::get('get_info_find_job', [\App\Http\Controllers\Candidate\CandidateInformationController::class, 'getInfoFindJob']);
        // create cv
        Route::get('cv', [App\Http\Controllers\Client\CVController::class, 'index']);
        Route::post('active_cv', [\App\Http\Controllers\Client\CVController::class, 'activeCV']);
        Route::get('delete_cv/{id}', [\App\Http\Controllers\Client\CVController::class, 'destroyCv']);
        Route::post('create_cv', [\App\Http\Controllers\Client\CreateCvController::class, 'createCV']);
        Route::post('upload_cv', [\App\Http\Controllers\Client\CVController::class, 'store']);
        Route::get('get_data', [\App\Http\Controllers\Client\CreateCvController::class, 'getData']);
        Route::get('update_cv/{profile_id}', [\App\Http\Controllers\Client\CreateCvController::class, 'index']);
        Route::post('update_cv/update_info/{profile_id}', [\App\Http\Controllers\Client\CreateCvController::class, 'updateInfo']);
        // exp ,n n
        Route::post('update_cv/save_exp', [\App\Http\Controllers\Client\CreateCvController::class, 'saveExp']);
        Route::post('update_cv/update_exp/{id}', [\App\Http\Controllers\Client\CreateCvController::class, 'updateExp']);
        Route::get('update_cv/delete_exp/{id}', [\App\Http\Controllers\Client\CreateCvController::class, 'deleteExp']);
        // edu
        Route::post('update_cv/save_edu', [\App\Http\Controllers\Client\CreateCvController::class, 'saveEdu']);
        Route::post('update_cv/update_edu/{id}', [\App\Http\Controllers\Client\CreateCvController::class, 'updateEdu']);
        Route::get('update_cv/delete_edu/{id}', [\App\Http\Controllers\Client\CreateCvController::class, 'deleteEdu']);
        //project
        Route::post('update_cv/save_project', [\App\Http\Controllers\Client\CreateCvController::class, 'saveProject']);
        Route::post('update_cv/update_project/{id}', [\App\Http\Controllers\Client\CreateCvController::class, 'updateProject']);
        Route::get('update_cv/delete_project/{id}', [\App\Http\Controllers\Client\CreateCvController::class, 'deleteProject']);
        //skill
        Route::post('update_cv/save_skill', [\App\Http\Controllers\Client\CreateCvController::class, 'saveSkill']);
        Route::post('update_cv/update_skill/{id}', [\App\Http\Controllers\Client\CreateCvController::class, 'updateSkill']);
        Route::get('update_cv/delete_skill/{id}', [\App\Http\Controllers\Client\CreateCvController::class, 'deleteSkill']);
        //save cv
        Route::post('update_cv/saveCV/{profile_id}', [\App\Http\Controllers\Client\CreateCvController::class, 'saveCV']);
        //Việc làm đã ứng tuyển
        Route::get('job_apply', [\App\Http\Controllers\Candidate\CandidateApplyController::class, 'job_apply']);
        //Việc làm đã lưu
        Route::get('show_save_job_post', [\App\Http\Controllers\Candidate\CandidateApplyController::class, 'show_save_job_post']);
        //Lưu việc làm
        Route::post('save_job_post/{id}', [\App\Http\Controllers\Candidate\CandidateApplyController::class, 'save_job_post']);
        //Hủy lưu việc làm
        Route::post('cancel_save_job_post/{id}', [\App\Http\Controllers\Candidate\CandidateApplyController::class, 'cancel_save_job_post']);
        //Payment
        Route::get('get_list_package', [\App\Http\Controllers\Candidate\PaymentCandidateController::class, 'getListPackage']);
        Route::post('insert_invoice', [\App\Http\Controllers\Candidate\PaymentCandidateController::class, 'insertInvoice']);
        Route::post('payment', [\App\Http\Controllers\Candidate\PaymentCandidateController::class, 'payment'])->name('payment');
        Route::get('vnpay_return', [\App\Http\Controllers\Candidate\PaymentCandidateController::class, 'vnpay_return'])->name('vnpay_return');
        Route::get('vnpay_ipn', [\App\Http\Controllers\Candidate\PaymentCandidateController::class, 'vnpay_ipn'])->name('vnpay_ipn');
        Route::get('history_payment', [\App\Http\Controllers\Candidate\PaymentCandidateController::class, 'historyPayment'])->name('historyPayment');
    });
});
Route::get('job_list', [\App\Http\Controllers\Client\JobListController::class, 'job_list']);
Route::get('job_detail/{id}', [\App\Http\Controllers\Client\JobListController::class, 'job_detail']);
//client/company
Route::resource('list_company', \App\Http\Controllers\Client\ListCompanyController::class);
Route::get('search', [\App\Http\Controllers\Client\SearchController::class, 'search']);
Route::get('select_salary_result', [\App\Http\Controllers\Client\SearchController::class, 'select_salary_result']);
Route::get('location_work', [\App\Http\Controllers\Client\JobListController::class, 'location_work']);

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
        Route::resource('job_post', \App\Http\Controllers\Company\JobPostController::class);
        //Đăng lại bài hết hạn
        Route::post('extend_job_post/{id}', [\App\Http\Controllers\Company\JobPostController::class, 'extend_job_post']);
        //Dừng tuyển bài đăng
        Route::post('stop_job_post/{id}', [\App\Http\Controllers\Company\JobPostController::class, 'stop_job_post']);
        //Hiển thị bài đăng hết hạn
        Route::get('job_post_expires', [\App\Http\Controllers\Company\JobPostController::class, 'job_post_expires']);
        Route::get('job_post_select', [\App\Http\Controllers\Company\JobPostController::class, 'job_post_select']);
        //Hiển thị tất cả hồ sơ ứng viên theo id bài đăng
        Route::get('list_candidate_apply_job/{id}', [\App\Http\Controllers\Company\ManageCandidateApply::class, 'list_candidate_apply_job']);
        //Xem hồ sơ của 1 ứng viên theo id bài đăng, gửi email cho ứng viên biết
        Route::get('candidate_detail/{id}', [\App\Http\Controllers\Company\ManageCandidateApply::class, 'candidate_detail']);
        //Đánh giá ứng viên gửi email
        Route::post('assses_candidate/{id}', [\App\Http\Controllers\Company\ManageCandidateApply::class, 'assses_candidate']);
        //List ứng viên gửi ứng tuyển vào công ty
        Route::get('list_candidate_applied', [\App\Http\Controllers\Company\ManageCandidateApply::class, 'list_candidate_applied']);
        //hiển thị ứng viên mở tìm kiếm việc
        Route::get('find_candidate', [\App\Http\Controllers\Company\ProfileCandidate::class, 'index']);
        // Mở khóa ứng viên
        Route::post('open_profile/{id}', [\App\Http\Controllers\Company\ProfileCandidate::class, 'open_profile']);
        //hiển thị ứng viên đã mở khóa
        Route::get('profile_open', [\App\Http\Controllers\Company\ProfileCandidate::class, 'show_profile_open']);
        //hiển thị ứng viên đã lưu
        Route::get('profile_save', [\App\Http\Controllers\Company\ProfileCandidate::class, 'show_save_profile']);
        //Lưu ứng viên
        Route::post('save_profile/{id}', [\App\Http\Controllers\Company\ProfileCandidate::class, 'save_profile']);
        //Hủy lưu ứng viên
        Route::post('cancel_save_profile/{id}', [\App\Http\Controllers\Company\ProfileCandidate::class, 'cancel_save_profile']);
        Route::post('feeback_profile/{id}', [\App\Http\Controllers\Company\ProfileCandidate::class, 'feeback_profile']);
        //đánh giá ứng viên
        Route::get('user', [\App\Http\Controllers\Company\Auth\LoginController::class, 'user']);
        Route::resource('refreshPass', RefreshPasswordCompanyController::class);
        // Payment
        Route::get('get_list_package', [\App\Http\Controllers\Company\PaymentController::class, 'getListPackage']);
        Route::post('insert_invoice', [App\Http\Controllers\Company\PaymentController::class, 'insertInvoice']);
        Route::post('payment', [App\Http\Controllers\Company\PaymentController::class, 'payment'])->name('payment');
        Route::get('vnpay_return', [App\Http\Controllers\Company\PaymentController::class, 'vnpay_return'])->name('vnpay_return');
        Route::get('vnpay_ipn', [App\Http\Controllers\Company\PaymentController::class, 'vnpay_ipn'])->name('vnpay_ipn');
        Route::get('history_payment', [App\Http\Controllers\Company\PaymentController::class, 'historyPayment'])->name('historyPayment');
        Route::delete('logout', [\App\Http\Controllers\Company\Auth\LoginController::class, 'logout']);
        Route::resource('dashboard', \App\Http\Controllers\Company\DashBoardController::class);
    });
});
Route::post('job_post_type/{id}', [\App\Http\Controllers\Company\JobPostController::class, 'job_post_type']);
//List ứng viên gửi ứng tuyển vào công ty
Route::get('list_candidate_applied', [\App\Http\Controllers\Company\JobPostController::class, 'list_candidate_applied']);

//Hiển thị bài đăng hết hạn
Route::get('job_post_expires', [\App\Http\Controllers\Company\JobPostController::class, 'job_post_expires']);
//Đăng lại bài hết hạn
Route::post('extend_job_post/{id}', [\App\Http\Controllers\Company\JobPostController::class, 'extend_job_post']);
//Dừng tuyển bài đăng
// Route::post('stop_job_post/{id}', [\App\Http\Controllers\Company\JobPostController::class, 'stop_job_post']);
//hiển thị ứng viên mở tìm kiếm việc
Route::get('find_candidate', [\App\Http\Controllers\Company\ProfileCandidate::class, 'index']);
// hiển thị ứng viên đã mở khóa
Route::get('show_profile_open', [\App\Http\Controllers\Company\ProfileCandidate::class, 'show_profile_open']);
// Mở khóa ứng viên
Route::post('open_profile/{id}', [\App\Http\Controllers\Company\ProfileCandidate::class, 'open_profile']);
// hiển thị ứng viên đã lưu
Route::get('show_save_profile', [\App\Http\Controllers\Company\ProfileCandidate::class, 'show_save_profile']);
//Lưu ứng viên
Route::post('save_profile/{id}', [\App\Http\Controllers\Company\ProfileCandidate::class, 'save_profile']);
//Hủy lưu ứng viên
Route::post('cancel_save_profile/{id}', [\App\Http\Controllers\Company\ProfileCandidate::class, 'cancel_save_profile']);
Route::get('job_post_select', [\App\Http\Controllers\Company\JobPostController::class, 'job_post_select']);
Route::post('stop_job_post/{id}', [\App\Http\Controllers\Company\JobPostController::class, 'stop_job_post']);
//hiển thị ứng viên đã mở khóa
Route::get('profile_open', [\App\Http\Controllers\Company\ProfileCandidate::class, 'profile_open']);

//client/company
Route::resource('list_company', \App\Http\Controllers\Client\ListCompanyController::class);

// create cv
Route::get('get_data', [\App\Http\Controllers\Client\CreateCvController::class, 'getData']);


// Hiển thị thông tin website bên phía người dùng
Route::get('web_tt', [\App\Http\Controllers\Client\WebsiteManagement::class, 'index']);
