<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\superadmins\SuperAdminDashboardController;
use App\Http\Controllers\superadmins\SuperAdminAccountController;
use App\Http\Controllers\superadmins\SuperAdminSubjectListController;
use App\Http\Controllers\superadmins\SuperAdminFacultyInformationController;
use App\Http\Controllers\superadmins\SuperAdminStudentController;
use App\Http\Controllers\superadmins\SuperAdminManageSectionController;
use App\Http\Controllers\superadmins\SuperAdminSchoolYearController;
use App\Http\Controllers\superadmins\SuperAdminGradeController;
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

// Super Admin Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/superadmin/logout', [AuthController::class, 'SuperAdminLogout'])->name('superadmin.logout');
    Route::get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'SuperAdminDashboardPage'])->name('superadmin.dashboard.page');
    Route::get('/superadmin/accounts', [SuperAdminAccountController::class, 'SuperAdminAccountPage'])->name('superadmin.accounts.page');
    Route::get('/superadmin/subjects', [SuperAdminSubjectListController::class, 'SuperAdminSubjectListPage'])->name('superadmin.subjects.page');
    Route::get('/superadmin/faculty', [SuperAdminFacultyInformationController::class, 'SuperAdminFacultyInformationPage'])->name('superadmin.faculty.page');
    Route::get('/superadmin/students', [SuperAdminStudentController::class, 'SuperAdminStudentPage'])->name('superadmin.students.page');
    Route::get('/superadmin/students/{id}', [SuperAdminStudentController::class, 'SuperAdminStudentShowPage'])->name('superadmin.students.show');
    Route::get('/superadmin/sections', [SuperAdminManageSectionController::class, 'SuperAdminManageSectionPage'])->name('superadmin.sections.page');
    Route::get('/superadmin/grades', [SuperAdminGradeController::class, 'SuperAdminGradePage'])->name('superadmin.grades.page');
    Route::post('/superadmin/grades/update-score', [SuperAdminGradeController::class, 'updateTaskScore'])->name('superadmin.grades.update_score');
    Route::post('/superadmin/grades/category/store', [SuperAdminGradeController::class, 'storeCategory'])->name('superadmin.grades.category.store');
    Route::post('/superadmin/grades/category/update/{id}', [SuperAdminGradeController::class, 'updateCategory'])->name('superadmin.grades.category.update');
    Route::delete('/superadmin/grades/category/delete/{id}', [SuperAdminGradeController::class, 'destroyCategory'])->name('superadmin.grades.category.destroy');
    Route::post('/superadmin/grades/task/store', [SuperAdminGradeController::class, 'storeTask'])->name('superadmin.grades.task.store');
    Route::post('/superadmin/grades/task/update/{id}', [SuperAdminGradeController::class, 'updateTask'])->name('superadmin.grades.task.update');
    Route::delete('/superadmin/grades/task/delete/{id}', [SuperAdminGradeController::class, 'destroyTask'])->name('superadmin.grades.task.destroy');
    Route::post('/superadmin/school-year/switch', [SuperAdminSchoolYearController::class, 'switch'])->name('superadmin.school_year.switch');
});
