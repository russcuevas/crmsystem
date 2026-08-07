<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\superadmins\SuperAdminDashboardController;
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
    return view('welcome');
});

// Authentication Routes for School Levels & Admins
Route::get('/auth/elementary', [AuthController::class, 'ElementaryLoginPage'])->name('elementary.login.page');
Route::get('/auth/junior-high-school', [AuthController::class, 'JuniorHighSchoolLoginPage'])->name('junior_high_school.login.page');
Route::get('/auth/senior-high-school', [AuthController::class, 'SeniorHighSchoolLoginPage'])->name('senior_high_school.login.page');
Route::get('/auth/college', [AuthController::class, 'CollegeLoginPage'])->name('college.login.page');
Route::get('/auth/admin', [AuthController::class, 'AdminLoginPage'])->name('admin.login.page');
Route::get('/auth/superadmin', [AuthController::class, 'SuperAdminLoginPage'])->name('superadmin.login.page');

// Super Admin Routes
Route::get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'SuperAdminDashboardPage'])->name('superadmin.dashboard.page');
