@extends('layouts.school')

@section('content')

<div class="timetable-page">

    {{-- ==============================
        PAGE HEADER
    =============================== --}}
    <div class="tt-header">
        <div>
            <div class="tt-breadcrumb">
                <i class="fas fa-fingerprint"></i>
                Fingerprint Attendance
                <span>/</span>
                Time Table
            </div>

            <h1>Attendance Time Table</h1>

            <p>
                Configure attendance start time, grace period and closing time
                for teachers, employees and students.
            </p>
        </div>

        <div class="tt-header-icon">
            <i class="far fa-clock"></i>
        </div>
    </div>


    {{-- ==============================
        SUMMARY CARDS
    =============================== --}}
    <div class="tt-stats">

        <div class="tt-stat-card purple">
            <div class="tt-stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>

            <div>
                <span>Teacher Schedules</span>
                <h3>12</h3>
            </div>
        </div>


        <div class="tt-stat-card blue">
            <div class="tt-stat-icon">
                <i class="fas fa-user-tie"></i>
            </div>

            <div>
                <span>Employee Schedules</span>
                <h3>08</h3>
            </div>
        </div>


        <div class="tt-stat-card green">
            <div class="tt-stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>

            <div>
                <span>Student Schedules</span>
                <h3>16</h3>
            </div>
        </div>


        <div class="tt-stat-card orange">
            <div class="tt-stat-icon">
                <i class="fas fa-clock"></i>
            </div>

            <div>
                <span>Active Rules</span>
                <h3>36</h3>
            </div>
        </div>

    </div>


    {{-- ==============================
        MAIN CARD
    =============================== --}}
    <div class="tt-main-card">

        {{-- TABS --}}
        <div class="tt-tabs">

            <button
                type="button"
                class="tt-tab active"
                data-tab="teacher"
            >
                <span class="tt-tab-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </span>

                <span>
                    <strong>Teacher</strong>
                    <small>Teacher attendance timing</small>
                </span>
            </button>


            <button
                type="button"
                class="tt-tab"
                data-tab="employee"
            >
                <span class="tt-tab-icon">
                    <i class="fas fa-user-tie"></i>
                </span>

                <span>
                    <strong>Employee</strong>
                    <small>Employee attendance timing</small>
                </span>
            </button>


            <button
                type="button"
                class="tt-tab"
                data-tab="student"
            >
                <span class="tt-tab-icon">
                    <i class="fas fa-user-graduate"></i>
                </span>

                <span>
                    <strong>Student</strong>
                    <small>Class attendance timing</small>
                </span>
            </button>

        </div>


        {{-- ========================================================
            TEACHER TAB
        ========================================================= --}}
        <div class="tt-tab-content active" id="teacher">

            <div class="tt-content-grid">

                {{-- FORM --}}
                <div class="tt-form-section">

                    <div class="tt-section-title">
                        <div class="tt-section-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>

                        <div>
                            <h3>Teacher Time Configuration</h3>
                            <p>Set attendance timing for a teacher</p>
                        </div>
                    </div>


                    <form>

                        <div class="tt-form-grid">

                            <div class="tt-form-group full">
                                <label>
                                    Select Teacher
                                    <span>*</span>
                                </label>

                                <div class="tt-input-wrapper">
                                    <i class="fas fa-user tt-input-icon"></i>

                                    <select class="tt-control">
                                        <option value="">Select Teacher</option>
                                        <option>Md. Rahim Uddin</option>
                                        <option>Abdul Karim</option>
                                        <option>Jannatul Ferdous</option>
                                    </select>
                                </div>
                            </div>


                            <div class="tt-form-group">
                                <label>Teacher ID</label>

                                <div class="tt-input-wrapper">
                                    <i class="fas fa-id-card tt-input-icon"></i>

                                    <input
                                        type="text"
                                        class="tt-control"
                                        value="TCH-001"
                                        readonly
                                    >
                                </div>
                            </div>


                            <div class="tt-form-group">
                                <label>Designation</label>

                                <div class="tt-input-wrapper">
                                    <i class="fas fa-briefcase tt-input-icon"></i>

                                    <input
                                        type="text"
                                        class="tt-control"
                                        value="Senior Teacher"
                                        readonly
                                    >
                                </div>
                            </div>

                        </div>


                        <div class="tt-time-heading">
                            <i class="far fa-clock"></i>
                            Attendance Timing
                        </div>


                        <div class="tt-time-grid">

                            <div class="tt-time-box start">

                                <div class="tt-time-top">
                                    <span class="tt-dot"></span>
                                    <strong>Start Time</strong>
                                </div>

                                <p>Official attendance start</p>

                                <input
                                    type="time"
                                    class="tt-time-input teacher-time"
                                    value="08:00"
                                >

                            </div>


                            <div class="tt-time-box grace">

                                <div class="tt-time-top">
                                    <span class="tt-dot"></span>
                                    <strong>Grace Time</strong>
                                </div>

                                <p>Present until this time</p>

                                <input
                                    type="time"
                                    class="tt-time-input teacher-time"
                                    value="08:15"
                                >

                            </div>


                            <div class="tt-time-box close">

                                <div class="tt-time-top">
                                    <span class="tt-dot"></span>
                                    <strong>Close Time</strong>
                                </div>

                                <p>Absent after this time</p>

                                <input
                                    type="time"
                                    class="tt-time-input teacher-time"
                                    value="09:00"
                                >

                            </div>

                        </div>


                        <div class="tt-info-box">

                            <div class="tt-info-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>

                            <div>
                                <strong>Attendance Rule</strong>

                                <p>
                                    Punch between
                                    <b>Start Time → Grace Time</b> = Present.
                                    After Grace Time → Close Time = Late.
                                    No punch before Close Time = Absent.
                                </p>
                            </div>

                        </div>


                        <div class="tt-actions">

                            <button type="reset" class="tt-btn tt-btn-light">
                                <i class="fas fa-undo"></i>
                                Reset
                            </button>

                            <button type="button" class="tt-btn tt-btn-primary">
                                <i class="fas fa-check"></i>
                                Save Time Table
                            </button>

                        </div>

                    </form>

                </div>


                {{-- PREVIEW --}}
                <div class="tt-preview">

                    <div class="tt-preview-header">
                        <div>
                            <span>LIVE PREVIEW</span>
                            <h3>Attendance Rule</h3>
                        </div>

                        <i class="fas fa-clock"></i>
                    </div>


                    <div class="tt-preview-profile">

                        <div class="tt-avatar">
                            <i class="fas fa-user"></i>
                        </div>

                        <div>
                            <h4>Md. Rahim Uddin</h4>
                            <p>TCH-001 • Senior Teacher</p>
                        </div>

                    </div>


                    <div class="tt-timeline">

                        <div class="tt-line"></div>


                        <div class="tt-timeline-item">

                            <span class="tt-timeline-dot start-dot"></span>

                            <div>
                                <small>START</small>
                                <strong>08:00 AM</strong>
                            </div>

                        </div>


                        <div class="tt-timeline-item">

                            <span class="tt-timeline-dot grace-dot"></span>

                            <div>
                                <small>GRACE</small>
                                <strong>08:15 AM</strong>
                            </div>

                        </div>


                        <div class="tt-timeline-item">

                            <span class="tt-timeline-dot close-dot"></span>

                            <div>
                                <small>CLOSE</small>
                                <strong>09:00 AM</strong>
                            </div>

                        </div>

                    </div>


                    <div class="tt-status-list">

                        <div class="tt-status present">
                            <span>
                                <i class="fas fa-check-circle"></i>
                                Present
                            </span>

                            <strong>08:00 - 08:15</strong>
                        </div>


                        <div class="tt-status late">
                            <span>
                                <i class="fas fa-clock"></i>
                                Late
                            </span>

                            <strong>08:16 - 09:00</strong>
                        </div>


                        <div class="tt-status absent">
                            <span>
                                <i class="fas fa-times-circle"></i>
                                Absent
                            </span>

                            <strong>After 09:00</strong>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
            EMPLOYEE TAB
        ========================================================= --}}
        <div class="tt-tab-content" id="employee">

            <div class="tt-content-grid">

                <div class="tt-form-section">

                    <div class="tt-section-title">

                        <div class="tt-section-icon blue-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>

                        <div>
                            <h3>Employee Time Configuration</h3>
                            <p>Set attendance timing for an employee</p>
                        </div>

                    </div>


                    <div class="tt-form-grid">

                        <div class="tt-form-group full">

                            <label>
                                Select Employee
                                <span>*</span>
                            </label>

                            <div class="tt-input-wrapper">

                                <i class="fas fa-user tt-input-icon"></i>

                                <select class="tt-control">
                                    <option>Select Employee</option>
                                    <option>Hasan Mahmud</option>
                                    <option>Rakib Hossain</option>
                                    <option>Saiful Islam</option>
                                </select>

                            </div>

                        </div>


                        <div class="tt-form-group">

                            <label>Employee ID</label>

                            <div class="tt-input-wrapper">

                                <i class="fas fa-id-card tt-input-icon"></i>

                                <input
                                    class="tt-control"
                                    value="EMP-001"
                                    readonly
                                >

                            </div>

                        </div>


                        <div class="tt-form-group">

                            <label>Designation</label>

                            <div class="tt-input-wrapper">

                                <i class="fas fa-briefcase tt-input-icon"></i>

                                <input
                                    class="tt-control"
                                    value="Accountant"
                                    readonly
                                >

                            </div>

                        </div>

                    </div>


                    <div class="tt-time-heading">
                        <i class="far fa-clock"></i>
                        Attendance Timing
                    </div>


                    <div class="tt-time-grid">

                        <div class="tt-time-box start">
                            <div class="tt-time-top">
                                <span class="tt-dot"></span>
                                <strong>Start Time</strong>
                            </div>

                            <p>Official office start</p>

                            <input
                                type="time"
                                class="tt-time-input"
                                value="09:00"
                            >
                        </div>


                        <div class="tt-time-box grace">
                            <div class="tt-time-top">
                                <span class="tt-dot"></span>
                                <strong>Grace Time</strong>
                            </div>

                            <p>Present until this time</p>

                            <input
                                type="time"
                                class="tt-time-input"
                                value="09:15"
                            >
                        </div>


                        <div class="tt-time-box close">
                            <div class="tt-time-top">
                                <span class="tt-dot"></span>
                                <strong>Close Time</strong>
                            </div>

                            <p>Absent after this time</p>

                            <input
                                type="time"
                                class="tt-time-input"
                                value="10:00"
                            >
                        </div>

                    </div>


                    <div class="tt-info-box">

                        <div class="tt-info-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>

                        <div>
                            <strong>Employee Attendance</strong>
                            <p>
                                In Punch and Out Punch can later be used
                                together to calculate complete attendance.
                            </p>
                        </div>

                    </div>


                    <div class="tt-actions">

                        <button class="tt-btn tt-btn-light">
                            <i class="fas fa-undo"></i>
                            Reset
                        </button>

                        <button class="tt-btn tt-btn-primary">
                            <i class="fas fa-check"></i>
                            Save Time Table
                        </button>

                    </div>

                </div>


                <div class="tt-preview">

                    <div class="tt-preview-header">

                        <div>
                            <span>EMPLOYEE</span>
                            <h3>Time Preview</h3>
                        </div>

                        <i class="fas fa-building"></i>

                    </div>


                    <div class="tt-preview-profile">

                        <div class="tt-avatar blue-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>

                        <div>
                            <h4>Hasan Mahmud</h4>
                            <p>EMP-001 • Accountant</p>
                        </div>

                    </div>


                    <div class="tt-status-list">

                        <div class="tt-status present">
                            <span>
                                <i class="fas fa-check-circle"></i>
                                Present
                            </span>
                            <strong>09:00 - 09:15</strong>
                        </div>

                        <div class="tt-status late">
                            <span>
                                <i class="fas fa-clock"></i>
                                Late
                            </span>
                            <strong>09:16 - 10:00</strong>
                        </div>

                        <div class="tt-status absent">
                            <span>
                                <i class="fas fa-times-circle"></i>
                                Absent
                            </span>
                            <strong>After 10:00</strong>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
            STUDENT TAB
        ========================================================= --}}
        <div class="tt-tab-content" id="student">

            <div class="tt-content-grid">

                <div class="tt-form-section">

                    <div class="tt-section-title">

                        <div class="tt-section-icon green-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>

                        <div>
                            <h3>Student Time Configuration</h3>
                            <p>Configure timetable class-wise</p>
                        </div>

                    </div>


                    <div class="tt-form-grid">

                        <div class="tt-form-group">

                            <label>
                                Class
                                <span>*</span>
                            </label>

                            <select class="tt-control no-icon">
                                <option>Select Class</option>
                                <option>Class 6</option>
                                <option>Class 7</option>
                                <option>Class 8</option>
                                <option>Class 9</option>
                                <option>Class 10</option>
                            </select>

                        </div>


                        <div class="tt-form-group">

                            <label>
                                Group
                                <span>*</span>
                            </label>

                            <select class="tt-control no-icon">
                                <option>Select Group</option>
                                <option>Science</option>
                                <option>Commerce</option>
                                <option>Arts</option>
                            </select>

                        </div>


                        <div class="tt-form-group">

                            <label>
                                Section
                                <span>*</span>
                            </label>

                            <select class="tt-control no-icon">
                                <option>Select Section</option>
                                <option>Section A</option>
                                <option>Section B</option>
                                <option>Section C</option>
                            </select>

                        </div>


                        <div class="tt-form-group">

                            <label>
                                Session
                                <span>*</span>
                            </label>

                            <select class="tt-control no-icon">
                                <option>2026</option>
                                <option>2025</option>
                                <option>2024</option>
                            </select>

                        </div>

                    </div>


                    <div class="tt-time-heading">
                        <i class="far fa-clock"></i>
                        Class Attendance Timing
                    </div>


                    <div class="tt-time-grid">

                        <div class="tt-time-box start">

                            <div class="tt-time-top">
                                <span class="tt-dot"></span>
                                <strong>Start Time</strong>
                            </div>

                            <p>Class attendance starts</p>

                            <input
                                type="time"
                                class="tt-time-input"
                                value="08:00"
                            >

                        </div>


                        <div class="tt-time-box grace">

                            <div class="tt-time-top">
                                <span class="tt-dot"></span>
                                <strong>Grace Time</strong>
                            </div>

                            <p>Present before this time</p>

                            <input
                                type="time"
                                class="tt-time-input"
                                value="08:15"
                            >

                        </div>


                        <div class="tt-time-box close">

                            <div class="tt-time-top">
                                <span class="tt-dot"></span>
                                <strong>Close Time</strong>
                            </div>

                            <p>Absent after this time</p>

                            <input
                                type="time"
                                class="tt-time-input"
                                value="09:00"
                            >

                        </div>

                    </div>


                    <div class="tt-info-box">

                        <div class="tt-info-icon">
                            <i class="fas fa-users"></i>
                        </div>

                        <div>
                            <strong>Class Wise Rule</strong>

                            <p>
                                This timing will apply to all students
                                belonging to the selected Class, Group,
                                Section and Session.
                            </p>
                        </div>

                    </div>


                    <div class="tt-actions">

                        <button class="tt-btn tt-btn-light">
                            <i class="fas fa-undo"></i>
                            Reset
                        </button>

                        <button class="tt-btn tt-btn-primary">
                            <i class="fas fa-check"></i>
                            Save Time Table
                        </button>

                    </div>

                </div>


                <div class="tt-preview">

                    <div class="tt-preview-header">

                        <div>
                            <span>CLASS RULE</span>
                            <h3>Student Preview</h3>
                        </div>

                        <i class="fas fa-graduation-cap"></i>

                    </div>


                    <div class="tt-class-preview">

                        <div class="tt-class-icon">
                            <i class="fas fa-users"></i>
                        </div>

                        <h2>Class 10</h2>

                        <p>
                            Science
                            <span>•</span>
                            Section A
                        </p>

                        <div class="tt-session-badge">
                            Session 2026
                        </div>

                    </div>


                    <div class="tt-status-list">

                        <div class="tt-status present">

                            <span>
                                <i class="fas fa-check-circle"></i>
                                Present
                            </span>

                            <strong>08:00 - 08:15</strong>

                        </div>


                        <div class="tt-status late">

                            <span>
                                <i class="fas fa-clock"></i>
                                Late
                            </span>

                            <strong>08:16 - 09:00</strong>

                        </div>


                        <div class="tt-status absent">

                            <span>
                                <i class="fas fa-times-circle"></i>
                                Absent
                            </span>

                            <strong>After 09:00</strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ==============================
        EXISTING TIMETABLES
    =============================== --}}
    <div class="tt-table-card">

        <div class="tt-table-header">

            <div>
                <h3>Configured Time Tables</h3>
                <p>View and manage existing attendance schedules</p>
            </div>

            <div class="tt-search">

                <i class="fas fa-search"></i>

                <input
                    type="text"
                    placeholder="Search..."
                >

            </div>

        </div>


        <div class="tt-table-responsive">

            <table class="tt-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Name / Class</th>
                        <th>Start Time</th>
                        <th>Grace Time</th>
                        <th>Close Time</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>


                <tbody>

                    <tr>

                        <td>01</td>

                        <td>
                            <span class="tt-type teacher-type">
                                <i class="fas fa-chalkboard-teacher"></i>
                                Teacher
                            </span>
                        </td>

                        <td>
                            <strong>Md. Rahim Uddin</strong>
                            <small>TCH-001</small>
                        </td>

                        <td>08:00 AM</td>

                        <td>08:15 AM</td>

                        <td>09:00 AM</td>

                        <td>
                            <span class="tt-active-badge">
                                <span></span>
                                Active
                            </span>
                        </td>

                        <td class="text-center">

                            <button class="tt-action-btn edit">
                                <i class="fas fa-pen"></i>
                            </button>

                            <button class="tt-action-btn delete">
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>


                    <tr>

                        <td>02</td>

                        <td>
                            <span class="tt-type employee-type">
                                <i class="fas fa-user-tie"></i>
                                Employee
                            </span>
                        </td>

                        <td>
                            <strong>Hasan Mahmud</strong>
                            <small>EMP-001</small>
                        </td>

                        <td>09:00 AM</td>

                        <td>09:15 AM</td>

                        <td>10:00 AM</td>

                        <td>
                            <span class="tt-active-badge">
                                <span></span>
                                Active
                            </span>
                        </td>

                        <td class="text-center">

                            <button class="tt-action-btn edit">
                                <i class="fas fa-pen"></i>
                            </button>

                            <button class="tt-action-btn delete">
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>


                    <tr>

                        <td>03</td>

                        <td>
                            <span class="tt-type student-type">
                                <i class="fas fa-user-graduate"></i>
                                Student
                            </span>
                        </td>

                        <td>
                            <strong>Class 10 - Science</strong>
                            <small>Section A • Session 2026</small>
                        </td>

                        <td>08:00 AM</td>

                        <td>08:15 AM</td>

                        <td>09:00 AM</td>

                        <td>
                            <span class="tt-active-badge">
                                <span></span>
                                Active
                            </span>
                        </td>

                        <td class="text-center">

                            <button class="tt-action-btn edit">
                                <i class="fas fa-pen"></i>
                            </button>

                            <button class="tt-action-btn delete">
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>





{{-- =========================================================
    JAVASCRIPT
========================================================= --}}
<script>

    document.addEventListener('DOMContentLoaded', function () {

        const tabs = document.querySelectorAll('.tt-tab');
        const contents = document.querySelectorAll('.tt-tab-content');

        tabs.forEach(function (tab) {

            tab.addEventListener('click', function () {

                const target = this.getAttribute('data-tab');

                tabs.forEach(function (item) {
                    item.classList.remove('active');
                });

                contents.forEach(function (content) {
                    content.classList.remove('active');
                });

                this.classList.add('active');

                document
                    .getElementById(target)
                    .classList.add('active');

            });

        });

    });

</script>
@endsection