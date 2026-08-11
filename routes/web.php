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
use App\Http\Controllers\superadmins\SuperAdminAdminController;
use App\Http\Controllers\admins\AdminDashboardController;
use App\Http\Controllers\admins\AdminAccountController;
use App\Http\Controllers\admins\AdminSubjectListController;
use App\Http\Controllers\admins\AdminFacultyInformationController;
use App\Http\Controllers\admins\AdminStudentController;
use App\Http\Controllers\admins\AdminSchoolYearController;
use App\Http\Controllers\admins\AdminManageSectionController;
use App\Http\Controllers\admins\AdminAssignedSubjectController;
use App\Http\Controllers\admins\AdminEnrollmentController;
use App\Http\Controllers\admins\AdminGradeController;
use App\Http\Controllers\junior_high_school\JuniorHighSchoolTeacherController;
use App\Http\Controllers\junior_high_school\JuniorHighSchoolStudentController;
use App\Http\Controllers\junior_high_school\JuniorHighSchoolEnrollmentController;
use App\Http\Controllers\junior_high_school\JuniorHighSchoolGradeController;
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
Route::post('/auth/junior-high-school', [AuthController::class, 'JuniorHighSchoolLogin'])->name('junior_high_school.login.submit');
Route::get('/auth/senior-high-school', [AuthController::class, 'SeniorHighSchoolLoginPage'])->name('senior_high_school.login.page');
Route::get('/auth/college', [AuthController::class, 'CollegeLoginPage'])->name('college.login.page');
Route::get('/auth/admin', [AuthController::class, 'AdminLoginPage'])->name('admin.login.page');
Route::post('/auth/admin', [AuthController::class, 'AdminLogin'])->name('admin.login.submit');

// Junior High School Teacher Protected Routes
Route::middleware(['auth', 'jhs.teacher'])->prefix('junior-high-school')->as('junior_high_school.')->group(function () {
    Route::get('/dashboard', [JuniorHighSchoolTeacherController::class, 'JuniorHighSchoolDashboardPage'])->name('dashboard.page');
    Route::post('/logout', [AuthController::class, 'JuniorHighSchoolLogout'])->name('logout');

    // Students Module (Handled Students Only)
    Route::get('/students', [JuniorHighSchoolStudentController::class, 'JuniorHighSchoolStudentPage'])->name('students.page');
    Route::post('/students/store', [JuniorHighSchoolStudentController::class, 'store'])->name('students.store');
    Route::post('/students/update/{id}', [JuniorHighSchoolStudentController::class, 'update'])->name('students.update');
    Route::delete('/students/delete/{id}', [JuniorHighSchoolStudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/{id}', [JuniorHighSchoolStudentController::class, 'show'])->name('students.show');

    // Enrollment Module (All JHS Students)
    Route::get('/enrollment', [JuniorHighSchoolEnrollmentController::class, 'JuniorHighSchoolEnrollmentPage'])->name('enrollment.page');
    Route::post('/enrollment/store', [JuniorHighSchoolEnrollmentController::class, 'store'])->name('enrollment.store');
    Route::post('/enrollment/update/{id}', [JuniorHighSchoolEnrollmentController::class, 'update'])->name('enrollment.update');
    Route::delete('/enrollment/delete/{id}', [JuniorHighSchoolEnrollmentController::class, 'destroy'])->name('enrollment.destroy');

    // Grades Module (Handled Subjects Only)
    Route::get('/grades', [JuniorHighSchoolGradeController::class, 'JuniorHighSchoolGradePage'])->name('grades.page');
    Route::get('/grades/advisory', [JuniorHighSchoolGradeController::class, 'JuniorHighSchoolAdvisoryGradePage'])->name('grades.advisory.page');
    Route::get('/grades/print-card/{student_id}', [JuniorHighSchoolGradeController::class, 'printReportCard'])->name('grades.print_card');
    Route::post('/grades/update-score', [JuniorHighSchoolGradeController::class, 'updateTaskScore'])->name('grades.update_score');
    Route::post('/grades/category/store', [JuniorHighSchoolGradeController::class, 'storeCategory'])->name('grades.category.store');
    Route::match(['post', 'put'], '/grades/category/update/{id}', [JuniorHighSchoolGradeController::class, 'updateCategory'])->name('grades.category.update');
    Route::delete('/grades/category/delete/{id}', [JuniorHighSchoolGradeController::class, 'destroyCategory'])->name('grades.category.destroy');
    Route::post('/grades/task/store', [JuniorHighSchoolGradeController::class, 'storeTask'])->name('grades.task.store');
    Route::match(['post', 'put'], '/grades/task/update/{id}', [JuniorHighSchoolGradeController::class, 'updateTask'])->name('grades.task.update');
    Route::delete('/grades/task/delete/{id}', [JuniorHighSchoolGradeController::class, 'destroyTask'])->name('grades.task.destroy');
    Route::post('/grades/attendance/date/store', [JuniorHighSchoolGradeController::class, 'storeAttendanceDate'])->name('grades.attendance.date.store');
    Route::post('/grades/attendance/update-status', [JuniorHighSchoolGradeController::class, 'updateAttendanceStatus'])->name('grades.attendance.update_status');
    Route::delete('/grades/attendance/date/delete', [JuniorHighSchoolGradeController::class, 'destroyAttendanceDate'])->name('grades.attendance.date.destroy');
    Route::post('/grades/compute-total', [JuniorHighSchoolGradeController::class, 'computeTotalGrades'])->name('grades.compute.total');
});

// Admin Protected Routes
Route::middleware(['auth'])->prefix('admin')->as('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'AdminLogout'])->name('logout');
    Route::get('/dashboard', [AdminDashboardController::class, 'AdminDashboardPage'])->name('dashboard.page');
    Route::get('/accounts', [AdminAccountController::class, 'AdminAccountPage'])->name('accounts.page');
    Route::post('/accounts/update/{id}', [AdminAccountController::class, 'update'])->name('accounts.update');
    Route::get('/subjects', [AdminSubjectListController::class, 'AdminSubjectListPage'])->name('subjects.page');
    Route::post('/subjects/store', [AdminSubjectListController::class, 'store'])->name('subjects.store');
    Route::post('/subjects/update/{id}', [AdminSubjectListController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/delete/{id}', [AdminSubjectListController::class, 'destroy'])->name('subjects.destroy');
    Route::get('/faculty', [AdminFacultyInformationController::class, 'AdminFacultyInformationPage'])->name('faculty.page');
    Route::post('/faculty/store', [AdminFacultyInformationController::class, 'store'])->name('faculty.store');
    Route::post('/faculty/update/{id}', [AdminFacultyInformationController::class, 'update'])->name('faculty.update');
    Route::delete('/faculty/delete/{id}', [AdminFacultyInformationController::class, 'destroy'])->name('faculty.destroy');
    Route::get('/students', [AdminStudentController::class, 'AdminStudentPage'])->name('students.page');
    Route::post('/students/store', [AdminStudentController::class, 'store'])->name('students.store');
    Route::post('/students/update/{id}', [AdminStudentController::class, 'update'])->name('students.update');
    Route::delete('/students/delete/{id}', [AdminStudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/{id}', [AdminStudentController::class, 'AdminStudentShowPage'])->name('students.show');
    Route::get('/school-years', [AdminSchoolYearController::class, 'AdminSchoolYearPage'])->name('school_years.page');
    Route::post('/school-years/store', [AdminSchoolYearController::class, 'store'])->name('school_years.store');
    Route::post('/school-years/update/{id}', [AdminSchoolYearController::class, 'update'])->name('school_years.update');
    Route::post('/school-years/switch', [AdminSchoolYearController::class, 'switch'])->name('school_years.switch');
    Route::post('/school-year/switch', [AdminSchoolYearController::class, 'switch'])->name('school_year.switch');
    Route::post('/school-years/set-active/{id}', [AdminSchoolYearController::class, 'setActive'])->name('school_years.setActive');
    Route::delete('/school-years/delete/{id}', [AdminSchoolYearController::class, 'destroy'])->name('school_years.destroy');
    Route::get('/sections', [AdminManageSectionController::class, 'AdminManageSectionPage'])->name('sections.page');
    Route::post('/sections/store', [AdminManageSectionController::class, 'store'])->name('sections.store');
    Route::post('/sections/update/{id}', [AdminManageSectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/delete/{id}', [AdminManageSectionController::class, 'destroy'])->name('sections.destroy');
    Route::get('/assigned-subjects', [AdminAssignedSubjectController::class, 'AdminAssignedSubjectPage'])->name('assigned_subjects.page');
    Route::post('/assigned-subjects/store', [AdminAssignedSubjectController::class, 'store'])->name('assigned_subjects.store');
    Route::post('/assigned-subjects/update/{id}', [AdminAssignedSubjectController::class, 'update'])->name('assigned_subjects.update');
    Route::delete('/assigned-subjects/delete/{id}', [AdminAssignedSubjectController::class, 'destroy'])->name('assigned_subjects.destroy');
    Route::get('/enrollment', [AdminEnrollmentController::class, 'AdminEnrollmentPage'])->name('enrollment.page');
    Route::post('/enrollment/store', [AdminEnrollmentController::class, 'store'])->name('enrollment.store');
    Route::post('/enrollment/update/{id}', [AdminEnrollmentController::class, 'update'])->name('enrollment.update');
    Route::delete('/enrollment/delete/{id}', [AdminEnrollmentController::class, 'destroy'])->name('enrollment.destroy');
    Route::get('/grades', [AdminGradeController::class, 'AdminGradePage'])->name('grades.page');
    Route::post('/grades/update-score', [AdminGradeController::class, 'updateTaskScore'])->name('grades.update_score');
    Route::post('/grades/category/store', [AdminGradeController::class, 'storeCategory'])->name('grades.category.store');
    Route::match(['post', 'put'], '/grades/category/update/{id}', [AdminGradeController::class, 'updateCategory'])->name('grades.category.update');
    Route::delete('/grades/category/delete/{id}', [AdminGradeController::class, 'destroyCategory'])->name('grades.category.destroy');
    Route::post('/grades/task/store', [AdminGradeController::class, 'storeTask'])->name('grades.task.store');
    Route::match(['post', 'put'], '/grades/task/update/{id}', [AdminGradeController::class, 'updateTask'])->name('grades.task.update');
    Route::delete('/grades/task/delete/{id}', [AdminGradeController::class, 'destroyTask'])->name('grades.task.destroy');
    Route::post('/grades/attendance/date/store', [AdminGradeController::class, 'storeAttendanceDate'])->name('grades.attendance.date.store');
    Route::post('/grades/attendance/update-status', [AdminGradeController::class, 'updateAttendanceStatus'])->name('grades.attendance.update_status');
    Route::delete('/grades/attendance/date/delete', [AdminGradeController::class, 'destroyAttendanceDate'])->name('grades.attendance.date.destroy');
    Route::post('/grades/subject/weights/update', [AdminGradeController::class, 'updateSubjectWeights'])->name('grades.subject.weights.update');
    Route::post('/grades/compute-total', [AdminGradeController::class, 'computeTotalGrades'])->name('grades.compute.total');
    Route::post('/grades/reset-total', [AdminGradeController::class, 'resetTotalGrades'])->name('grades.reset.total');
});

// Super Admin Auth Routes
Route::get('/auth/superadmin', [AuthController::class, 'SuperAdminLoginPage'])->name('superadmin.login.page');
Route::post('/auth/superadmin', [AuthController::class, 'SuperAdminLogin'])->name('superadmin.login.submit');

// Super Admin Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/superadmin/logout', [AuthController::class, 'SuperAdminLogout'])->name('superadmin.logout');
    Route::get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'SuperAdminDashboardPage'])->name('superadmin.dashboard.page');
    Route::get('/superadmin/accounts', [SuperAdminAccountController::class, 'SuperAdminAccountPage'])->name('superadmin.accounts.page');
    Route::post('/superadmin/accounts/update/{id}', [SuperAdminAccountController::class, 'update'])->name('superadmin.accounts.update');
    Route::get('/superadmin/admins', [SuperAdminAdminController::class, 'SuperAdminAdminPage'])->name('superadmin.admins.page');
    Route::post('/superadmin/admins/store', [SuperAdminAdminController::class, 'store'])->name('superadmin.admins.store');
    Route::post('/superadmin/admins/update/{id}', [SuperAdminAdminController::class, 'update'])->name('superadmin.admins.update');
    Route::delete('/superadmin/admins/delete/{id}', [SuperAdminAdminController::class, 'destroy'])->name('superadmin.admins.destroy');
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
    Route::match(['post', 'put'], '/superadmin/grades/category/update/{id}', [SuperAdminGradeController::class, 'updateCategory'])->name('superadmin.grades.category.update');
    Route::delete('/superadmin/grades/category/delete/{id}', [SuperAdminGradeController::class, 'destroyCategory'])->name('superadmin.grades.category.destroy');
    Route::post('/superadmin/grades/task/store', [SuperAdminGradeController::class, 'storeTask'])->name('superadmin.grades.task.store');
    Route::match(['post', 'put'], '/superadmin/grades/task/update/{id}', [SuperAdminGradeController::class, 'updateTask'])->name('superadmin.grades.task.update');
    Route::delete('/superadmin/grades/task/delete/{id}', [SuperAdminGradeController::class, 'destroyTask'])->name('superadmin.grades.task.destroy');
    Route::post('/superadmin/grades/attendance/date/store', [SuperAdminGradeController::class, 'storeAttendanceDate'])->name('superadmin.grades.attendance.date.store');
    Route::post('/superadmin/grades/attendance/update-status', [SuperAdminGradeController::class, 'updateAttendanceStatus'])->name('superadmin.grades.attendance.update_status');
    Route::delete('/superadmin/grades/attendance/date/delete', [SuperAdminGradeController::class, 'destroyAttendanceDate'])->name('superadmin.grades.attendance.date.destroy');
    Route::post('/superadmin/grades/subject/weights/update', [SuperAdminGradeController::class, 'updateSubjectWeights'])->name('superadmin.grades.subject.weights.update');
    Route::post('/superadmin/grades/compute-total', [SuperAdminGradeController::class, 'computeTotalGrades'])->name('superadmin.grades.compute.total');
    Route::post('/superadmin/grades/reset-total', [SuperAdminGradeController::class, 'resetTotalGrades'])->name('superadmin.grades.reset.total');
    Route::post('/superadmin/school-year/switch', [SuperAdminSchoolYearController::class, 'switch'])->name('superadmin.school_year.switch');
});
