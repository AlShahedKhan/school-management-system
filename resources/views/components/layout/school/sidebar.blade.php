@props([
    'schoolLogo' => null,
])

<x-layout.sidebar :logo="$schoolLogo" logo-alt="Astha Academy" logo-id="sideSchoolLogo">
    <a href="{{ route('school.dashboard') }}" data-title="Dashboard" data-link class="sidebar-item">
        <i class="hgi hgi-stroke hgi-rounded hgi-dashboard-browsing w-4"></i>
        Dashboard
    </a>
    {{-- Fingerprint Attendance --}}
            <div class="sidebar-group {{ request()->routeIs('school.fingerprint-attendance.*') ? 'open' : '' }}">

                <div class="sidebar-group-toggle">

                    <span>
                        <i class="fas fa-fingerprint"></i>
                        Fingerprint Attendance
                    </span>

                    <i class="fas fa-chevron-right text-xs"></i>

         </div>
                <div class="sidebar-group-content">

                    {{-- Teacher --}}
                    <a href="{{ route('school.fingerprint-attendance.teacher') }}"
                    class="sidebar-subitem {{ request()->routeIs('school.fingerprint-attendance.teacher') ? 'active' : '' }}">

                        <i class="fas fa-chalkboard-teacher"></i>
                        Teacher Attendance
                    </a>
                    {{-- Employee --}}
                    <a href="{{ route('school.fingerprint-attendance.employee') }}"
                    class="sidebar-subitem {{ request()->routeIs('school.fingerprint-attendance.employee') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        Employee Attendance
                    </a>
                    {{-- Student --}}
                    <a href="{{ route('school.fingerprint-attendance.student') }}"
                    class="sidebar-subitem {{ request()->routeIs('school.fingerprint-attendance.student') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate"></i>
                        Student Attendance
                    </a>
                </div>
            </div>

    {{-- <p class="nav-header">Teacher Management</p> --}}

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-teacher w-4"></i>
                Teacher List
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">

            <a href="{{ route('school.teacher-registration') }}" data-title="Teacher Registration" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-user-add-01"></i>
                Teacher
            </a>

            <a href="{{ route('school.class-permission') }}" data-title="Class Permission" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-lock-password"></i>
                Permission
            </a>

            <a href="{{ route('school.teacher-id-card') }}" data-title="Teacher ID Card" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-identity-card"></i>
                Id Card
            </a>

            <a href="{{ route('school.teacher-attendance') }}" data-title="Teacher Attendance" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-calendar-check-in-01"></i>
                Attendance
            </a>

        </div>
    </div>


    <!-- ================= Student Management ================= -->
    {{-- <p class="nav-header">Student Management</p> --}}

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-student w-4"></i>
                Student List
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>
        <div class="sidebar-group-content">
            <a href="{{ route('school.student-admission') }}" data-title="Student Admission" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-user-group"></i>
                Admission
            </a>

            <!-- Added on 2026-07-09: Dedicated Bulk Upload submenu link -->
            <a href="{{ route('school.student-bulk-upload') }}" data-title="Bulk Upload" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-file-import"></i>
                Bulk Upload
            </a>

            <!-- Added on 2026-07-09: Dedicated Re-Admission submenu link -->
            <a href="{{ route('school.student-re-admission') }}" data-title="Re-Admission" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-user-add-01"></i>
                Re-Admission
            </a>

            <a href="{{ route('school.student-promote') }}" data-title="Promote Students" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-graduation-scroll"></i>
                Promote
            </a>

            <a href="{{ route('school.students') }}" data-title="Student List" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-user-group"></i>
                Student List
            </a>

            <a href="{{ route('school.student-id-card') }}" data-title="Student ID Card" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-identity-card"></i>
                ID Card
            </a>

            <a href="{{ route('school.student-attendance') }}" data-title="Student Attendance" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-calendar-check-in-01"></i>
                Attendance
            </a>

            {{--
            <a href="{{ route('school.student-promote-request') }}" data-title="Promote Request" data-link
            class="sidebar-subitem">
            <i class="hgi hgi-stroke hgi-rounded hgi-document-validation"></i>
            Promote Request
            </a>

            <a href="{{ route('school.student-promote-history') }}" data-title="Promote History" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-file-02"></i>
                Promote History
            </a>

            <a href="{{ route('school.student-class-time') }}" data-title="Class Time" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-clock-01"></i>
                Class Time
            </a>

            <a href="{{ route('school.guardians') }}" data-title="Guardian Lists" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-user-account"></i>
                Guardian
            </a>
            --}}

        </div>
    </div>


    <!-- ================= Class & Academics Management ================= -->
    {{-- <p class="nav-header">Class & Academics</p> --}}

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-book-open-01 w-4"></i>
                Academic List
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">

            <a href="{{ route('school.classes') }}" data-title="Class" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-layers-01"></i>
                Class
            </a>

            <a href="{{ route('school.groups') }}" data-title="Group" data-link class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-grid-view"></i>
                Group
            </a>

            <a href="{{ route('school.sections') }}" data-title="Section" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-grid-table"></i>

                Section
            </a>

            <a href="{{ route('school.sessions') }}" data-title="Session" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-calendar-03"></i>
                Session
            </a>

            <a href="{{ route('school.subjects') }}" data-title="Subject" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-book-02"></i>
                Subject
            </a>

            <a href="{{ route('school.syllabus') }}" data-title="Syllabus" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-scroll"></i>
                Syllabus
            </a>

            <a href="{{ route('school.class-routine') }}" data-title="Class Routine" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-calendar-03"></i>
                Class Routine
            </a>

        </div>
    </div>



    <!-- ================= Exam Management ================= -->
    {{-- <p class="nav-header">Exam Management</p> --}}

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-file-02"></i>
                Examination List
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">

            <!-- Exam Name -->
            <a href="{{ route('school.exam-name') }}" data-title="Exam Name" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-file-01"></i>
                Exam Name
            </a>

            <!-- Exam Routine -->
            <a href="{{ route('school.exam-routine') }}" data-title="Exam Routine" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-calendar-03"></i>
                Exam Routine
            </a>

            <!-- Grade -->
            <a href="{{ route('school.grade') }}" data-title="Grade" data-link class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-award-01"></i>
                Grade
            </a>

            <!-- Admit Card -->
            <a href="{{ route('school.admit-card') }}" data-title="Admit Card" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-file-02"></i>
                Admid Card
            </a>

            <!-- Seat Plan -->
            <a href="{{ route('school.seat-plan') }}" data-title="Seat Number" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-chair-01"></i>
                Set Number
            </a>

            <!-- Mark Entry -->
            <a href="{{ route('school.mark-submit') }}" data-title="Mark Entry" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-edit-01"></i>
                Subject Mark
            </a>

            <!-- Schedule -->
            <a href="{{ route('school.schedule') }}" data-title="Schedule" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-clock-01"></i>
                Schedule
            </a>

            <!-- Result Find -->
            <a href="{{ route('school.result-find') }}" data-title="Result Find" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-search-01"></i>
                Result Find
            </a>

            <a href="{{ route('school.merit-list') }}" data-title="Merit List" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-ranking"></i>
                Merit List
            </a>

            <a href="{{ route('school.fail-list') }}" data-title="Fail List" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-cancel-circle"></i>
                Fail List
            </a>

            <!-- Certificate -->
            <a href="{{ route('school.certificate') }}" data-title="Certificate" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-certificate-01"></i>
                Certificate
            </a>

        </div>
    </div>



    <!-- ================= Fees ================= -->
    {{-- <p class="nav-header">Fees Management</p> --}}

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-wallet-02"></i>
                Fee Management
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">

            <a href="{{ route('school.fees-type') }}" data-title="Fee Template" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-tag-01"></i>
                Fee Temple
            </a>

            <a href="{{ route('school.student-fees') }}" data-title="Student Fees" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-user-group"></i>
                Student Fee
            </a>

            <a href="{{ route('school.discounts') }}" data-title="Discount" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-percent"></i>
                Discount
            </a>

            <a href="{{ route('school.payment') }}" data-title="Payment" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-credit-card"></i>
                Fee Collection
            </a>

            <a href="{{ route('school.due-list') }}" data-title="Due List" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-alert-circle"></i>
                Due Collection
            </a>

        </div>
    </div>
    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-wallet-add-01"></i>
                Financial & Account
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>
        <div class="sidebar-group-content">
            <a href="{{ route('school.donate') }}" data-title="Donate" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-charity"></i>
                Donate
            </a>
            <a href="{{ route('school.collection') }}" data-title="Collection" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-wallet-02"></i>
                Collection
            </a>
            <a href="{{ route('school.profit-loss') }}" data-title="Profit Loss" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-chart-line-data-01"></i>
                Profit & Loss
            </a>
        </div>
    </div>
    {{-- <!-- ================= Inventory ================= -->

    <p class="nav-header">Inventory Management</p>

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span><i class="fas fa-boxes w-4"></i> Inventory</span>
            <i class="fas fa-chevron-right text-xs"></i>
        </div>

        <div class="sidebar-group-content">

            <a href="{{ route('school.product') }}" data-title="Product" data-link class="sidebar-subitem">
    <i class="fas fa-box"></i> Product
    </a>

    <a href="{{ route('school.purchase') }}" data-title="Purchase" data-link
        class="sidebar-subitem">
        <i class="fas fa-shopping-cart"></i> Purchase
    </a>

    <a href="{{ route('school.return') }}" data-title="Return" data-link class="sidebar-subitem">
        <i class="fas fa-undo"></i> Return
    </a>

    <a href="{{ route('school.due-paid') }}" data-title="Due Paid" data-link
        class="sidebar-subitem">
        <i class="fas fa-money-check"></i> Due Paid
    </a>

    <a href="{{ route('school.profit-loss') }}" data-title="Profit Loss" data-link
        class="sidebar-subitem">
        <i class="fas fa-chart-line"></i> Profit-Loss
    </a>

    <a href="{{ route('school.add-payment') }}" data-title="Add Payment" data-link
        class="sidebar-subitem">
        <i class="fas fa-credit-card"></i> Add Payment
    </a>

    </div>
    </div> --}}

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-user-multiple-02"></i>
                HRM Management
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">
            <a href="{{ route('school.employee') }}" data-title="Employee" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-briefcase-01"></i>
                Employee
            </a>

            <a href="{{ route('school.payroll') }}" data-title="Payroll" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-dollar-circle"></i>
                Payroll
            </a>

            <a href="{{ route('school.expense') }}" data-title="Expense" data-link class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-money-send-01"></i>
                Expense
            </a>
        </div>
    </div>


    <!-- ================= Question Bank ================= -->
    <!-- <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-book-02"></i>
                Question Bank
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">

            <a href="{{ route('school.omr') }}" data-title="OMR" data-link class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-database"></i>
                OMR
            </a>

            <a href="{{ route('school.questions') }}" data-title="Questions" data-link class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-book-02"></i>
                Questions
            </a>

        </div>
    </div> -->


    <!-- ================= Notification Management ================= -->

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-notification-03 w-4"></i>
                Notification
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">

            <a href="{{ route('school.notification-holiday-coming-soon') }}" data-title="Holiday" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-calendar-remove-01"></i>
                Holiday
            </a>

            <a href="{{ route('school.notification-notice-coming-soon') }}" data-title="Notice" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-megaphone-02"></i>
                Notice
            </a>

            <a href="{{ route('school.notification-leave-coming-soon') }}" data-title="Leave List" data-link class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-leaf-01"></i>
                Leave List
            </a>

        </div>
    </div>

    <a href="{{ route('school.sms-settings') }}" data-title="SMS Settings" data-link class="sidebar-item">
        <i class="hgi hgi-stroke hgi-rounded hgi-message-01 w-4"></i>
        SMS Settings
    </a>

    {{-- <div class="sidebar-group">
<div class="sidebar-group-toggle">
    <span>
    <i class="hgi hgi-stroke hgi-rounded hgi-home-10"></i>
    Landing Page
    </span>
    <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
</div>

<div class="sidebar-group-content">
    <a href="{{ route('school.landing-open') }}" data-title="Open" data-link class="sidebar-subitem">
    <i class="hgi hgi-stroke hgi-rounded hgi-link-01"></i>
    Open
    </a>

    <a href="{{ route('school.landing-customize') }}" data-title="Customize" data-link class="sidebar-subitem">
    <i class="hgi hgi-stroke hgi-rounded hgi-paint-board"></i>
    Customize
    </a>

    <a href="{{ route('school.landing-blogs') }}" data-title="Blog List" data-link
    class="sidebar-subitem">
    <i class="hgi hgi-stroke hgi-rounded hgi-news"></i>
    Blog List
    </a>
</div>
    </div> --}}

    {{-- <div class="sidebar-group">
<div class="sidebar-group-toggle">
    <span>
    <i class="hgi hgi-stroke hgi-rounded hgi-global"></i>
    Custom Domain
    </span>
    <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
</div>

<div class="sidebar-group-content">
    <a href="{{ route('school.domain') }}" data-title="Domain" data-link class="sidebar-subitem">
    <i class="hgi hgi-stroke hgi-rounded hgi-link-square-02"></i>
    Domain
    </a>
</div>
    </div> --}}

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-identity-card"></i>
                Id Card Print
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">
            <a href="{{ route('school.id-card-orders') }}" data-title="Order" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-shopping-bag-03"></i>
                Order
            </a>

            <a href="{{ route('school.id-card-status') }}" data-title="Status" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-task-done-01"></i>
                Status
            </a>
        </div>
    </div>

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-grid-view"></i>
                Design Temple
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">
            <a href="{{ route('school.design-id-card') }}" data-title="Id Card" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-identity-card"></i>
                Id Card
            </a>

            <a href="{{ route('school.design-sms') }}" data-title="SMS" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-message-01"></i>
                SMS
            </a>
        </div>
    </div>

    <div class="sidebar-group">
        <div class="sidebar-group-toggle">
            <span>
                <i class="hgi hgi-stroke hgi-rounded hgi-artificial-intelligence-04"></i>
                Ai Call System
            </span>
            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
        </div>

        <div class="sidebar-group-content">
            <a href="{{ route('school.ai-call-registration') }}" data-title="Registration" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-user-add-01"></i>
                Registration
            </a>

            <a href="{{ route('school.ai-call-topup') }}" data-title="TopUp" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-arrow-up-01"></i>
                TopUp
            </a>

            <a href="{{ route('school.ai-call-history') }}" data-title="History" data-link
                class="sidebar-subitem">
                <i class="hgi hgi-stroke hgi-rounded hgi-time-03"></i>
                History
            </a>
        </div>
    </div>



    <div class="subscription-box mt-4 p-3 mb-2"
        style="border-radius: 0; border: 1px solid #e5e7eb; background: #fff;">
        <div class="plan-info">
            <p class="nav-header">
                Subscription</p>

            <p id="sidePlanName" class="text-sm font-bold text-slate-800 mb-1">School Pro</p>

            <p id="sideExpiryDate" class="text-[10px] text-slate-500 mb-3">Expires: 30 Apr, 2027</p>

            <div class="w-full bg-gray-200 h-1.5 mb-4" style="border-radius: 0; overflow: hidden;">
                <div id="sideProgressBar" class="bg-blue-600 h-full"
                    style="width: 72%; border-radius: 0; transition: width 1s ease-in-out;"></div>
            </div>

            <a href="{{ route('school.current-plan') }}" class="upgrade-btn"
                style="border-radius: 0; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; background: #1e40af; color: white; padding: 10px; text-decoration: none;">
                <i class="fas fa-link text-[9px]" aria-hidden="true"></i>
                <span class="menu-text">Upgrade Plan</span>
            </a>
        </div>
    </div>
</x-layout.sidebar>

@include('school.partials.sms-settings-modal')
