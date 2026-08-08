<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\superadmins\SuperAdminDashboardController;
use App\Http\Controllers\superadmins\SuperAdminAccountController;
use App\Http\Controllers\superadmins\SuperAdminSubjectListController;
use App\Http\Controllers\superadmins\SuperAdminFacultyInformationController;
use App\Http\Controllers\superadmins\SuperAdminStudentController;
use App\Http\Controllers\superadmins\SuperAdminManageSectionController;
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

// Super Admin Auth Routes
Route::get('/auth/superadmin', [AuthController::class, 'SuperAdminLoginPage'])->name('superadmin.login.page');
Route::post('/auth/superadmin', [AuthController::class, 'SuperAdminLogin'])->name('superadmin.login.submit');
Route::post('/superadmin/logout', [AuthController::class, 'SuperAdminLogout'])->name('superadmin.logout');

// Super Admin Protected Routes
Route::middleware(['superadmin'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'SuperAdminDashboardPage'])->name('superadmin.dashboard.page');
    Route::get('/superadmin/accounts', [SuperAdminAccountController::class, 'SuperAdminAccountPage'])->name('superadmin.accounts.page');
    Route::get('/superadmin/subjects', [SuperAdminSubjectListController::class, 'SuperAdminSubjectListPage'])->name('superadmin.subjects.page');
    Route::get('/superadmin/faculty', [SuperAdminFacultyInformationController::class, 'SuperAdminFacultyInformationPage'])->name('superadmin.faculty.page');
    Route::get('/superadmin/students', [SuperAdminStudentController::class, 'SuperAdminStudentPage'])->name('superadmin.students.page');
    Route::get('/superadmin/sections', [SuperAdminManageSectionController::class, 'SuperAdminManageSectionPage'])->name('superadmin.sections.page');
});
