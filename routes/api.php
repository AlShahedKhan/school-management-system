<?php

use App\Http\Controllers\Api\AdminPublicTranslationController;
use App\Http\Controllers\Api\AdminProfileEditController;
use App\Http\Controllers\Api\AdvancePaymentController;
use App\Http\Controllers\Api\AdminSmSRequestApproveController;
use App\Http\Controllers\Api\AdmissionController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DemoRequestController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\DropdownController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\RegisteredSchoolController;
use App\Http\Controllers\Api\SchoolAdmissionController;
use App\Http\Controllers\Api\SchoolBulkUploadController;
use App\Http\Controllers\Api\SchoolAnnouncementController;
use App\Http\Controllers\Api\SchoolApprovalController;
use App\Http\Controllers\Api\SchoolClassController;
use App\Http\Controllers\Api\PrincipalController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\SchoolDueListController;
use App\Http\Controllers\Api\SchoolEmployeeController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\SchoolExamAdmitCardController;
use App\Http\Controllers\Api\SchoolExamGradeController;
use App\Http\Controllers\Api\SchoolExamMarkSubmitController;
use App\Http\Controllers\Api\SchoolExamNameController;
use App\Http\Controllers\Api\SchoolExamResultFindController;
use App\Http\Controllers\Api\SchoolMeritListController;
use App\Http\Controllers\Api\SchoolExamRoutineController;
use App\Http\Controllers\Api\SchoolExamScheduleController;
use App\Http\Controllers\Api\SchoolExamSeatPlanController;
use App\Http\Controllers\Api\SchoolExpenseController;
use App\Http\Controllers\Api\SchoolDiscountController;
use App\Http\Controllers\Api\SchoolFeeDiscountController;
use App\Http\Controllers\Api\SchoolFeeTemplateController;
use App\Http\Controllers\Api\SchoolStudentFeeController;
use App\Http\Controllers\Api\SchoolFeeTypeController;
use App\Http\Controllers\Api\SchoolGroupController;
use App\Http\Controllers\Api\SchoolGuardianController;
use App\Http\Controllers\Api\SchoolHolidayController;
use App\Http\Controllers\Api\SchoolIncomeController;
use App\Http\Controllers\Api\SchoolMembershipController;
use App\Http\Controllers\Api\SchoolPaymentController;
use App\Http\Controllers\Api\SchoolPayrollController;
use App\Http\Controllers\Api\SchoolProfileEditController;
use App\Http\Controllers\Api\SchoolRoutineController;
use App\Http\Controllers\Api\SchoolSectionController;
use App\Http\Controllers\Api\SchoolSessionController;
use App\Http\Controllers\Api\SchoolSmsPackageController;
use App\Http\Controllers\Api\SchoolStudentController;
use App\Http\Controllers\Api\SchoolSubjectController;
use App\Http\Controllers\Api\SchoolSubscriptionController;
use App\Http\Controllers\Api\SchoolSyllabusController;
use App\Http\Controllers\Api\SchoolTeacherClassPermissionController;
use App\Http\Controllers\Api\SchoolTeacherController;
use App\Http\Controllers\Api\SmsPackageController;
use App\Http\Controllers\Api\TeacherProfileEditController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminDynamicOperationController;
use App\Http\Controllers\Api\AdminHomePageSettingController;


/*
|--------------------------------------------------------------------------
| Public Routes (No Login Required)
|--------------------------------------------------------------------------
*/

// Authentication - WITH SESSION MIDDLEWARE for web compatibility
Route::middleware(['web'])->post('/login', [AuthController::class, 'login'])->name('login');

// Redirect GET requests to login page (for browser access)
Route::get('/login', function () {
    return redirect('/')->with('error', 'Please use the login form.');
});

// Registration
Route::post('/school-register', [SchoolController::class, 'register']);
Route::post('/admission-register', [AdmissionController::class, 'register']);
Route::post('/demo-requests', [DemoRequestController::class, 'store']);

// Forget Password
Route::post('/password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/password/reset', [ForgotPasswordController::class, 'resetPassword']);

// Public Dropdowns for Admission
Route::get('/get-approved-schools', [AdmissionController::class, 'getApprovedSchools']);
Route::get('/get-school-classes/{school_id}', [AdmissionController::class, 'getClasses']);
Route::get('/get-groups/{school_id}/{class_id}', [AdmissionController::class, 'getGroups']);
Route::get('/get-sessions/{school_id}/{class_id}', [AdmissionController::class, 'getSessions']);
Route::get('/get-fees/{school_id}/{class_id}', [AdmissionController::class, 'getFees']);

// ZKTeco Device Attendance Endpoint
Route::post('/iclock/cdata', [AttendanceController::class, 'store']);
Route::get('/iclock/cdata', [AttendanceController::class, 'handleHeartbeat']);


/*
|--------------------------------------------------------------------------
| Protected Routes (Login Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(['web'])->post('/logout', [AuthController::class, 'logout']);

    // --- ADMIN SECTION ---

    // Admin Profile Edit
    Route::post('/admin/profile-update', [AdminProfileEditController::class, 'update']);

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // Public Translation Management
        Route::get('/public-translations', [AdminPublicTranslationController::class, 'index']);
        Route::post('/public-translations', [AdminPublicTranslationController::class, 'store']);
        Route::put('/public-translations/{publicTranslation}', [AdminPublicTranslationController::class, 'update']);

        // Device Management
        Route::apiResource('devices', DeviceController::class);
    });

    // Package Management
    Route::apiResource('packages', PackageController::class);
    Route::apiResource('sms-packages', SmsPackageController::class);

    // School Approval
    Route::get('/schools/pending', [SchoolApprovalController::class, 'pendingSchools']);
    Route::post('/schools/approve/{id}', [SchoolApprovalController::class, 'updateApprovalStatus']);
    Route::delete('/schools/delete/{id}', [SchoolApprovalController::class, 'deleteSchool']);

    // Registered School Management
    Route::get('/schools/registered', [RegisteredSchoolController::class, 'getRegisteredSchools']);
    Route::get('/schools/get/{id}', [RegisteredSchoolController::class, 'getSchool']);
    Route::put('/schools/update/{id}', [RegisteredSchoolController::class, 'updateSchool']);
    Route::delete('/schools/delete/{id}', [RegisteredSchoolController::class, 'deleteSchool']);

    // Admin Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions/status/{id}', [SubscriptionController::class, 'updateStatus']);
    Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy']);
    Route::get('/subscriptions/{id}/edit', [SubscriptionController::class, 'edit']);
    Route::post('/subscriptions/{id}/update', [SubscriptionController::class, 'update']);
    Route::get('/packages-fetch', [SubscriptionController::class, 'packages']);

    // Custom Package Upgrades (Sale, Contract, Subscription Overrides)
    Route::post('/subscriptions/upgrade/{id}', [SubscriptionController::class, 'upgrade']);

    // Admin SmS Package Requests Approval
    Route::get('/sms-requests', [AdminSmSRequestApproveController::class, 'index']);
    Route::post('/sms-requests/{id}/approve', [AdminSmSRequestApproveController::class, 'approve']);
    Route::post('/sms-requests/{id}/reject', [AdminSmSRequestApproveController::class, 'reject']);
    Route::put('/sms-requests/{id}', [AdminSmSRequestApproveController::class, 'update']); // For Edit
    Route::delete('/sms-requests/{id}', [AdminSmSRequestApproveController::class, 'destroy']); // For Delete


    // --- SCHOOL SECTION ---

    // School Profile Edit
    Route::post('/school/update-profile', [SchoolProfileEditController::class, 'update']);

    // Principal Management
    Route::get('/school/principal', [PrincipalController::class, 'show']);
    Route::post('/school/principal-update', [PrincipalController::class, 'update']);

    // Subscriptions
    Route::get('/current-subscription', [SchoolSubscriptionController::class, 'current']);
    Route::post('/subscription/renew/{id}', [SchoolSubscriptionController::class, 'renew']);
    Route::get('/school/packages-fetch', [SchoolSubscriptionController::class, 'packagesFetch']);
    Route::post('/school/subscription/change/{id}', [SchoolSubscriptionController::class, 'change']);

    // Teacher Management
    Route::get('/teachers', [SchoolTeacherController::class, 'index']);
    Route::post('/teachers', [SchoolTeacherController::class, 'store']);
    Route::get('/teachers/{id}', [SchoolTeacherController::class, 'show']);
    Route::put('/teachers/{id}', [SchoolTeacherController::class, 'update']);
    Route::delete('/teachers/{id}', [SchoolTeacherController::class, 'destroy']);
    Route::post('/teachers/{id}/deactivate', [SchoolTeacherController::class, 'deactivate']);

    Route::post('/teachers/{id}/activate', [SchoolTeacherController::class, 'activate']);

    Route::post('/teacher/change-password', [TeacherProfileEditController::class, 'changePassword']);

    Route::apiResource('teacher-permissions', SchoolTeacherClassPermissionController::class);

    // Student & Guardian
    Route::apiResource('guardians', SchoolGuardianController::class);
    Route::post('/school-admission', [SchoolAdmissionController::class, 'register']);
    Route::get('/school/students', [SchoolStudentController::class, 'index']);
    Route::get('/school/students/{id}', [SchoolStudentController::class, 'show']);
    Route::match(['post', 'put'], '/school/students/{id}', [SchoolStudentController::class, 'update']);
    Route::delete('/school/students/{id}', [SchoolStudentController::class, 'destroy']);
    Route::post('/school/students/status/{id}', [SchoolStudentController::class, 'updateStatus']);
    // New specific route for comprehensive details
    Route::get('/school/students/details/{id}', [SchoolStudentController::class, 'showDetails']);
    Route::post('/school/students/update-details/{id}', [SchoolStudentController::class, 'updateDetails']);

    // Student Bulk Upload
    Route::get('/school/student-import/template', [SchoolBulkUploadController::class, 'downloadTemplate']);
    Route::post('/school/student-import/upload',  [SchoolBulkUploadController::class, 'upload']);

    // School SMS Settings
    Route::get('/school/sms-settings', [\App\Http\Controllers\Api\SchoolSmsSettingController::class, 'index']);
    Route::post('/school/sms-settings', [\App\Http\Controllers\Api\SchoolSmsSettingController::class, 'store']);

    // Modified on 2026-07-07: Student Promotion API routes (added promote history)
    Route::get('/school/promote/students', [\App\Http\Controllers\Api\SchoolPromoteController::class, 'getStudents']);
    Route::get('/school/promote/history', [\App\Http\Controllers\Api\SchoolPromoteController::class, 'getPromotionHistory']);
    Route::post('/school/promote', [\App\Http\Controllers\Api\SchoolPromoteController::class, 'promote']);

    // Modified on 2026-07-09: Student Re-Admission API routes
    Route::get('/school/readmit/students', [\App\Http\Controllers\Api\SchoolReadmissionController::class, 'getStudents']);
    Route::get('/school/readmit/history', [\App\Http\Controllers\Api\SchoolReadmissionController::class, 'getReadmissionHistory']);
    Route::post('/school/readmit', [\App\Http\Controllers\Api\SchoolReadmissionController::class, 'readmit']);


    // Finance (Income)
    Route::apiResource('incomes', SchoolIncomeController::class);
    Route::post('incomes/{id}/resend-sms', [SchoolIncomeController::class, 'resendSms']);
    Route::get('incomes-export', [SchoolIncomeController::class, 'export']); // Full Report
    Route::get('incomes-receipt/{id}', [SchoolIncomeController::class, 'receipt']); // Single Receipt

    // Finance (Expense)
    Route::apiResource('expenses', ExpenseController::class);

    // HRM (Employee & Payroll)
    Route::apiResource('employees', EmployeeController::class);
    Route::get('payrolls/staff-details', [\App\Http\Controllers\Api\EmployeePayrollController::class, 'staffDetails']);
    Route::apiResource('payrolls', \App\Http\Controllers\Api\EmployeePayrollController::class);

    //School Membership
    Route::get('/memberships', [SchoolMembershipController::class, 'index']);
    Route::post('/memberships', [SchoolMembershipController::class, 'store']);
    Route::post('/memberships/update/{id}', [SchoolMembershipController::class, 'update']);
    Route::delete('/memberships/{id}', [SchoolMembershipController::class, 'destroy']);
    Route::get('/membership-list', [SchoolIncomeController::class, 'membershipList']);

    // Academic Setup
    Route::apiResource('classes', SchoolClassController::class);
    Route::apiResource('groups', SchoolGroupController::class);
    Route::apiResource('sections', SchoolSectionController::class);
    Route::apiResource('school-sessions', SchoolSessionController::class);
    Route::apiResource('school-subjects', SchoolSubjectController::class);
    Route::get('get-grading-systems', [SchoolSubjectController::class, 'getGradingSystems']);
    Route::apiResource('school-syllabuses', SchoolSyllabusController::class);
    Route::apiResource('school-routines', SchoolRoutineController::class);

    //Class Filter
    Route::get('/classes-filter', [SchoolClassController::class, 'classFilter']);

    //Group Filter
    Route::get('/groups-filter', [SchoolGroupController::class, 'groupFilter']);

    //section Filter
    Route::get('/sections-filter', [SchoolSectionController::class, 'sectionFilter']);

    //Session Filter
    Route::get('/sessions-filter', [SchoolSessionController::class, 'sessionFilter']);
    Route::get('/school-sessions/{id}/dates', [SchoolSessionController::class, 'getDates']);

    //Subject Filter
    Route::get('/subjects-filter', [SchoolSubjectController::class, 'subjectFilter']);

    //Syllabus Filter
    Route::get('/syllabuses-filter', [SchoolSyllabusController::class, 'syllabusFilter']);


    // Fees (New: Templates + Student Fees)
    Route::get('student-fees/by-student', [SchoolStudentFeeController::class, 'getStudentFees']);
    Route::apiResource('fee-templates', SchoolFeeTemplateController::class);
    Route::apiResource('student-fees', SchoolStudentFeeController::class);
    Route::post('fee-templates/{id}/generate-fees', [SchoolFeeTemplateController::class, 'generateStudentFees']);

    // Discounts (New system)
    Route::apiResource('discounts', SchoolDiscountController::class);

    // Fees (Legacy — kept for backward compatibility)
    Route::apiResource('fee-types', SchoolFeeTypeController::class);
    Route::apiResource('fee-discounts', SchoolFeeDiscountController::class);

    // Payments
    Route::prefix('school')->group(function () {
        // 1. Specific custom routes MUST come first
        Route::get('payments/get-total-fee', [SchoolPaymentController::class, 'getTotalFee']);
        Route::get('payments/download-pdf',  [SchoolPaymentController::class, 'downloadPdf']);
        Route::get('payments/slip-pdf',      [SchoolPaymentController::class, 'downloadSlipPdf']);
        Route::get('payments/slip-excel',    [SchoolPaymentController::class, 'downloadSlipExcel']);
        Route::post('payments/store-advance', [SchoolPaymentController::class, 'storeAdvance']);

        // 2. Then the resource routes
        Route::apiResource('payments', SchoolPaymentController::class);
    });

    // Month grid for a student
    Route::get('students/{id}/payment-months', [SchoolPaymentController::class, 'paymentMonths']);

    // Advance Payments — keep index for history, remove POST routes (replaced by storeAdvance)
    Route::prefix('advance-payments')->group(function () {
        Route::get('/', [AdvancePaymentController::class, 'index']);
    });

    // Employees
    Route::apiResource('employees', EmployeeController::class);

    // Payrolls
    Route::apiResource('school-payrolls', SchoolPayrollController::class);

    // Announcements
    Route::apiResource('school-announcements', SchoolAnnouncementController::class);

    // School Holidays
    Route::apiResource('school-holidays', SchoolHolidayController::class);

    // School Exams
    Route::apiResource('school-exam-names', SchoolExamNameController::class);
    Route::get('school-exam-names-export', [SchoolExamNameController::class, 'export']);

    // School Due Lists
    Route::get('/school-due-list', [SchoolDueListController::class, 'index']);
    Route::post('/due-list/pay', [SchoolDueListController::class, 'pay']);
    Route::post('/update-payment/{id}', [SchoolDueListController::class, 'updatePayment']);
    Route::delete('/delete-payment/{id}', [SchoolDueListController::class, 'destroy']);

    // School SMS Packages
    Route::get('/sms-packages', [SchoolSmsPackageController::class, 'index']);
    Route::post('/purchase-sms', [SchoolSmsPackageController::class, 'purchase']);
    Route::get('/sms-balance', [SchoolSmsPackageController::class, 'getBalance']);
    Route::get('/sms-purchase-history', [SchoolSmsPackageController::class, 'purchaseHistory']);


    // School Management Dropdown Routes
    Route::get('/get-school-classes', [DropdownController::class, 'getClasses']);
    Route::get('/get-school-groups', [DropdownController::class, 'getGroups']);
    Route::get('/get-school-sections', [DropdownController::class, 'getSections']);
    Route::get('/get-school-sessions', [DropdownController::class, 'getSessions']);
    Route::get('/get-school-sessions-all', [DropdownController::class, 'getSessionsWithInactiveData']);
    Route::get('/get-school-subjects', [DropdownController::class, 'getSubjects']);
    Route::get('/get-school-exams', [DropdownController::class, 'getExams']);
    Route::get('/get-school-info', [DropdownController::class, 'getSchoolInfo']);
    Route::get('/get-school-students', [DropdownController::class, 'getStudents']);
    Route::get('/get-admit-card-status', [DropdownController::class, 'checkAdmitCardStatus']);

    // School Exam Routines
    Route::apiResource('school-exam-routines', SchoolExamRoutineController::class);
    Route::get('school-exam-routines-export', [SchoolExamRoutineController::class, 'export']);

    // Exam Grade Module Routes
    Route::apiResource('school-exam-grades', SchoolExamGradeController::class);
    Route::get('school-exam-grades-export', [SchoolExamGradeController::class, 'export']);
    Route::get('gpa-thresholds', [SchoolExamGradeController::class, 'gpaThresholds']);


    // School Exam Admit Cards
    Route::apiResource('school-exam-admit-cards', SchoolExamAdmitCardController::class);
    Route::get('/check-admit-card-prerequisites', [SchoolExamAdmitCardController::class, 'checkPrerequisites']);
    Route::get('/get-students-list', [SchoolExamAdmitCardController::class, 'getStudents']);

    // School Exam Seat Plans
    Route::apiResource('school-exam-seat-plans', SchoolExamSeatPlanController::class);

    // Exam Mark Management Routes
    Route::apiResource('school-exam-marks', SchoolExamMarkSubmitController::class);

    // Exam Schedule Module Routes
    Route::group([], function () {
        Route::get('school-exam-schedules/counts', [SchoolExamScheduleController::class, 'getCounts']);
        Route::apiResource('school-exam-schedules', SchoolExamScheduleController::class);
    });

    // Exam Result Find Routes
    Route::post('/school-find-results', [SchoolExamResultFindController::class, 'findResult']);
    Route::post('/school-merit-list', [SchoolMeritListController::class, 'generate']);
    Route::post('/school-merit-list/export-pdf', [SchoolMeritListController::class, 'exportPdf']);
    Route::delete('/school-results/{id}', [SchoolExamResultFindController::class, 'destroy']);
});
// Modified on 2026-07-09: Made public to prevent guest session cookie overwrite during Axios calls
Route::get('/dynamic-operation', [AdminDynamicOperationController::class, 'index']);

Route::middleware(['web', 'auth', 'role:admin'])->group(function () {
    Route::post('/dynamic-operation/update-text', [AdminDynamicOperationController::class, 'updateText']);
    Route::post('/dynamic-operation/update-footer', [AdminDynamicOperationController::class, 'updateFooterContent']);
    Route::post('/dynamic-operation/upload-logo', [AdminDynamicOperationController::class, 'uploadLogo']);
    Route::post('/dynamic-operation/upload-brand-banner', [AdminDynamicOperationController::class, 'uploadBrandBanner']);
    Route::post('/dynamic-operation/upload-school-banners', [AdminDynamicOperationController::class, 'uploadSchoolBanners']);
    Route::post('/dynamic-operation/delete-image', [AdminDynamicOperationController::class, 'deleteImage']);
    Route::post('/dynamic-operation/delete-school-banner', [AdminDynamicOperationController::class, 'deleteSchoolBanner']);

    Route::get('/home-page-settings', [AdminHomePageSettingController::class, 'index']);
    Route::post('/home-page-settings', [AdminHomePageSettingController::class, 'update']);
});
