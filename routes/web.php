<?php

use App\Http\Controllers\Packages\AuthController;
use App\Http\Controllers\Skill\AuthController as SkillAuthController;
use App\Jobs\SendEmailJob;
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

    $data['email'] = 'huynmph26141@fpt.edu.vn';

    dispatch(new SendEmailJob($data));

    dd('Email Send Successfully.');
});
