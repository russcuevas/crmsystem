<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\superadmins\SuperAdminDashboardController;
use App\Http\Controllers\superadmins\SuperAdminAccountController;
use App\Http\Controllers\superadmins\SuperAdminSubjectListController;
use App\Http\Controllers\superadmins\SuperAdminFacultyInformationController;
use App\Http\Controllers\superadmins\SuperAdminStudentController;
use App\Http\Controllers\superadmins\SuperAdminManageSectionController;
use App\Http\Controllers\superadmins\SuperAdminAssignedSubjectController;
use App\Http\Controllers\superadmins\SuperAdminSchoolYearController;
use App\Http\Controllers\superadmins\SuperAdminEnrollmentController;
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
    Route::post('/superadmin/accounts/update/{id}', [SuperAdminAccountController::class, 'update'])->name('superadmin.accounts.update');
    Route::get('/superadmin/subjects', [SuperAdminSubjectListController::class, 'SuperAdminSubjectListPage'])->name('superadmin.subjects.page');
    Route::post('/superadmin/subjects/store', [SuperAdminSubjectListController::class, 'store'])->name('superadmin.subjects.store');
    Route::post('/superadmin/subjects/update/{id}', [SuperAdminSubjectListController::class, 'update'])->name('superadmin.subjects.update');
    Route::delete('/superadmin/subjects/delete/{id}', [SuperAdminSubjectListController::class, 'destroy'])->name('superadmin.subjects.destroy');
    Route::get('/superadmin/faculty', [SuperAdminFacultyInformationController::class, 'SuperAdminFacultyInformationPage'])->name('superadmin.faculty.page');
    Route::post('/superadmin/faculty/store', [SuperAdminFacultyInformationController::class, 'store'])->name('superadmin.faculty.store');
    Route::post('/superadmin/faculty/update/{id}', [SuperAdminFacultyInformationController::class, 'update'])->name('superadmin.faculty.update');
    Route::delete('/superadmin/faculty/delete/{id}', [SuperAdminFacultyInformationController::class, 'destroy'])->name('superadmin.faculty.destroy');
    Route::get('/superadmin/students', [SuperAdminStudentController::class, 'SuperAdminStudentPage'])->name('superadmin.students.page');
    Route::post('/superadmin/students/store', [SuperAdminStudentController::class, 'store'])->name('superadmin.students.store');
    Route::post('/superadmin/students/update/{id}', [SuperAdminStudentController::class, 'update'])->name('superadmin.students.update');
    Route::delete('/superadmin/students/delete/{id}', [SuperAdminStudentController::class, 'destroy'])->name('superadmin.students.destroy');
    Route::get('/superadmin/students/{id}', [SuperAdminStudentController::class, 'SuperAdminStudentShowPage'])->name('superadmin.students.show');
    Route::get('/superadmin/school-years', [SuperAdminSchoolYearController::class, 'SuperAdminSchoolYearPage'])->name('superadmin.school_years.page');
    Route::post('/superadmin/school-years/store', [SuperAdminSchoolYearController::class, 'store'])->name('superadmin.school_years.store');
    Route::post('/superadmin/school-years/update/{id}', [SuperAdminSchoolYearController::class, 'update'])->name('superadmin.school_years.update');
    Route::post('/superadmin/school-years/switch', [SuperAdminSchoolYearController::class, 'switch'])->name('superadmin.school_years.switch');
    Route::post('/superadmin/school-year/switch', [SuperAdminSchoolYearController::class, 'switch'])->name('superadmin.school_year.switch');
    Route::post('/superadmin/school-years/set-active/{id}', [SuperAdminSchoolYearController::class, 'setActive'])->name('superadmin.school_years.setActive');
    Route::delete('/superadmin/school-years/delete/{id}', [SuperAdminSchoolYearController::class, 'destroy'])->name('superadmin.school_years.destroy');
    Route::get('/superadmin/sections', [SuperAdminManageSectionController::class, 'SuperAdminManageSectionPage'])->name('superadmin.sections.page');
    Route::post('/superadmin/sections/store', [SuperAdminManageSectionController::class, 'store'])->name('superadmin.sections.store');
    Route::post('/superadmin/sections/update/{id}', [SuperAdminManageSectionController::class, 'update'])->name('superadmin.sections.update');
    Route::delete('/superadmin/sections/delete/{id}', [SuperAdminManageSectionController::class, 'destroy'])->name('superadmin.sections.destroy');
    Route::get('/superadmin/assigned-subjects', [SuperAdminAssignedSubjectController::class, 'SuperAdminAssignedSubjectPage'])->name('superadmin.assigned_subjects.page');
    Route::post('/superadmin/assigned-subjects/store', [SuperAdminAssignedSubjectController::class, 'store'])->name('superadmin.assigned_subjects.store');
    Route::post('/superadmin/assigned-subjects/update/{id}', [SuperAdminAssignedSubjectController::class, 'update'])->name('superadmin.assigned_subjects.update');
    Route::delete('/superadmin/assigned-subjects/delete/{id}', [SuperAdminAssignedSubjectController::class, 'destroy'])->name('superadmin.assigned_subjects.destroy');
    Route::get('/superadmin/enrollment', [SuperAdminEnrollmentController::class, 'SuperAdminEnrollmentPage'])->name('superadmin.enrollment.page');
    Route::post('/superadmin/enrollment/store', [SuperAdminEnrollmentController::class, 'store'])->name('superadmin.enrollment.store');
    Route::post('/superadmin/enrollment/update/{id}', [SuperAdminEnrollmentController::class, 'update'])->name('superadmin.enrollment.update');
    Route::delete('/superadmin/enrollment/delete/{id}', [SuperAdminEnrollmentController::class, 'destroy'])->name('superadmin.enrollment.destroy');
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
