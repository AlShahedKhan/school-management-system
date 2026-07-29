<?php

namespace App\Http\Controllers;

use App\Models\AdmissionStudent;
use App\Models\DashboardNews;
use App\Models\Expense;
use App\Models\Income;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolEmployee;
use App\Models\SchoolExamName;
use App\Models\SchoolExamRoutine;
use App\Models\SchoolExamSchedule;
use App\Models\Donate;
use App\Models\DonateCollection;
use App\Models\SchoolExpense;
use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\SchoolHoliday;
use App\Models\SchoolPayment;
use App\Models\SchoolPayroll;
use App\Models\SchoolRoutine;
use App\Models\SchoolSession;
use App\Models\SchoolStudentFee;
use App\Models\SchoolSubscription;
use App\Models\SmsPackage;
use App\Models\StudentPromotion;
use App\Models\StudentReadmission;
use App\Models\Teacher;
use App\Models\TeacherClassPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    // -----------------------------
    // Admin Dashboard & Menu Pages
    // -----------------------------

    public function admin()
    {
        // Total schools
        $totalSchools = School::count();

        // Pending schools (approval_status = pending)
        $pendingSchools = School::where('approval_status', 'pending')->count();

        // Total SMS packages
        $smsPackages = SmsPackage::count();

        return view('admin.dashboard', compact('totalSchools', 'pendingSchools', 'smsPackages'));
    }

    public function approvalSchools()
    {
        return view('admin.approval-schools');
    }

    public function registeredSchools()
    {
        return view('admin.registered-schools');
    }

    public function createPlan()
    {
        return view('admin.create-plan');
    }

    public function smsPackages()
    {
        return view('admin.sms-packages');
    }

    public function smsPackageActivationRequests()
    {
        return view('admin.sms-package-activation-requests');
    }

    public function dynamicOperation()
    {
        return redirect()->route('admin.dynamic-operation.general');
    }

    public function dynamicOperationGeneral()
    {
        return view('admin.dynamic-operation-general');
    }

    public function dynamicOperationHome()
    {
        return view('admin.dynamic-operation-home');
    }

    public function dynamicOperationTranslations()
    {
        return view('admin.dynamic-operation-translations');
    }

    public function subscriptions()
    {
        return view('admin.subscriptions');
    }

    // -----------------------------
    // School Dashboard & Menu Pages
    // -----------------------------
    public function school()
    {
        $school = School::where('user_id', Auth::id())->first();

        if (! $school) {
            return redirect()->back()->with('error', 'School profile not found.');
        }

        $schoolId = $school->user_id;
        $dashboardFilter = $this->resolveDashboardDateFilter(request());
        $dashboardFilterLabel = $dashboardFilter['label'];
        $dashboardFilterStart = $dashboardFilter['start'];
        $dashboardFilterEnd = $dashboardFilter['end'];
        $applyDashboardFilter = function ($query, string $column = 'created_at') use ($dashboardFilterStart, $dashboardFilterEnd) {
            if ($dashboardFilterStart && $dashboardFilterEnd) {
                $query->whereBetween($column, [
                    $dashboardFilterStart->copy()->startOfDay(),
                    $dashboardFilterEnd->copy()->endOfDay(),
                ]);
            }

            return $query;
        };

        // --- Existing Card Logic ---
        $classes = $applyDashboardFilter(SchoolClass::where('school_id', $school->id))->count();
        $teachersCount = $applyDashboardFilter(Teacher::where('school_id', $school->id))->count();
        $studentsCount = $applyDashboardFilter(AdmissionStudent::where('school_id', $schoolId)
            ->whereIn('status', ['Active', 'approved'])
        )->count();
        $admissionsCount = $applyDashboardFilter(AdmissionStudent::where('school_id', $schoolId)
            ->whereIn('status', ['Active', 'approved', 'Inactive'])
        )->count();
        $employeesCount = $applyDashboardFilter(Employee::where('school_id', $school->id))->count();
        $promotionsCount = $applyDashboardFilter(StudentPromotion::where('school_id', $school->id))->count();
        $totalTuitionFees = $applyDashboardFilter(SchoolStudentFee::where('school_id', $school->id)
            ->where('fee_type_name', 'Tuition'))->sum('payable_amount');
        $totalFoodFees = $applyDashboardFilter(
            SchoolStudentFee::where('school_id', $school->id)
                ->where('fee_type_name', 'Food')
        )->sum('payable_amount');
        $totalFineFees = $applyDashboardFilter(
            SchoolStudentFee::where('school_id', $school->id)
                ->where('fee_type_name', 'Fine')
        )->sum('payable_amount');
        $sessionsCount = $applyDashboardFilter(
            SchoolSession::where('school_id', $school->id)
        )->count();
        $examsCount = $applyDashboardFilter(
            SchoolExamName::where('school_id', $school->id)
        )->count();
        $totalDue = $applyDashboardFilter(
            SchoolStudentFee::where('school_id', $school->id)
        )->sum('due_amount');
        $overdueAmount = $applyDashboardFilter(
            SchoolStudentFee::query()
                ->where('school_id', $school->id)
                ->whereDate('due_date', '<', now())
                ->whereIn('status', ['pending', 'due', 'partial_paid', 'due_partial', 'over_due', 'over_due_partial'])
        )->sum('due_amount');
        $totalExpense = $applyDashboardFilter(
            SchoolExpense::where('school_id', $school->id),
            'expense_date'
        )->sum('amount');
        $totalCash = $applyDashboardFilter(
            SchoolPayment::where('school_id', $school->id)
                ->where('pay_method', 'Cash'),
            'pay_date'
        )->sum('type_amount');
        $totalBank = $applyDashboardFilter(
            SchoolPayment::where('school_id', $school->id)
                ->where('pay_method', 'Bank'),
            'pay_date'
        )->sum('type_amount');
        // dd($schoolId);
        // Modified on 2026-07-07: Count 'Active' or 'approved' students as active
        $studentsActive = AdmissionStudent::where('school_id', $schoolId)->whereIn('status', ['Active', 'approved'])->count();
        $studentsInactive = AdmissionStudent::where('school_id', $schoolId)->where('status', 'Inactive')->count();

        $totalFees = $applyDashboardFilter(SchoolStudentFee::where('school_id', $school->id))->sum('payable_amount');

        $totalCollection = $applyDashboardFilter(SchoolPayment::where('school_id', $school->id))->sum('type_amount');
        $totalPayroll = $applyDashboardFilter(
            SchoolPayroll::where('school_id', $school->id)
        )->sum('paid_amount');
        $totalIncome = Income::where('school_id', $school->id)->sum('amount');
        $totalExpense = Expense::where('school_id', $school->id)->sum('amount');

        // --- Dynamic Chart Logic (Fixed to start from January) ---
        $months = collect();
        $monthlyPayments = collect();
        $monthlyDues = collect();
        $monthlyIncomes = collect();
        $monthlyExpenses = collect();

        $currentMonthNumber = now()->month;

        for ($m = 1; $m <= $currentMonthNumber; $m++) {
            $date = Carbon::create(now()->year, $m, 1);
            $months->push($date->format('M'));

            // Payments vs Due
            $monthlyPayments->push(SchoolPayment::where('school_id', $schoolId)
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('type_amount'));

            $monthlyDues->push(SchoolPayment::where('school_id', $schoolId)
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('payable_due'));

            // Income vs Expense Trend
            $monthlyIncomes->push(Income::where('school_id', $schoolId)
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('amount'));

            $monthlyExpenses->push(Expense::where('school_id', $schoolId)
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('amount'));
        }

        // Profit vs Loss Calculation
        $net = $totalIncome + $totalExpense;
        $profitPercent = $net > 0 ? round(($totalIncome / $net) * 100) : 50;
        $lossPercent = $net > 0 ? round(($totalExpense / $net) * 100) : 50;

        // Bank vs Cash Calculation
        $bankTotal = SchoolPayment::where('school_id', $schoolId)->where('pay_method', 'Bank')->sum('type_amount');
        $cashTotal = SchoolPayment::where('school_id', $schoolId)->where('pay_method', 'Cash')->sum('type_amount');

        // --- Table Variables ---
        $subscription = SchoolSubscription::where('school_id', $schoolId)->with('package')->where('status', 'active')->latest()->first();
        $teachersList = Teacher::where('school_id', $schoolId)->latest()->take(5)->get();
        $topClasses = SchoolExamSchedule::where('school_id', $schoolId)->latest()->take(5)->get();
        $dueList = SchoolPayment::where('school_id', $schoolId)->with(['student.schoolClass'])->where('total_due', '>', 0)->latest()->take(5)->get();
        $upcomingExams = SchoolExamRoutine::where('school_id', $schoolId)->where('exam_date', '>=', now()->toDateString())->orderBy('exam_date', 'asc')->take(5)->get();

        $hour = now()->hour;
        $greeting = match (true) {
            $hour < 12 => 'Good Morning',
            $hour < 17 => 'Good Afternoon',
            $hour < 21 => 'Good Evening',
            default => 'Good Night',
        };

        $activeDashboardNews = Cache::remember('dashboard_news.active', now()->addMinutes(10), function () {
            return DashboardNews::query()
                ->visible()
                ->displayOrder()
                ->pluck('message')
                ->values()
                ->all();
        });

        $dashboardNews = $activeDashboardNews !== []
            ? [
                'label' => 'News',
                'message' => implode('     |     ', $activeDashboardNews),
            ]
            : [
                'label' => 'News',
                'message' => 'No news available',
            ];

        $dashboardNewsItems = $activeDashboardNews !== []
            ? $activeDashboardNews
            : [$dashboardNews['message']];

        $promotedStudentIds = StudentPromotion::query()
            ->where('school_id', $schoolId)
            ->pluck('student_id')
            ->flip()
            ->all();

        $readmittedStudentIds = StudentReadmission::query()
            ->where('school_id', $schoolId)
            ->pluck('student_id')
            ->flip()
            ->all();

        $recentAdmissions = $applyDashboardFilter(
            AdmissionStudent::query()
                ->with(['schoolClass', 'schoolSection', 'schoolGroup', 'schoolSession'])
                ->where('school_id', $schoolId),
            'admission_date'
        )
            ->latest('admission_date')
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function (AdmissionStudent $student) use ($promotedStudentIds, $readmittedStudentIds): array {
                $admissionDate = $student->admission_date
                    ? Carbon::parse($student->admission_date)
                    : $student->created_at;

                return [
                    'photo' => $student->image
                        ? asset('storage/'.$student->image)
                        : 'https://ui-avatars.com/api/?name='.urlencode($student->student_name),
                    'name' => $student->student_name,
                    'student_id' => $student->student_id_number ?: ($student->admission_id ?: '-'),
                    'father_name' => $student->father_name ?: '-',
                    'class' => $student->schoolClass?->class_name ?? $student->class ?: '-',
                    'group' => $student->schoolGroup?->group_name ?? $student->group ?: '-',
                    'section' => $student->schoolSection?->section_name ?? $student->section ?: '-',
                    'session' => $student->schoolSession?->session_year ?? $student->session ?: '-',
                    'mobile' => $student->mobile ?: '-',
                    'student_type' => match (true) {
                        isset($promotedStudentIds[$student->id]) => 'Promote',
                        isset($readmittedStudentIds[$student->id]) => 'Re-Admission',
                        $student->admission_fee === 'N/A' => 'Bulk Upload',
                        default => 'Admission',
                    },
                    'status' => match ($student->status) {
                        'Inactive' => 'Unactive',
                        'pending' => 'Pending',
                        default => $student->status ?: 'Active',
                    },
                    'status_class' => match ($student->status) {
                        'Inactive' => 'bg-red-100 text-red-700',
                        'pending' => 'bg-amber-100 text-amber-700',
                        default => 'bg-green-100 text-green-700',
                    },
                    'date' => $admissionDate?->format('d M Y') ?? '',
                    'datetime' => $admissionDate?->toDateString() ?? '',
                ];
            })
            ->all();

        $recentPromotions = $applyDashboardFilter(
            StudentPromotion::query()
                ->with([
                    'student:id,student_name,image',
                    'fromClass:id,class_name',
                    'toClass:id,class_name',
                    'toSection:id,section_name',
                ])
                ->where('school_id', $school->id),
            'promote_date'
        )
            ->latest('promote_date')
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function (StudentPromotion $promotion): array {
                $promotionDate = $promotion->promote_date
                    ? Carbon::parse($promotion->promote_date)
                    : $promotion->created_at;
                $studentName = $promotion->student?->student_name ?: 'Unknown student';

                return [
                    'photo' => $promotion->student?->image
                        ? asset('storage/'.$promotion->student->image)
                        : 'https://ui-avatars.com/api/?name='.urlencode($studentName),
                    'name' => $studentName,
                    'student_id' => $promotion->to_student_id_number ?: $promotion->from_student_id_number ?: '-',
                    'from_class' => $promotion->fromClass?->class_name ?: '-',
                    'to_class' => $promotion->toClass?->class_name ?: '-',
                    'section' => $promotion->toSection?->section_name ?: '-',
                    'date' => $promotionDate?->format('d M Y') ?? '',
                    'datetime' => $promotionDate?->toDateString() ?? '',
                ];
            })
            ->all();

        $recentPayments = $applyDashboardFilter(
            SchoolPayment::query()
                ->with('student:id,student_name,student_id_number,admission_id,image')
                ->where('school_id', $school->id),
            'pay_date'
        )
            ->latest('pay_date')
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function (SchoolPayment $payment): array {
                $paymentDate = $payment->pay_date
                    ? Carbon::parse($payment->pay_date)
                    : $payment->created_at;
                $studentName = $payment->student?->student_name ?: 'Unknown student';
                $feeName = collect([$payment->fees_type, $payment->fee_name])
                    ->filter()
                    ->implode(' - ') ?: '-';

                return [
                    'photo' => $payment->student?->image
                        ? asset('storage/'.$payment->student->image)
                        : 'https://ui-avatars.com/api/?name='.urlencode($studentName),
                    'name' => $studentName,
                    'student_id' => $payment->student?->student_id_number ?: ($payment->student?->admission_id ?: '-'),
                    'fee' => $feeName,
                    'method' => $payment->pay_method ?: '-',
                    'amount' => number_format((float) $payment->type_amount, 2),
                    'date' => $paymentDate?->format('d M Y') ?? '',
                    'datetime' => $paymentDate?->toDateString() ?? '',
                ];
            })
            ->all();

        $recentDues = $applyDashboardFilter(
            SchoolStudentFee::query()
                ->with('student:id,student_name,student_id_number,admission_id,image')
                ->where('school_id', $school->id)
                ->where('due_amount', '>', 0),
            'due_date'
        )
            ->latest('due_date')
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function (SchoolStudentFee $fee): array {
                $dueDate = $fee->due_date ?: $fee->pay_date ?: $fee->created_at;
                $studentName = $fee->student?->student_name ?: 'Unknown student';
                $feeName = collect([$fee->fee_type_name, $fee->fee_name])
                    ->filter()
                    ->implode(' - ') ?: '-';

                return [
                    'photo' => $fee->student?->image
                        ? asset('storage/'.$fee->student->image)
                        : 'https://ui-avatars.com/api/?name='.urlencode($studentName),
                    'name' => $studentName,
                    'student_id' => $fee->student?->student_id_number ?: ($fee->student?->admission_id ?: '-'),
                    'fee' => $feeName,
                    'amount' => number_format((float) $fee->due_amount, 2),
                    'date' => $dueDate?->format('d M Y') ?? '',
                    'datetime' => $dueDate?->toDateString() ?? '',
                ];
            })
            ->all();

        $recentOverdues = $applyDashboardFilter(
            SchoolStudentFee::query()
                ->with('student:id,student_name,student_id_number,admission_id,image')
                ->where('school_id', $school->id)
                ->where('due_amount', '>', 0)
                ->whereDate('due_date', '<', now())
                ->whereIn('status', ['pending', 'due', 'partial_paid', 'due_partial', 'over_due', 'over_due_partial']),
            'due_date'
        )
            ->latest('due_date')
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function (SchoolStudentFee $fee): array {
                $dueDate = $fee->due_date ?: $fee->pay_date ?: $fee->created_at;
                $studentName = $fee->student?->student_name ?: 'Unknown student';
                $feeName = collect([$fee->fee_type_name, $fee->fee_name])
                    ->filter()
                    ->implode(' - ') ?: '-';

                return [
                    'photo' => $fee->student?->image
                        ? asset('storage/'.$fee->student->image)
                        : 'https://ui-avatars.com/api/?name='.urlencode($studentName),
                    'name' => $studentName,
                    'student_id' => $fee->student?->student_id_number ?: ($fee->student?->admission_id ?: '-'),
                    'fee' => $feeName,
                    'amount' => number_format((float) $fee->due_amount, 2),
                    'date' => $dueDate?->format('d M Y') ?? '',
                    'datetime' => $dueDate?->toDateString() ?? '',
                ];
            })
            ->all();

        $recentLeaves = $applyDashboardFilter(
            SchoolPayroll::query()
                ->where('school_id', $school->id)
                ->where('leave', '>', 0)
        )
            ->latest('created_at')
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function (SchoolPayroll $payroll): array {
                $recordedAt = $payroll->created_at;
                $employeeName = $payroll->employee_name ?: 'Unknown employee';

                return [
                    'photo' => 'https://ui-avatars.com/api/?name='.urlencode($employeeName),
                    'name' => $employeeName,
                    'designation' => $payroll->designation ?: '-',
                    'period' => trim($payroll->month.' '.$payroll->year) ?: '-',
                    'days' => (int) $payroll->leave,
                    'date' => $recordedAt?->format('d M Y') ?? '',
                    'datetime' => $recordedAt?->toDateString() ?? '',
                ];
            })
            ->all();

        $recentHolidays = $applyDashboardFilter(
            SchoolHoliday::query()->where('school_id', $school->id),
            'start_date'
        )
            ->latest('start_date')
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function (SchoolHoliday $holiday): array {
                $startDate = Carbon::parse($holiday->start_date);
                $endDate = Carbon::parse($holiday->end_date);
                $target = $holiday->type === 'Class Wise'
                    ? collect([$holiday->class_name, $holiday->group_name, $holiday->section_name])->filter()->implode(' - ')
                    : 'All school';

                return [
                    'type' => $holiday->type,
                    'reason' => $holiday->reason,
                    'target' => $target ?: '-',
                    'start_date' => $startDate->format('d M Y'),
                    'end_date' => $endDate->format('d M Y'),
                    'days' => (int) $holiday->total_days,
                    'datetime' => $startDate->toDateString(),
                ];
            })
            ->all();

        return view('school.dashboard', compact(
            'classes',
            'teachersCount',
            'studentsCount',
            'admissionsCount',
            'employeesCount',
            'promotionsCount',
            'totalTuitionFees',
            'totalFoodFees',
            'totalFineFees',
            'sessionsCount',
            'examsCount',
            'totalCash',
            'totalDue',
            'overdueAmount',
            'totalBank',
            'totalExpense',
            'studentsActive',
            'studentsInactive',
            'totalFees',
            'totalCollection',
            'totalPayroll',
            'totalIncome',
            'totalExpense',
            'subscription',
            'teachersList',
            'topClasses',
            'dueList',
            'upcomingExams',
            'months',
            'monthlyPayments',
            'monthlyDues',
            'monthlyIncomes',
            'monthlyExpenses',
            'profitPercent',
            'lossPercent',
            'bankTotal',
            'cashTotal',
            'greeting',
            'dashboardNews',
            'dashboardNewsItems',
            'recentAdmissions',
            'recentPromotions',
            'recentPayments',
            'recentDues',
            'recentOverdues',
            'recentLeaves',
            'recentHolidays',
            'dashboardFilterLabel',
            'dashboardFilterStart',
            'dashboardFilterEnd'
        ));
    }

    private function resolveDashboardDateFilter(Request $request): array
    {
        $filter = $request->query('filter');
        $today = now();

        return match ($filter) {
            'today' => [
                'label' => 'Today',
                'start' => $today->copy()->startOfDay(),
                'end' => $today->copy()->endOfDay(),
            ],
            'last_7_days' => [
                'label' => 'Last 7 Days',
                'start' => $today->copy()->subDays(6)->startOfDay(),
                'end' => $today->copy()->endOfDay(),
            ],
            'this_month' => [
                'label' => 'This Month',
                'start' => $today->copy()->startOfMonth(),
                'end' => $today->copy()->endOfMonth(),
            ],
            'this_year' => [
                'label' => 'This Year',
                'start' => $today->copy()->startOfYear(),
                'end' => $today->copy()->endOfYear(),
            ],
            'custom' => $this->resolveCustomDashboardDateFilter($request),
            default => [
                'label' => 'Filter',
                'start' => null,
                'end' => null,
            ],
        };
    }

    private function resolveCustomDashboardDateFilter(Request $request): array
    {
        try {
            $start = $request->filled('start_date')
                ? Carbon::parse($request->query('start_date'))->startOfDay()
                : null;
            $end = $request->filled('end_date')
                ? Carbon::parse($request->query('end_date'))->endOfDay()
                : null;
        } catch (\Throwable) {
            $start = null;
            $end = null;
        }

        if (! $start || ! $end || $start->gt($end)) {
            return [
                'label' => 'Filter',
                'start' => null,
                'end' => null,
            ];
        }

        return [
            'label' => 'Custom',
            'start' => $start,
            'end' => $end,
        ];
    }

    // Teacher Pages
    public function teacherRegistration(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $designation = trim((string) $request->query('designation', ''));
        $teacherId = trim((string) $request->query('teacher_id', ''));

        $school = School::where('user_id', Auth::id())->first();
        if (! $school) {
            return redirect()->back()->with('error', 'School profile not found.');
        }

        $schoolId = $school->id;

        $teachers = Teacher::query()
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($teacherQuery) use ($search) {
                    $teacherQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%")
                        ->orWhere('id_number', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($teacherId !== '', fn ($query) => $query->whereKey($teacherId))
            ->when($designation !== '', fn ($query) => $query->where('designation', $designation))
            ->paginate(30)
            ->withQueryString();

        $teacherFilterOptions = Teacher::query()
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->orderBy('name')
            ->get(['id', 'name', 'designation'])
            ->map(fn (Teacher $teacher) => [
                'value' => (string) $teacher->id,
                'label' => $teacher->name,
                'designation' => $teacher->designation,
            ])
            ->values()
            ->all();

        return view('school.teacher.index', compact('teachers', 'teacherFilterOptions'));
    }

    public function SchoolclassPermission()
    {
        return view('school.class-permission');
    }

    // Student Pages
    public function studentAdmission()
    {
        return view('school.student-admission');
    }

    public function studentLists()
    {
        return view('school.student.index');
    }

    // Modified on 2026-07-07: Added studentPromote and studentPromoteHistory methods
    public function studentPromote()
    {
        return view('school.student-promote');
    }

    public function studentPromoteHistory()
    {
        return view('school.student-promote-history');
    }

    // Modified on 2026-07-09: Render Student Bulk Upload view
    public function studentBulkUpload()
    {
        return view('school.student-bulk-upload');
    }

    // Modified on 2026-07-09: Render Student Re-Admission view
    public function studentReAdmission()
    {
        return view('school.student-re-admission');
    }

    public function smsSettings()
    {
        return view('school.sms-settings');
    }

    // Academic Pages
    public function classes()
    {
        return view('school.academic.class.index');
    }

    public function groups()
    {
        return view('school.academic.group.index');
    }

    public function sections()
    {
        return view('school.academic.section.index');
    }

    public function sessions()
    {
        return view('school.academic.session.index');
    }

    public function subjects()
    {
        return view('school.academic.subject.index');
    }

    public function syllabus()
    {
        return view('school.academic.syllabus.index');
    }

    public function classRoutine()
    {
        return view('school.academic.routine.index');
    }

    // Guardian
    public function guardians()
    {
        return view('school.guardians');
    }

    // Finance Pages
    public function income()
    {
        return view('school.income');
    }

    public function membership()
    {
        return view('school.membership');
    }

    public function donate(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $month = trim((string) $request->query('month', ''));
        $year = trim((string) $request->query('year', ''));
        $school = School::where('user_id', Auth::id())->first();
        if (! $school) {
            return redirect()->back()->with('error', 'School profile not found.');
        }
        $schoolId = $school->id;
        $donates = Donate::query()
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($donateQuery) use ($search) {
                    $donateQuery
                        ->where('donate_no', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('mobile_number', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('donate_reason', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%");
                });
            })
            ->when($month !== '', fn ($query) => $query->whereMonth('created_at', $month))
            ->when($year !== '', fn ($query) => $query->whereYear('created_at', $year))
            ->orderBy('donate_no', 'asc')
            ->paginate(30)
            ->withQueryString();
        return view('school.finance.donate.index', compact('donates'));
    }

    public function collection(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $month = trim((string) $request->query('month', ''));
        $year = trim((string) $request->query('year', ''));
        $school = School::where('user_id', Auth::id())->first();
        if (! $school) {
            return redirect()->back()->with('error', 'School profile not found.');
        }
        $schoolId = $school->id;
        $collections = DonateCollection::query()
            ->with('donate')
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('donate', function ($donateQuery) use ($search) {
                    $donateQuery
                        ->where('donate_no', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('mobile_number', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('donate_reason', 'like', "%{$search}%");
                });
            })
            ->when($month !== '', fn ($query) => $query->where('receive_month', $month))
            ->when($year !== '', fn ($query) => $query->where('receive_year', $year))
            ->orderBy('id', 'asc')
            ->paginate(30)
            ->withQueryString();
        return view('school.finance.collection.index', compact('collections'));
    }
    
    public function expense(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $month = trim((string) $request->query('month', ''));
        $year = trim((string) $request->query('year', ''));
        $school = School::where('user_id', Auth::id())->first();
        if (!$school) {
            return redirect()->back()->with('error', 'School profile not found.');
        }
        $schoolId = $school->id;
        $expenses = Expense::query()
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($expenseQuery) use ($search) {
                    $expenseQuery
                        ->where('expense_reason', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%");
                });
            })
            ->when($month !== '', fn ($query) => $query->where('month', $month))
            ->when($year !== '', fn ($query) => $query->where('year', $year))
            ->orderBy('date', 'desc')
            ->paginate(30)
            ->withQueryString();

        return view('school.finance.expense.index', compact('expenses'));
    }

    public function employee(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $month = trim((string) $request->query('month', ''));
        $year = trim((string) $request->query('year', ''));
        $school = School::where('user_id', Auth::id())->first();
        if (! $school) {
            return redirect()->back()->with('error', 'School profile not found.');
        }
        $schoolId = $school->id;
        $employees = Employee::query()
            ->withSum([
                'payrolls as paid_amount' => function ($query) use ($month, $year) {
                    if ($month !== '') {
                        $query->where('receive_month', $month);
                    }

                    if ($year !== '') {
                        $query->where('receive_year', $year);
                    }
                },
            ], 'receive_amount')
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($employeeQuery) use ($search) {
                    $employeeQuery
                        ->where('employee_no', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('mobile_number', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            })
            ->when($month !== '' || $year !== '', function ($query) use ($month, $year) {
                $query->where(function ($employeeQuery) use ($month, $year) {
                    $employeeQuery->whereDate('salary_start_date', '<=', now());
                    if ($month !== '') {
                        $employeeQuery->whereMonth('salary_start_date', '<=', $month);
                    }
                    if ($year !== '') {
                        $employeeQuery->whereYear('salary_start_date', '<=', $year);
                    }
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(30)
            ->withQueryString();
        return view('school.hrm.employee.index', compact('employees'));
    }

    public function payroll(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $month = trim((string) $request->query('month', ''));
        $year = trim((string) $request->query('year', ''));
        $school = School::where('user_id', Auth::id())->first();
        if (! $school) {
            return redirect()->back()->with('error', 'School profile not found.');
        }
        $schoolId = $school->id;
        $payrolls = EmployeePayroll::query()
            ->with('employee')
            ->when($schoolId, fn ($query) => $query->where('school_id', $schoolId))
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery
                        ->where('employee_no', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('mobile_number', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            })
            ->when($month !== '', fn ($query) => $query->where('receive_month', $month))
            ->when($year !== '', fn ($query) => $query->where('receive_year', $year))
            ->orderBy('id', 'asc')
            ->paginate(30)
            ->withQueryString();
        return view('school.hrm.payroll.index', compact('payrolls'));
    }
    

    // Subscription / Plans
    public function currentPlan()
    {
        return view('school.current-plan');
    }

    public function smsPackage()
    {
        return view('school.sms-package');
    }

    // Fees Pages
    public function feesType()
    {
        return view('school.fees.fees_type.index');
    }

    public function discount()
    {
        return view('school.fees.discount.index');
    }

    public function discountsNew()
    {
        return view('school.fees.discount.index');
    }

    public function payment()
    {
        return view('school.fees.collection.index');
    }

    public function dueList()
    {
        return view('school.fees.due-collection.index');
    }

    public function studentFees()
    {
        return view('school.fees.student-fees.index');
    }

    public function paymentSlip(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:admission_students,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $school = School::where('user_id', Auth::id())->firstOrFail();

        $student = AdmissionStudent::with([
            'schoolClass',
            'schoolGroup',
            'schoolSection',
            'schoolSession',
        ])->findOrFail($request->student_id);

        $payments = SchoolPayment::where('school_id', $school->id)
            ->where('admission_student_id', $request->student_id)
            ->when($request->from_date, fn ($q) => $q->whereDate('pay_date', '>=', $request->from_date))
            ->when($request->to_date, fn ($q) => $q->whereDate('pay_date', '<=', $request->to_date))
            ->orderBy('pay_date', 'asc')
            ->get();

        $duration = ($request->from_date ? \Carbon\Carbon::parse($request->from_date)->format('j-F-Y') : 'N/A')
            .' To '.($request->to_date ? \Carbon\Carbon::parse($request->to_date)->format('j-F-Y') : 'N/A');

        $grandTotal = $payments
            ->groupBy(fn ($p) => $p->fees_type.'||'.$p->fee_name)
            ->sum(fn ($group) => (float) $group->first()->total_payable);

        $grandPaid = $payments->sum(fn ($p) => (float) $p->type_amount);
        $grandDue = max($grandTotal - $grandPaid, 0);

        // Map fee due dates from school_student_fees for "Pay Date" column
        $feeDueDates = [];
        if ($payments->isNotEmpty()) {
            $studentFees = SchoolStudentFee::where('school_id', $school->id)
                ->where('student_id', $request->student_id)
                ->whereIn('fee_type_name', $payments->pluck('fees_type')->unique())
                ->select('fee_type_name', 'fee_name', 'pay_date')
                ->get();
            foreach ($studentFees as $sf) {
                $feeDueDates[$sf->fee_type_name.'||'.$sf->fee_name] = $sf->pay_date;
            }
        }

        return view('school.payment_slip', compact(
            'school',
            'student',
            'payments',
            'duration',
            'grandTotal',
            'grandPaid',
            'grandDue',
            'feeDueDates',
        ));
    }

    // Notice Pages
    public function announcement()
    {
        return view('school.announcement');
    }

    public function createHoliday()
    {
        return view('school.create_holiday');
    }

    // ================= Exam =================
    public function examName()
    {
        return view('school.exam.exam_name.index');
    }

    public function examRoutine()
    {
        return view('school.exam.exam_routine');
    }

    public function grade()
    {
        return view('school.exam.grade.index');
    }

    public function admitCard()
    {
        return view('school.exam.admit_card');
    }

    public function seatPlan()
    {
        return view('school.exam.seat_plan');
    }

    public function markSubmit()
    {
        return view('school.exam.mark_submit');
    }

    public function schedule()
    {
        return view('school.exam.schedule');
    }

    public function resultFind()
    {
        return view('school.exam.result_find');
    }

    // ================= Inventory =================

    public function product()
    {
        return view('school.inventory.product');
    }

    public function purchase()
    {
        return view('school.inventory.purchase');
    }

    public function return()
    {
        return view('school.inventory.return');
    }

    public function duePaid()
    {
        return view('school.inventory.due_paid');
    }

    public function profitLoss()
    {
        return view('school.inventory.profit_loss');
    }

    public function addPayment()
    {
        return view('school.inventory.add_payment');
    }

    // ================= Role =================

    public function rolePermission()
    {
        return view('school.role.role_permission');
    }

    // -----------------------------
    // Teacher Dashboard & Menu Pages
    // -----------------------------

    public function teacher()
    {
        $user = Auth::user();
        $teacher = Teacher::where('id_number', $user->id_number)->first();

        $students = 0;
        $assignments = 0;
        $classes = 0;

        if ($teacher) {
            // Count distinct assigned classes
            $classes = TeacherClassPermission::where('teacher_id', $teacher->id)
                ->distinct('class_id')
                ->count('class_id');

            // Count total unique students under permitted classes/groups/sections
            $permissions = TeacherClassPermission::where('teacher_id', $teacher->id)->get();
            if ($permissions->isNotEmpty()) {
                $studentQuery = AdmissionStudent::where('school_id', $teacher->school_id)
                    ->whereIn('status', ['Active', 'approved']);

                $studentQuery->where(function ($q) use ($permissions) {
                    foreach ($permissions as $permission) {
                        $q->orWhere(function ($sq) use ($permission) {
                            $sq->where('class', $permission->class_id);
                            if ($permission->group_id) {
                                $sq->where('group', $permission->group_id);
                            }
                            if ($permission->section_id) {
                                $sq->where('section', $permission->section_id);
                            }
                        });
                    }
                });
                $students = $studentQuery->count();
            }

            // Count total routine schedules as assignments
            $assignments = SchoolRoutine::where('teacher_id', $teacher->id)->count();
        }

        // Modified on 2026-07-11: Dynamically calculate teacher statistics based on class permissions
        return view('teacher.dashboard', compact(
            'students',
            'assignments',
            'classes'
        ));
    }

    public function teacherList()
    {
        return view('teacher.teacher-list');
    }

    public function teacherChangePassword()
    {

        return view('teacher.change-password');

    }

    public function classPermission()
    {
        return view('teacher.class-permission');
    }

    public function teacherAssignment()
    {
        return view('teacher.assignment');
    }

    public function teacherStudentList()
    {
        return view('teacher.student-list');
    }

    public function teacherClassTime()
    {
        return view('teacher.class-time');
    }

    // -----------------------------
    // Student Dashboard & Menu Pages
    // -----------------------------

    public function student()
    {
        $subjects = 6;
        $attendance = 95;

        return view('student.dashboard', compact('subjects', 'attendance'));
    }

    public function studentTeacherList()
    {
        return view('student.teacher-list');
    }

    public function studentList()
    {
        return view('student.student-list');
    }

    public function classTime()
    {
        return view('student.class-time');
    }

    public function classPromote()
    {
        return view('student.class-promote');
    }

    public function assignment()
    {
        return view('student.assignment');
    }

    public function underConstruction(Request $request)
    {
        $developmentNotice = $request->route('development_notice')
            ? [
                'module' => $request->route('development_module', 'This module'),
                'completion' => '10 August 2026, 12:00 AM (Midnight)',
            ]
            : null;

        return view('upcoming.under_construction', compact('developmentNotice'));
    }
}
