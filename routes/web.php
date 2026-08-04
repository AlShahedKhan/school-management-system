<?php

use App\Http\Controllers\Admin\AdminDemoRequestController;
use App\Http\Controllers\Admin\AboutPageSettingController;
use App\Http\Controllers\Admin\AboutPersonController as AdminAboutPersonController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\DashboardNewsController as AdminDashboardNewsController;
use App\Http\Controllers\Admin\FeatureController as AdminFeatureController;
use App\Http\Controllers\Admin\PageShowcaseController;
use App\Http\Controllers\Api\AdminDynamicOperationController;
use App\Http\Controllers\Api\AdminHomePageSettingController;
use App\Http\Controllers\Admin\AdminSmsCampaignController;
use App\Http\Controllers\Landing\AboutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Landing\BlogController as LandingBlogController;
use App\Http\Controllers\Landing\DemoController;
use App\Http\Controllers\Landing\FeatureController as LandingFeatureController;
use App\Http\Controllers\Landing\HomeController;
use App\Http\Controllers\Landing\LanguageController;
use App\Http\Controllers\Landing\ManagementServiceController;
use App\Http\Controllers\Landing\PricingController;
use App\Http\Controllers\Landing\ResultVerificationController;
use App\Http\Controllers\Landing\SchoolManagementController;
use App\Http\Controllers\School\ResultPdfPreviewController;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;



Route::get('/link-storage', function () {
    Artisan::call('storage:link');
    return "Storage link created successfully!";
});


Route::get('/refresh', function () {
    Artisan::call('optimize:clear');
    return "Cleared Successfully!";
});


/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('public.language.switch');

Route::get('/result/verify', ResultVerificationController::class)
    ->middleware('signed')
    ->name('public.result.verify');

Route::get('/internal/result-pdf/{token}', ResultPdfPreviewController::class)
    ->middleware('signed')
    ->name('internal.school.result-pdf-preview');

Route::middleware('public.locale')->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/blogs', [LandingBlogController::class, 'index'])
        ->name('public.blogs.index');

    Route::get('/blogs/{blog}', [LandingBlogController::class, 'show'])
        ->name('public.blogs.show');

    Route::get('/features', [LandingFeatureController::class, 'index'])
        ->name('public.features.index');

    Route::get('/features/{feature}', [LandingFeatureController::class, 'show'])
        ->name('public.features.show');

    Route::get('/school-management', SchoolManagementController::class)
        ->name('public.school-management');

    Route::get('/digital-transformation', [ManagementServiceController::class, 'digitalTransformation'])
        ->name('public.digital-transformation');

    Route::get('/smart-bangladesh-2041', [ManagementServiceController::class, 'smartBangladesh'])
        ->name('public.smart-bangladesh');

    Route::get('/full-automation', [ManagementServiceController::class, 'fullAutomation'])
        ->name('public.full-automation');

    Route::get('/madrasha-management', [ManagementServiceController::class, 'madrasha'])
        ->name('public.madrasha-management');

    Route::get('/kindergarten-management', [ManagementServiceController::class, 'kindergarten'])
        ->name('public.kindergarten-management');

    Route::get('/pricing', [PricingController::class, 'index'])
        ->name('public.pricing.index');

    Route::get('/about', [AboutController::class, 'index'])
        ->name('public.about.index');

    Route::get('/about/people/{aboutPerson}', [AboutController::class, 'showPerson'])
        ->name('public.about.people.show');

    Route::get('/about/mission', [AboutController::class, 'mission'])
        ->name('public.about.mission');

    Route::get('/about/vision', [AboutController::class, 'vision'])
        ->name('public.about.vision');

    Route::get('/demo', [DemoController::class, 'index'])
        ->name('public.demo');

    Route::get('/login', function () {
        $packages = Package::where('is_active', 1)->get();
        return view('auth.landing', compact('packages'));
    })->name('landing');
});


Route::get('/say-hello', function () {
    return "Hello, World!";
})->name('say-hello');


/*
|--------------------------------------------------------------------------
| School Approval Status Page
|--------------------------------------------------------------------------
*/

Route::get('/school/approval-status', function (Request $request) {

    $id = $request->query('id');
    $name = $request->query('name');
    if (!$id) {
        return redirect('/');
    }
    return view('school.approval_status', compact('id', 'name'));
});


/*
|--------------------------------------------------------------------------
| Student Approval Status Page
|--------------------------------------------------------------------------
*/
Route::get('/student/approval-status', function (Request $request) {
    $id = $request->query('id');
    $name = $request->query('name');
    if (!$id) {
        return redirect('/');
    }
    return view('student.approval-status', compact('id', 'name'));
});



/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');

    Route::get('/admin/approval-schools', [DashboardController::class, 'approvalSchools'])
        ->name('admin.approval-schools');

    Route::get('/admin/registered-schools', [DashboardController::class, 'registeredSchools'])
        ->name('admin.registered-schools');

    Route::get('/admin/create-plan', [DashboardController::class, 'createPlan'])
        ->name('admin.create-plan');

    Route::get('/admin/sms-packages', [DashboardController::class, 'smsPackages'])
        ->name('admin.sms-packages');

    Route::get('/admin/sms-package-activation-requests', [DashboardController::class, 'smsPackageActivationRequests'])
        ->name('admin.sms-package-activation-requests');

    Route::get('/admin/dynamic-operation', [DashboardController::class, 'dynamicOperation'])
        ->name('admin.dynamic-operation');

    Route::get('/admin/dynamic-operation/general', [DashboardController::class, 'dynamicOperationGeneral'])
        ->name('admin.dynamic-operation.general');

    Route::get('/admin/dynamic-operation/home', [DashboardController::class, 'dynamicOperationHome'])
        ->name('admin.dynamic-operation.home');

    Route::get('/admin/dynamic-operation/translations', [DashboardController::class, 'dynamicOperationTranslations'])
        ->name('admin.dynamic-operation.translations');

    Route::get('/admin/subscriptions', [DashboardController::class, 'subscriptions'])
        ->name('admin.subscriptions');

    Route::get('/admin/demo-requests', [AdminDemoRequestController::class, 'index'])
        ->name('admin.demo-requests.index');

    Route::patch('/admin/demo-requests/{demoRequest}/status', [AdminDemoRequestController::class, 'updateStatus'])
        ->name('admin.demo-requests.update-status');

    Route::resource('/admin/dashboard-news', AdminDashboardNewsController::class)
        ->except('show')
        ->names('admin.dashboard-news');

    Route::resource('/admin/blogs', AdminBlogController::class)
        ->names('admin.blogs');

    Route::resource('/admin/features', AdminFeatureController::class)
        ->except('show')
        ->names('admin.features');

    Route::get('/admin/about/settings', [AboutPageSettingController::class, 'edit'])
        ->name('admin.about.settings.edit');

    Route::put('/admin/about/settings', [AboutPageSettingController::class, 'update'])
        ->name('admin.about.settings.update');

    Route::resource('/admin/about/people', AdminAboutPersonController::class)
        ->except('show')
        ->parameters(['people' => 'aboutPerson'])
        ->names('admin.about.people');

    Route::resource('admin/showcases', PageShowcaseController::class)
        ->names('admin.showcases');

    // Admin SMS Settings and Activations
    Route::get('/admin/sms-credentials', [\App\Http\Controllers\Admin\AdminSmsCredentialController::class, 'index'])->name('admin.sms-credentials');
    Route::post('/admin/sms-credentials', [\App\Http\Controllers\Admin\AdminSmsCredentialController::class, 'update'])->name('admin.sms-credentials.update');
    Route::get('/admin/sms-templates', [AdminSmsCampaignController::class, 'templatesIndex'])->name('admin.sms-templates');
    Route::post('/admin/sms-templates', [AdminSmsCampaignController::class, 'saveTemplate'])->name('admin.sms-templates.save');
    Route::delete('/admin/sms-templates/{id}', [AdminSmsCampaignController::class, 'destroyTemplate'])->name('admin.sms-templates.destroy');

    Route::get('/admin/sms-activations', [AdminSmsCampaignController::class, 'activationsIndex'])->name('admin.sms-activations');
    Route::post('/admin/sms-activations', [AdminSmsCampaignController::class, 'saveActivation'])->name('admin.sms-activations.save');
    Route::delete('/admin/sms-activations/{id}', [AdminSmsCampaignController::class, 'destroyActivation'])->name('admin.sms-activations.destroy');
    Route::post('/admin/sms-activations/{id}/toggle', [AdminSmsCampaignController::class, 'toggleActivation'])->name('admin.sms-activations.toggle');

    // Cascading Geolocation APIs for activations
    Route::get('/admin/locations/countries', [AdminSmsCampaignController::class, 'getCountries'])->name('admin.locations.countries');
    Route::get('/admin/locations/divisions', [AdminSmsCampaignController::class, 'getDivisions'])->name('admin.locations.divisions');
    Route::get('/admin/locations/districts', [AdminSmsCampaignController::class, 'getDistricts'])->name('admin.locations.districts');
    Route::get('/admin/locations/upazilas', [AdminSmsCampaignController::class, 'getUpazilas'])->name('admin.locations.upazilas');
    Route::get('/admin/locations/schools', [AdminSmsCampaignController::class, 'getSchools'])->name('admin.locations.schools');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('api')
    ->group(function () {
        Route::get('/dynamic-operation', [AdminDynamicOperationController::class, 'index']);
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


/*
|--------------------------------------------------------------------------
| School Routes
|---------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:school'])->group(function () {

    // -----------------------------
    // School Dashboard & Menu Pages
    // -----------------------------
    Route::get('/school/dashboard', [DashboardController::class, 'school'])->name('school.dashboard');

    // Teacher & Student
    Route::get('/school/teacher-registration', [DashboardController::class, 'teacherRegistration'])
        ->name('school.teacher-registration');
    Route::get('/school/get-teachers-data', [\App\Http\Controllers\Api\SchoolTeacherController::class, 'index'])
        ->name('school.teachers.data');
    Route::get('/school/teacher-id-card', [DashboardController::class, 'underConstruction'])
        ->name('school.teacher-id-card');
    Route::get('/school/teacher-attendance', [DashboardController::class, 'underConstruction'])
        ->name('school.teacher-attendance');

    Route::get('/school/student-admission', [DashboardController::class, 'studentAdmission'])
        ->name('school.student-admission');
    Route::get('/school/students', [DashboardController::class, 'studentLists'])
        ->name('school.students');
    // Modified on 2026-07-07: Map student-promote and history to DashboardController methods
    Route::get('/school/student-promote', [DashboardController::class, 'studentPromote'])
        ->name('school.student-promote');
    Route::get('/school/student-promote/history', [DashboardController::class, 'studentPromoteHistory'])
        ->name('school.student-promote-history');

    // Modified on 2026-07-09: Register Student Bulk Upload, Promote Request, and Re-Admission routes
    Route::get('/school/student-bulk-upload', [DashboardController::class, 'studentBulkUpload'])
        ->name('school.student-bulk-upload');
    Route::get('/school/student-promote-request', [DashboardController::class, 'underConstruction'])
        ->name('school.student-promote-request');
    Route::get('/school/student-re-admission', [DashboardController::class, 'studentReAdmission'])
        ->name('school.student-re-admission');

    Route::get('/school/student-class-time', [DashboardController::class, 'underConstruction'])
        ->name('school.student-class-time');
    Route::get('/school/student-id-card', [DashboardController::class, 'underConstruction'])
        ->name('school.student-id-card');
    Route::get('/school/student-attendance', [DashboardController::class, 'underConstruction'])
        ->name('school.student-attendance');

    // Academic Settings
    Route::get('/school/classes', [DashboardController::class, 'classes'])->name('school.classes');
    Route::get('/school/groups', [DashboardController::class, 'groups'])->name('school.groups');
    Route::get('/school/sections', [DashboardController::class, 'sections'])->name('school.sections');
    Route::get('/school/sessions', [DashboardController::class, 'sessions'])->name('school.sessions');
    Route::get('/school/subjects', [DashboardController::class, 'subjects'])->name('school.subjects');
    Route::get('/school/syllabus', [DashboardController::class, 'syllabus'])->name('school.syllabus');
    Route::get('/school/class-permission', [DashboardController::class, 'SchoolclassPermission'])->name('school.class-permission');
    Route::get('/school/class-routine', [DashboardController::class, 'classRoutine'])->name('school.class-routine');

    // Guardian & Parents
    Route::get('/school/guardians', [DashboardController::class, 'guardians'])->name('school.guardians');

    // Finance
    Route::get('/school/income', [DashboardController::class, 'income'])->name('school.income');
    Route::get('/school/membership', [DashboardController::class, 'membership'])
        ->name('school.membership');
    Route::get('/school/donate', [DashboardController::class, 'donate'])->name('school.donate');
    Route::get('/school/collection', [DashboardController::class, 'collection'])->name('school.collection');
    Route::get('/school/expense', [DashboardController::class, 'expense'])->name('school.expense');
    Route::get('/school/product', [DashboardController::class, 'underConstruction'])->name('school.product');
    Route::get('/school/supplier', [DashboardController::class, 'underConstruction'])->name('school.supplier');
    Route::get('/school/purchase', [DashboardController::class, 'underConstruction'])->name('school.purchase');
    Route::get('/school/due-paid', [DashboardController::class, 'underConstruction'])->name('school.due-paid');

    // HRM Management
    Route::get('/school/employee', [DashboardController::class, 'employee'])->name('school.employee');
    Route::get('/school/Payroll', [DashboardController::class, 'Payroll'])->name('school.Payroll');
    Route::get('/school/hrm-employee-coming-soon', [DashboardController::class, 'underConstruction'])
        ->defaults('development_notice', true)
        ->defaults('development_module', 'Employee')
        ->name('school.hrm-employee-coming-soon');
    Route::get('/school/hrm-payroll-coming-soon', [DashboardController::class, 'underConstruction'])
        ->defaults('development_notice', true)
        ->defaults('development_module', 'Payroll')
        ->name('school.hrm-payroll-coming-soon');
    Route::get('/school/hrm-expense-coming-soon', [DashboardController::class, 'underConstruction'])
        ->defaults('development_notice', true)
        ->defaults('development_module', 'Expense')
        ->name('school.hrm-expense-coming-soon');

    // Subscription / Plans
    Route::get('/school/current-plan', [DashboardController::class, 'currentPlan'])->name('school.current-plan');
    Route::get('/school/sms-package', [DashboardController::class, 'smsPackage'])
        ->name('school.sms-package');




    // ================= Fees =================
    Route::get('/school/fees-type', [DashboardController::class, 'feesType'])
        ->name('school.fees-type');

    Route::get('/school/fee-templates/backfill', [App\Http\Controllers\Api\SchoolFeeTemplateController::class, 'backfillFromLegacy'])
        ->name('school.fee-templates.backfill');

    Route::get('/school/discount', [DashboardController::class, 'discount'])
        ->name('school.discount');

    Route::get('/school/discounts', [DashboardController::class, 'discountsNew'])
        ->name('school.discounts');

    Route::get('/school/payment', [DashboardController::class, 'payment'])
        ->name('school.payment');

    Route::get('/school/payment/slip', [DashboardController::class, 'paymentSlip'])
        ->name('school.payment.slip');

    Route::get('/school/due-list', [DashboardController::class, 'dueList'])
        ->name('school.due-list');

    Route::get('/school/student-fees', [DashboardController::class, 'studentFees'])
        ->name('school.student-fees');




    // ================= HRM =================
    Route::get('/school/employee', [DashboardController::class, 'employee'])
        ->name('school.employee');

    Route::get('/school/payroll', [DashboardController::class, 'payroll'])
        ->name('school.payroll');

    Route::get('/school/role-permission', [DashboardController::class, 'underConstruction'])
        ->name('school.role-permission');

    // ================= Question Bank =================
    Route::get('/school/omr', [DashboardController::class, 'underConstruction'])
        ->name('school.omr');
    Route::get('/school/questions', [DashboardController::class, 'underConstruction'])
        ->name('school.questions');


    // ================= Notice =================
    Route::get('/school/announcement', [DashboardController::class, 'announcement'])
        ->name('school.announcement');


    // ================= Holiday =================
    Route::get('/school/create-holiday', [DashboardController::class, 'createHoliday'])
        ->name('school.create-holiday');

       
     // ================= Notification =================
     Route::get('/school/notice', [DashboardController::class, 'notice'])->name('school.notice');
     Route::get('/school/holiday', [DashboardController::class, 'holiday'])->name('school.holiday');
     Route::get('/school/leave', [DashboardController::class, 'leave'])->name('school.leave');
     Route::get('/school/notification-holiday-coming-soon', [DashboardController::class, 'underConstruction'])
        ->defaults('development_notice', true)
        ->defaults('development_module', 'Holiday')
        ->name('school.notification-holiday-coming-soon');
     Route::get('/school/notification-notice-coming-soon', [DashboardController::class, 'underConstruction'])
        ->defaults('development_notice', true)
        ->defaults('development_module', 'Notice')
        ->name('school.notification-notice-coming-soon');
     Route::get('/school/notification-leave-coming-soon', [DashboardController::class, 'underConstruction'])
        ->defaults('development_notice', true)
        ->defaults('development_module', 'Leave')
        ->name('school.notification-leave-coming-soon');


    // ================= Exam Management =================
    Route::get('/school/exam-name', [DashboardController::class, 'examName'])
        ->name('school.exam-name');

    Route::get('/school/exam-routine', [DashboardController::class, 'examRoutine'])
        ->name('school.exam-routine');

    Route::get('/school/grade', [DashboardController::class, 'grade'])
        ->name('school.grade');

    Route::get('/school/admit-card', [DashboardController::class, 'admitCard'])
        ->name('school.admit-card');

    Route::get('/school/seat-plan', [DashboardController::class, 'seatPlan'])
        ->name('school.seat-plan');

    Route::get('/school/mark-submit', [DashboardController::class, 'markSubmit'])
        ->name('school.mark-submit');

    Route::get('/school/schedule', [DashboardController::class, 'schedule'])
        ->name('school.schedule');

    Route::get('/school/result-find', [DashboardController::class, 'resultFind'])
        ->name('school.result-find');

    Route::get('/school/merit-list', [DashboardController::class, 'meritList'])
        ->name('school.merit-list');

    Route::get('/school/fail-list', [DashboardController::class, 'failList'])
        ->name('school.fail-list');

    Route::get('/school/certificate', [DashboardController::class, 'underConstruction'])
        ->name('school.certificate');

    Route::redirect('/school/landing-open', '/')
        ->name('school.landing-open');

    Route::get('/school/landing-customize', [DashboardController::class, 'underConstruction'])
        ->name('school.landing-customize');

    Route::get('/school/landing-blogs', [DashboardController::class, 'underConstruction'])
        ->name('school.landing-blogs');

    Route::get('/school/domain', [DashboardController::class, 'underConstruction'])
        ->name('school.domain');

    Route::get('/school/id-card-orders', [DashboardController::class, 'underConstruction'])
        ->name('school.id-card-orders');

    Route::get('/school/id-card-status', [DashboardController::class, 'underConstruction'])
        ->name('school.id-card-status');

    Route::get('/school/design-id-card', [DashboardController::class, 'underConstruction'])
        ->name('school.design-id-card');

    Route::get('/school/sms-settings', [DashboardController::class, 'smsSettings'])
        ->name('school.sms-settings');

    Route::get('/school/design-sms', [DashboardController::class, 'smsSettings'])
        ->name('school.design-sms');

    Route::get('/school/ai-call-registration', [DashboardController::class, 'underConstruction'])
        ->name('school.ai-call-registration');

    Route::get('/school/ai-call-topup', [DashboardController::class, 'underConstruction'])
        ->name('school.ai-call-topup');

    Route::get('/school/ai-call-history', [DashboardController::class, 'underConstruction'])
        ->name('school.ai-call-history');

    // ================= Inventory Management =================
    Route::get('/school/return', [DashboardController::class, 'return'])
        ->name('school.return');

    Route::get('/school/profit-loss', [DashboardController::class, 'profitLoss'])
        ->name('school.profit-loss');

    Route::get('/school/add-payment', [DashboardController::class, 'addPayment'])
        ->name('school.add-payment');

    // ================= Role Management =================
    // Route::get('/school/role-permission', [DashboardController::class, 'rolePermission'])
    //     ->name('school.role-permission');
});



/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {

    Route::get('/student/dashboard', [DashboardController::class, 'student'])
        ->name('student.dashboard');

    Route::get('/student/teacher-list', [DashboardController::class, 'studentTeacherList'])
        ->name('student.teacher.list');

    Route::get('/student/student-list', [DashboardController::class, 'studentList'])
        ->name('student.student.list');

    Route::get('/student/class-time', [DashboardController::class, 'classTime'])
        ->name('student.class.time');

    Route::get('/student/class-promote', [DashboardController::class, 'classPromote'])
        ->name('student.class.promote');

    Route::get('/student/assignment', [DashboardController::class, 'assignment'])
        ->name('student.assignment');
});


/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {

    Route::get('/teacher/change-password', [DashboardController::class, 'teacherChangePassword'])
        ->name('teacher.change-password');

    Route::middleware(['check.default.password'])->group(function () {

        Route::get('/teacher/dashboard', [DashboardController::class, 'teacher'])
            ->name('teacher.dashboard');

        Route::get('/teacher/teacher-list', [DashboardController::class, 'teacherList'])
            ->name('teacher.teacher.list');

        Route::get('/teacher/class-permission', [DashboardController::class, 'classPermission'])
            ->name('teacher.class.permission');

        Route::get('/teacher/assignment', [DashboardController::class, 'teacherAssignment'])
            ->name('teacher.assignment');

        Route::get('/teacher/student-list', [DashboardController::class, 'teacherStudentList'])
            ->name('teacher.student.list');

        Route::get('/teacher/class-time', [DashboardController::class, 'teacherClassTime'])
            ->name('teacher.class.time');

    });

});
