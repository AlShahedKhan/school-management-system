<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandAssets['brandTitle'] ?? 'School SaaS' }}</title>
    <link rel="icon" type="{{ $brandAssets['faviconType'] ?? 'image/png' }}" href="{{ $brandAssets['faviconUrl'] ?? asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ $brandAssets['faviconUrl'] ?? asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ $brandAssets['faviconUrl'] ?? asset('images/logo.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />


    <style>
        body {
            background: #f3f4f6;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            font-family: 'Roboto', sans-serif;
        }

        /* 1. Apply Roboto to everything */
        *,
        *::before,
        *::after {
            font-family: 'Roboto', sans-serif;
        }

        /* 2. FORCE FontAwesome to keep its own font for icons */
        /* We add 'i' and [class^="fa-"] to ensure we catch all icon types */
        i.fas,
        i.fab,
        i.far,
        i.fal,
        i.fa-solid,
        i.fa-regular,
        i.fa-brands,
        i.fa,
        [class^="fa-"]::before,
        [class*=" fa-"]::before {
            font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "Font Awesome" !important;
            font-weight: 900 !important;
            /* Required for Solid icons */
        }

        body:not(.login-active) {
            padding-top: 65px !important;
        }


        /* Register form: stay centered with equal space, scroll internally */
        body.register-active {
            overflow: hidden !important;
            height: 100vh;
            padding: 0 !important;
            align-items: center;
        }

        body.register-active .card-wrapper {
            max-height: calc(100vh - 100px);
            margin: 0 auto !important;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: none;
        }

        body.register-active .card {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        body.register-active #registerContainer {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding-right: 2px;
        }

        body.register-active #registerContainer::-webkit-scrollbar {
            width: 3px;
        }

        body.register-active #registerContainer::-webkit-scrollbar-thumb {
            background: #d1d5db;
        }

        @media (max-width: 480px) {
            body.register-active {
                display: flex !important;
                justify-content: center;
            }

            body.register-active .card-wrapper {
                max-height: calc(100vh - 100px);
                margin: 0 auto !important;
            }

            body.register-active .card {
                box-shadow: none;
                border-bottom: none;
                padding: 50px 25px 50px 25px;
            }
        }

        @keyframes fadeUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-wrapper {
            width: 90%;
            max-width: 400px;
            margin: 0 auto;
            animation: fadeUp 0.5s ease forwards;
        }

        .card {
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            /* Reduced top padding since logo is no longer floating outside */
            padding: 20px 20px;
            border-radius: 0;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .logo-box {
            /* Remove the absolute positioning properties */
            position: relative;
            top: 0;
            left: 0;
            transform: none;

            /* Keep dimensions and centering */
            width: 97px;
            height: 97px;
            margin: 0 auto 10px auto !important;
            /* Centers horizontally and adds space below */

            background: #fff;
            border-radius: 50%;
            /* Keeping the circle for the logo branding */
            border: 1px solid #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* fills entire circle */
            border-radius: 50%;
        }

        .form-input {
            width: 100%;
            border: 1px solid #d1d5db;
            padding: 6px 9px;
            font-size: 13px;
            margin-bottom: 5px;
            border-radius: 0;
            transition: .2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #9ca3af;
        }

        .input-error {
            border-color: red !important;
        }

        .error-text {
            font-size: 11px;
            color: red;
            margin-bottom: 5px;
        }

        .divider {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            margin: 20px 0 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
            text-transform: capitalize;
        }

        .divider-login {
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            margin: 20px 0 10px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            line-height: 0.1em;
        }

        .divider-login span {
            background: #fff;
            padding: 0 10px;
        }

        .switch-btn {
            flex: 1;
            padding: 6px 0;
            font-size: 13px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            cursor: pointer;
            text-align: center;
            font-weight: 500;
            transition: all .2s;
            border-radius: 0;
        }

        .switch-active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .step-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .step {
            text-align: center;
            font-size: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .step-circle {
            width: 28px;
            height: 28px;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3px;
            border-radius: 50%;
            font-weight: 600;
            font-size: 12px;
        }

        .step-arrow {
            font-size: 12px;
            color: #9ca3af;
        }

        .step-active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .step-complete {
            background: #16a34a;
            color: #fff;
            border-color: #16a34a;
        }

        .preview-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-top: 8px;
            border: 1px solid #ddd;
            border-radius: 0;
        }

        button {
            font-size: 13px;
            padding: 6px 9px !important;
            border-radius: 0;
            transition: all .2s;
            font-weight: 600;
            cursor: pointer;

            display: flex;
            align-items: center;
            justify-content: center;

            height: 34px !important;
        }


        .btn-outline {
            border: 2px solid #2563eb;
            color: #2563eb;
            background: #fff;
            width: 100%;
            height: 36px;
        }

        .btn-outline:hover {
            background: #2563eb;
            color: #fff;
        }

        .btn-outline-green {
            border: 2px solid #16a34a;
            color: #16a34a;
            background: #fff;
            width: 100%;
            height: 36px;
        }

        .btn-outline-green:hover {
            background: #16a34a;
            color: #fff;
        }

        .text-sm-space {
            margin-bottom: 10px;
        }

        @media (max-width: 480px) {
            body {
                padding-top: 60px !important;
                display: block;
            }

            .card-wrapper {
                max-width: 100%;
                width: 100%;
                padding: 0 25px !important;
                animation: none;
                margin-bottom: 50px !important;
                margin-top: 0px !important;
            }

            .card {
                box-shadow: none;
                padding: 30px 25px 20px;
                border-radius: 0;
                border-bottom: 1px solid #eee;
                /* Light separator instead of shadow */
            }

            .step-circle {
                width: 26px;
                height: 26px;
                font-size: 11px;
            }

            .step-arrow {
                font-size: 10px;
            }

            .switch-btn {
                font-size: 12px;
                padding: 5px 0;
            }

            .form-input {
                padding: 6px 9px;
                font-size: 13px;
            }

            button {
                font-size: 12px;
                padding: 5px 8px !important;
                height: 32px !important;
            }
        }

        /* ONLY center login screen on mobile */
        @media (max-width: 480px) {

            body.login-active {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                min-height: 100vh;
                padding-top: 0 !important;
            }

            body.login-active .card-wrapper {
                margin: auto !important;
            }
        }


        /* =====FORM SPACING ===== */

        /* Space between all form elements */
        #loginForm .form-input {
            margin-bottom: 8px;
        }

        /* Space between password field block */
        #loginForm .relative {
            margin-bottom: 8px;
        }

        /* Checkbox + forgot section spacing */
        #loginForm .flex.justify-between {
            margin-top: 2px;
            margin-bottom: 10px;
        }

        /* Login button spacing */
        #loginForm .btn-outline {
            margin-top: 8px;
        }

        /* Divider spacing */
        #loginForm .divider-login {
            margin: 15px 0 10px;
        }

        /* Registration link spacing */
        #loginForm .text-center.mt-4 {
            margin-top: 12px;
        }

        /* Social section spacing */
        #loginForm .text-gray-500 {
            margin-top: 22px;
        }

        #loginForm .flex.justify-center {
            margin-top: 10px;
        }

        /* Align checkbox and text perfectly */
        #loginForm label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        /* Normalize checkbox position */
        #loginForm input[type="checkbox"] {
            margin: 0;
            position: relative;
            top: 1px;
            /* fine-tune vertical alignment */
        }


        /* Space between Sign in & fields on desktop  */
        #loginForm h2 {
            margin-top: 2px;
            /* space between logo and Sign In */
            margin-bottom: 2px;
        }

        #loginForm #login_identifier {
            margin-bottom: 16px;
            /* space between ID and password */
        }


        /* Space between Sign in & fields on mobile  */
        @media (max-width:480px) {

            #loginForm h2 {
                margin-top: 15px;
                /* smaller space for mobile */
                margin-bottom: 20px;
            }

            #loginForm #login_identifier {
                margin-bottom: 10px;
            }

        }

        /* Hide the switch button from Registration card */
        #registerContainer .flex.mb-6 {
            display: none;
        }

        /* Perfect vertical centering for password toggle icons */
        .relative i.fa-eye,
        .relative i.fa-eye-slash {
            position: absolute !important;
            /* This calculates the center regardless of input padding */
            top: 18px !important;
            transform: translateY(-50%) !important;
            right: 12px !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center;
            cursor: pointer;
            z-index: 20;
            /* Ensure no height stretching */
            height: auto !important;
        }

        /* Adjust for the pr-10 padding to ensure text doesn't overlap eye */
        .form-input.pr-10 {
            padding-right: 35px !important;
        }


        /* Custom Select System - Adjusted to match form-input height */
        .custom-select-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 12px;
        }

        .custom-select-display {
            background: #fff;
            border: 1px solid #d1d5db;
            /* Matched to .form-input */
            height: 31px;
            /* Matches the visual height of your 34px button/input including borders */
            padding: 0 9px;
            /* Matched to .form-input padding */
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-size: 13px;
            /* Matched to .form-input font-size */
            color: #374151;
            transition: border-color 0.2s;
            border-radius: 0;
        }

        .custom-select-display:hover {
            border-color: #9ca3af;
            /* Matched to .form-input:focus color */
        }

        .custom-select-display i {
            font-size: 10px;
            color: #9ca3af;
            transition: transform 0.3s;
        }

        .custom-select-wrapper.active .custom-select-display i {
            transform: rotate(180deg);
        }

        .custom-select-options {
            position: absolute;
            top: 36px;
            /* Reduced gap to stay close to the smaller box */
            left: 0;
            width: 100%;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 999;
            display: none;
            max-height: calc(35px * 5);
            /* Max height for 5 items based on smaller padding */
            overflow-y: auto;
            padding: 2px 0;
        }

        .custom-option {
            padding: 8px 12px;
            /* Slightly tighter padding for better fit */
            font-size: 12px;
            color: #4b5563;
            cursor: pointer;
            transition: background 0.2s;
        }

        .custom-option:hover {
            background: #f3f4f6;
            color: #1e40af;
        }

        /* Hide the real select but keep it functional */
        .hidden-select {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            height: 0;
            width: 0;
        }

        /* Scrollbar Styling */
        .custom-select-options::-webkit-scrollbar {
            width: 4px;
        }

        .custom-select-options::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        /* Mobile alignment for custom select */
        @media (max-width: 480px) {
            .custom-select-display {
                height: 29px;
                /* Slightly smaller for mobile to match buttons */
                font-size: 13px;
            }

            .custom-select-options {
                top: 32px;
            }
        }

        #brand_banner_box img {
            width: 100%;
            height: 100%;
            object-fit: contain !important;
            /* Change to cover if you prefer no gaps */
            display: block;
        }

        #brand_description {
            margin-bottom: 12px !important;
            /* Reduced from mb-6 (24px) */
            line-height: 1.2;
        }

        .mt-6.border-t.pt-4 {
            margin-top: 12px !important;
            /* Reduced from mt-6 */
            padding-top: 8px !important;
            /* Reduced from pt-4 */
        }

        #promotion_text {
            margin-bottom: 4px !important;
            /* Reduced from mb-2 */
        }

        /* ============================================================
            BUTTONS NEW VERSION
        ============================================================ */
        .btn-solid-primary,
        .btn-solid-secondary {
            flex: 1;
            height: 42px;
            border: none;
            font-weight: 600;
            font-size: 14px;
            /* Changed from uppercase to capitalize per request */
            text-transform: capitalize;
            cursor: pointer;
            /* Removed all transitions and transform interactions */
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0px !important;
        }

        /* Primary Action: CodeCanyon Vibrant Blue (Static) */
        .btn-solid-primary {
            background: #3b82f6;
            color: #ffffff;
        }

        /* Secondary Action: CodeCanyon Professional Slate (Static) */
        .btn-solid-secondary {
            background: #64748b;
            color: #ffffff;
        }

        /* Minimal Hover: Only slight color change, no movement/shadows */
        .btn-solid-primary:hover {
            background: #2563eb;
        }

        .btn-solid-secondary:hover {
            background: #475569;
        }


        /* ============================================================
         ULTRA-COMPACT REMEMBER ME SWITCH (ELITE MINIMALIST)
        ============================================================ */
        .switch {
            position: relative;
            display: inline-block;
            /* Ultra-small footprint */
            width: 24px;
            height: 12px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .2s;
            /* Faster transition for smaller distance */
        }

        /* The Toggle Square */
        .slider:before {
            position: absolute;
            content: "";
            height: 8px;
            width: 8px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .2s;
        }

        input:checked+.slider {
            background-color: #3b82f6;
            /* CodeCanyon Standard Blue */
        }

        /* Move logic: (Total Width 24px) - (Square 8px) - (Left Offset 2px) = 14px */
        input:checked+.slider:before {
            transform: translateX(12px);
        }
    </style>
</head>

<body>

    <div class="card-wrapper">
        <div class="card">
            <!-- LOGO -->
            <div class="logo-box">
                <img src="{{ $brandAssets['logoLightUrl'] ?? asset('images/logo.png') }}" alt="{{ $brandAssets['logoAlt'] ?? 'Brand Logo' }}">
            </div>

            <div id="loginForm">
                <h2 class="text-center font-semibold text-3xl mb-1">Sign In</h2>
                <p id="brand_description" class="text-sm text-gray-500 text-center w-full mb-6">
                    {{ $brandAssets['brandDescription'] ?? 'Elite Multi-Tenant Saas Infrastructure For Modern Institutional Management.' }}
                </p>

                <input type="text" id="login_identifier" placeholder="ID Number" class="form-input">
                <div id="login_identifier_error" class="error-text hidden"></div>

                <div class="relative">
                    <input id="loginPass" type="password" placeholder="Password" class="form-input pr-10">
                    <i class="fa fa-eye absolute right-3 text-gray-400 cursor-pointer"
                        onclick="togglePassword('loginPass',this)"></i>
                    <div id="loginPass_error" class="error-text hidden"></div>
                </div>

                <div class="flex justify-between items-center text-xs mb-6">
                    <div class="flex items-center gap-2">
                        <label class="switch">
                            <input type="checkbox" id="rememberMe">
                            <span class="slider"></span>
                        </label>
                        <span class="text-slate-600 font-medium">Remember Me</span>
                    </div>
                    <a href="javascript:void(0)" onclick="openForgetModal()" class="text-blue-600 font-medium">Forgot
                        Password</a>
                </div>

                <div class="flex gap-3 mt-5 mb-5">
                    <button type="button" class="btn-solid-secondary" onclick="showRegister()">Registration</button>
                    <button type="button" id="loginSubmitBtn" class="btn-solid-primary" onclick="loginUser()">Login</button>
                </div>

                <div class="mt-6 border-t pt-4">
                    <p id="promotion_text"
                        class="text-center text-[10px] font-semibold text-gray-400 tracking-widest mb-2">{{ $brandAssets['promotionText'] ?? 'Promoted' }}</p>

                    <div id="brand_banner_box"
                        class="banner-box w-full h-20 bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                        @if (!empty($brandAssets['bannerUrl']))
                            <img src="{{ $brandAssets['bannerUrl'] }}" alt="Brand Banner" class="w-full h-full object-contain">
                        @else
                            <span class="px-4 text-center text-xs font-medium text-gray-400">{{ $brandAssets['brandTitle'] ?? 'Astha Academics' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ================= REGISTER ================= -->
            <div id="registerContainer" class="hidden">

                <div class="flex mb-6 gap-3">
                    <button id="schoolBtn" class="switch-btn switch-active"
                        onclick="switchType('school')">School</button>
                    <button id="admissionBtn" class="switch-btn" onclick="switchType('admission')">Admission</button>
                </div>

                <!-- ================= SCHOOL REGISTER ================= -->
                <div id="schoolRegister">

                    <div class="divider text-xl">School Information</div>

                    <input id="school_name" placeholder="School Name" class="form-input">
                    <div id="school_name_error" class="error-text hidden"></div>

                    <div class="custom-select-wrapper" data-target="division">
                        <div class="custom-select-display">
                            <span class="placeholder">Select Division</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="custom-select-options"></div>
                        <select id="division" class="hidden-select">
                            <option value="">Select Division</option>
                        </select>
                    </div>
                    <div id="division_error" class="error-text hidden"></div>

                    <div class="custom-select-wrapper" data-target="district">
                        <div class="custom-select-display">
                            <span class="placeholder">Select District</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="custom-select-options"></div>
                        <select id="district" class="hidden-select">
                            <option value="">Select District</option>
                        </select>
                    </div>
                    <div id="district_error" class="error-text hidden"></div>

                    <div class="custom-select-wrapper" data-target="upazila">
                        <div class="custom-select-display">
                            <span class="placeholder">Select Upazila</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="custom-select-options"></div>
                        <select id="upazila" class="hidden-select">
                            <option value="">Select Upazila</option>
                        </select>
                    </div>
                    <div id="upazila_error" class="error-text hidden"></div>

                    <input id="village" placeholder="Village" class="form-input">
                    <div id="village_error" class="error-text hidden"></div>

                    <input id="eiin_number" placeholder="EIIN Number" class="form-input">
                    <div id="eiin_number_error" class="error-text hidden"></div>

                    <input id="mobile" placeholder="Mobile" class="form-input">
                    <div id="mobile_error" class="error-text hidden"></div>

                    <input id="email" placeholder="Email" class="form-input">
                    <div id="email_error" class="error-text hidden"></div>

                    <div class="relative mb-3">
                        <input id="schoolPass" type="password" placeholder="Password" class="form-input pr-10">
                        <i class="fa fa-eye absolute right-3 top-3 text-gray-400 cursor-pointer"
                            onclick="togglePassword('schoolPass',this)"></i>
                        <div id="schoolPass_error" class="error-text hidden"></div>
                    </div>

                    <div class="relative mb-3">
                        <input id="schoolPass2" type="password" placeholder="Confirm Password"
                            class="form-input pr-10">
                        <i class="fa fa-eye absolute right-3 top-3 text-gray-400 cursor-pointer"
                            onclick="togglePassword('schoolPass2',this)"></i>
                        <div id="schoolPass2_error" class="error-text hidden"></div>
                    </div>

                    <div class="divider mt-6">Select Subscription Plan</div>

                    <div class="custom-select-wrapper" data-target="package_dropdown">
                        <div class="custom-select-display">
                            <span class="placeholder">Select Package</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="custom-select-options"></div>
                        <select id="package_dropdown" class="hidden-select" onchange="updatePackageCard()">
                            <option value="">Select Package</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}" data-price="{{ $package->per_student_price }}"
                                    data-students="{{ $package->student_limit }}"
                                    data-teachers="{{ $package->teacher_limit }}"
                                    data-discount="{{ $package->annual_discount_percent ?? 0 }}"
                                    data-sms="{{ $package->sms_limit }}" data-trial="{{ $package->free_trial_days }}"
                                    data-monthly="{{ $package->total_payable }}">
                                    {{ $package->package_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="packageCard"
                        class="hidden mt-3 p-3 border rounded-none bg-white shadow-sm text-gray-700">
                        <h3 id="cardType" class="font-semibold text-gray-800"></h3>
                        <p id="cardStudents" class="text-sm text-gray-500 mt-1"></p>
                        <p id="cardTeachers" class="text-sm text-gray-500"></p>
                        <p id="cardSMS" class="text-xs text-gray-400 mt-1"></p>
                        <p id="cardTrial" class="text-xs text-blue-600 mt-1"></p>
                        <p id="cardMonthly" class="text-xs text-indigo-600 mt-1"></p>
                        <p id="cardDiscount" class="text-xs text-green-600 mt-1"></p>
                        <p id="cardPrice" class="mt-1 text-sm text-gray-700"></p>
                    </div>

                    <input type="hidden" id="package_id">

                    <div class="mb-4 mt-4">
                        <label class="block text-sm text-gray-600 mb-1">Duration</label>
                        <div class="custom-select-wrapper" data-target="duration_months">
                            <div class="custom-select-display">
                                <span>4 Months</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="custom-select-options"></div>
                            <select id="duration_months" class="hidden-select" onchange="calculatePrice()">
                                <option value="4">4 Months</option>
                                <option value="8">8 Months</option>
                                <option value="12">12 Months</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Start Date</label>
                        <input type="date" id="start_date" class="form-input text-gray-700"
                            value="{{ now()->format('Y-m-d') }}" onchange="calculatePrice()">
                    </div>

                    <div class="bg-gray-50 p-3 rounded-none mb-4 shadow-sm text-gray-700">
                        <p class="text-sm mb-1">Total Price: <span id="originalPrice"
                                class="font-medium text-gray-700">৳0</span></p>
                        <p class="text-sm mb-0">Discounted Price: <span id="finalPrice"
                                class="font-medium text-gray-700">৳0</span></p>
                    </div>

                    <div class="divider">Upload School Logo</div>
                    <input type="file" id="school_logo" onchange="previewImage(this,'schoolPreview')"
                        class="form-input">
                    <div id="logo_error" class="error-text hidden"></div>
                    <img id="schoolPreview" class="preview-img hidden">

                    <div class="divider">Register</div>
                    <div class="flex gap-3 mt-2">
                        <button class="btn-solid-primary" onclick="showLogin()">Login</button>

                        <button id="registerBtn" class="btn-solid-secondary flex items-center justify-center gap-2"
                            onclick="registerSchool()">
                            <span id="registerBtnText">Registration</span>
                            <span id="registerBtnLoader" class="hidden"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </div>

                <!-- ================= ADMISSION REGISTER ================= -->
                <div id="admissionRegister" class="hidden">

                    <div class="step-wrapper">
                        <div class="step">
                            <div id="step1mark" class="step-circle step-active">1</div>
                            <span>Register</span>
                        </div>
                        <span class="step-arrow">→</span>
                        <div class="step">
                            <div id="step2mark" class="step-circle">2</div>
                            <span>School</span>
                        </div>
                        <span class="step-arrow">→</span>
                        <div class="step">
                            <div id="step3mark" class="step-circle">3</div>
                            <span>Guardian</span>
                        </div>
                        <span class="step-arrow">→</span>
                        <div class="step">
                            <div id="step4mark" class="step-circle">4</div>
                            <span>Student</span>
                        </div>
                    </div>

                    <!-- STEP 1 -->
                    <div id="step1">
                        <div class="divider">Find Your Location</div>
                        <select id="a_division" class="form-input required-field">
                            <option value="">Select Division</option>
                        </select>
                        <div id="a_division_error" class="error-text hidden"></div>

                        <select id="a_district" class="form-input required-field">
                            <option value="">Select District</option>
                        </select>
                        <div id="a_district_error" class="error-text hidden"></div>

                        <select id="a_upazila" class="form-input required-field">
                            <option value="">Select Upazila</option>
                        </select>
                        <div id="a_upazila_error" class="error-text hidden"></div>

                        <div class="divider">School Information</div>
                        <select id="a_school" class="form-input required-field">
                            <option value="">Select School</option>
                        </select>
                        <div id="a_school_error" class="error-text hidden"></div>

                        <select id="a_class" class="form-input required-field">
                            <option value="">Select Class</option>
                        </select>
                        <div id="a_class_error" class="error-text hidden"></div>

                        <select id="a_group" class="form-input">
                            <option value="">Select Group</option>
                        </select>
                        <div id="a_group_error" class="error-text hidden"></div>

                        <select id="a_session" class="form-input required-field">
                            <option value="">Select Session</option>
                        </select>
                        <div id="a_session_error" class="error-text hidden"></div>

                        <select id="a_fee" class="form-input required-field">
                            <option value="">Select Fee Type</option>
                        </select>
                        <div id="a_fee_error" class="error-text hidden"></div>
                        <input type="date" id="a_date" class="form-input required-field">
                        <div id="a_date_error" class="error-text hidden"></div>

                        <div class="divider"></div>
                        <div class="flex justify-between gap-3">
                            <button class="btn-outline" onclick="showLogin()">Back</button>
                            <button class="btn-outline" onclick="nextStep(1)">Next</button>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div id="step2" class="hidden">
                        <div class="divider">Previous School Info</div>
                        <input id="p_school" placeholder="Previous School" class="form-input required-field">
                        <div id="p_school_error" class="error-text hidden"></div>

                        <input id="p_class" placeholder="Class" class="form-input">
                        <div id="p_class_error" class="error-text hidden"></div>

                        <input id="p_group" placeholder="Group" class="form-input">
                        <div id="p_group_error" class="error-text hidden"></div>

                        <input id="p_section" placeholder="Section" class="form-input">
                        <div id="p_section_error" class="error-text hidden"></div>

                        <input id="p_session" placeholder="Session" class="form-input">
                        <div id="p_session_error" class="error-text hidden"></div>

                        <input id="last_exam_result" placeholder="Last Exam Result" class="form-input">
                        <div id="last_exam_result_error" class="error-text hidden"></div>

                        <div class="divider"></div>
                        <div class="flex justify-between gap-3">
                            <button class="btn-outline" onclick="showStep(1)">Back</button>
                            <button class="btn-outline" onclick="nextStep(2)">Next</button>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div id="step3" class="hidden">
                        <div class="divider">Guardian Information</div>
                        <input id="g_name" placeholder="Guardian Name" class="form-input required-field">
                        <div id="g_name_error" class="error-text hidden"></div>

                        <input id="g_relation" placeholder="Relation" class="form-input required-field">
                        <div id="g_relation_error" class="error-text hidden"></div>

                        <select id="g_division" class="form-input">
                            <option value="">Select Division</option>
                        </select>
                        <div id="g_division_error" class="error-text hidden"></div>

                        <select id="g_district" class="form-input">
                            <option value="">Select District</option>
                        </select>
                        <div id="g_district_error" class="error-text hidden"></div>

                        <select id="g_upazila" class="form-input">
                            <option value="">Select Upazila</option>
                        </select>
                        <div id="g_upazila_error" class="error-text hidden"></div>

                        <input id="g_village" placeholder="Village" class="form-input">
                        <div id="g_village_error" class="error-text hidden"></div>

                        <input id="g_mobile" placeholder="Mobile" class="form-input required-field">
                        <div id="g_mobile_error" class="error-text hidden"></div>

                        <div class="divider"></div>
                        <div class="flex justify-between gap-3">
                            <button class="btn-outline" onclick="showStep(2)">Back</button>
                            <button class="btn-outline" onclick="nextStep(3)">Next</button>
                        </div>
                    </div>

                    <!-- STEP 4 -->
                    <div id="step4" class="hidden">
                        <div class="divider">Student Information</div>
                        <input id="student_name" placeholder="Student Name" class="form-input required-field">
                        <div id="student_name_error" class="error-text hidden"></div>

                        <input id="father_name" placeholder="Father Name" class="form-input required-field">
                        <div id="father_name_error" class="error-text hidden"></div>

                        <input id="mother_name" placeholder="Mother Name" class="form-input required-field">
                        <div id="mother_name_error" class="error-text hidden"></div>


                        <div class="divider">Current Location</div>
                        <select id="c_division" class="form-input">
                            <option value="">Select Division</option>
                        </select>
                        <div id="c_division_error" class="error-text hidden"></div>

                        <select id="c_district" class="form-input">
                            <option value="">Select District</option>
                        </select>
                        <div id="c_district_error" class="error-text hidden"></div>

                        <select id="c_upazila" class="form-input">
                            <option value="">Select Upazila</option>
                        </select>
                        <div id="c_upazila_error" class="error-text hidden"></div>

                        <input id="c_village" placeholder="Current Village" class="form-input">
                        <div id="c_village_error" class="error-text hidden"></div>

                        <div class="divider">Permanent Location</div>
                        <select id="p_division2" class="form-input">
                            <option value="">Select Division</option>
                        </select>
                        <div id="p_division2_error" class="error-text hidden"></div>

                        <select id="p_district2" class="form-input">
                            <option value="">Select District</option>
                        </select>
                        <div id="p_district2_error" class="error-text hidden"></div>

                        <select id="p_upazila2" class="form-input">
                            <option value="">Select Upazila</option>
                        </select>
                        <div id="p_upazila2_error" class="error-text hidden"></div>

                        <input id="p_village2" placeholder="Permanent Village" class="form-input">
                        <div id="p_village2_error" class="error-text hidden"></div>

                        <input id="student_mobile" placeholder="Mobile" class="form-input required-field">
                        <div id="student_mobile_error" class="error-text hidden"></div>

                        <div class="relative">
                            <input id="studentPass" type="password" placeholder="Password"
                                class="form-input pr-10 required-field">
                            <i class="fa fa-eye absolute right-3 top-3 cursor-pointer"
                                onclick="togglePassword('studentPass',this)"></i>
                            <div id="studentPass_error" class="error-text hidden"></div>
                        </div>

                        <div class="relative">
                            <input id="studentPass2" type="password" placeholder="Confirm Password"
                                class="form-input pr-10 required-field">
                            <i class="fa fa-eye absolute right-3 top-3 cursor-pointer"
                                onclick="togglePassword('studentPass2',this)"></i>
                            <div id="studentPass2_error" class="error-text hidden"></div>
                        </div>

                        <div class="divider">Upload Student Image</div>
                        <input type="file" id="student_image" onchange="previewImage(this,'studentPreview')"
                            class="form-input">
                        <div id="student_image_error" class="error-text hidden"></div>
                        <img id="studentPreview" class="preview-img hidden">

                        <div class="divider"></div>
                        <div class="flex justify-between gap-3">
                            <button class="btn-outline" onclick="showStep(3)">Back</button>
                            <button class="btn-outline-green" onclick="registerAdmission()">Register</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= FORGET PASSWORD MODAL ================= -->
    <div id="forgetPasswordModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">

        <div class="bg-white shadow-2xl w-full max-w-sm px-6 pt-8 pb-10 relative flex flex-col justify-between">

            <!-- STEP 1: REQUEST OTP -->
            <div id="step_request_otp" class="flex flex-col justify-between h-full space-y-6">

                <div class="space-y-6">
                    <h2 class="text-2xl font-semibold text-center">Reset Password</h2>

                    <p class="text-sm text-gray-500 text-center leading-relaxed">
                        Enter your mobile number to receive a 4-digit verification code
                    </p>

                    <input type="text" id="reset_mobile" placeholder="Mobile Number (017XXXXXXXX)"
                        class="form-input w-full px-3 py-2.5 border border-gray-300">
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-2">

                    <button class="btn-outline w-1/2 py-2.5 flex items-center justify-center text-sm font-medium"
                        onclick="showLogin(); closeForgetModal();">
                        Login
                    </button>

                    <button class="btn-outline w-1/2 py-2.5 flex items-center justify-center text-sm font-medium"
                        onclick="sendResetOTP()">
                        Send OTP
                    </button>

                </div>

            </div>


            <!-- STEP 2: VERIFY OTP -->
            <div id="step_verify_otp" class="hidden flex flex-col justify-between text-center h-full space-y-6">

                <div class="space-y-6">

                    <h2 class="text-2xl font-semibold">Verify Code</h2>

                    <p class="text-sm text-gray-500 leading-relaxed">
                        Enter the 4-digit OTP sent to your mobile
                    </p>

                    <input type="text" id="reset_otp" maxlength="4" placeholder="----"
                        class="form-input text-center tracking-[6px] text-lg w-full px-3 py-2.5 border border-gray-300">

                    <div id="otp_timer" class="text-sm text-gray-500">
                        OTP expires in 02:00 minutes
                    </div>

                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-2">

                    <button class="btn-outline w-1/2 py-2.5 flex items-center justify-center text-sm font-medium"
                        onclick="showLogin(); closeForgetModal();">
                        Login
                    </button>

                    <button class="btn-outline w-1/2 py-2.5 flex items-center justify-center text-sm font-medium"
                        onclick="verifyResetOTP()">
                        Verify OTP
                    </button>

                </div>

            </div>


            <!-- STEP 3: NEW PASSWORD -->
            <div id="step_new_password" class="hidden flex flex-col justify-between h-full space-y-6">

                <div class="space-y-6">

                    <h2 class="text-2xl font-semibold text-center">Create New Password</h2>

                    <div class="relative">
                        <input type="password" id="new_password" placeholder="New Password"
                            class="form-input pr-10 w-full px-3 py-2.5 border border-gray-300">

                        <i class="fa fa-eye absolute right-3 top-3 text-gray-400 cursor-pointer"
                            onclick="togglePassword('new_password', this)"></i>
                    </div>

                    <div class="relative">
                        <input type="password" id="new_password_confirmation" placeholder="Confirm Password"
                            class="form-input pr-10 w-full px-3 py-2.5 border border-gray-300">

                        <i class="fa fa-eye absolute right-3 top-3 text-gray-400 cursor-pointer"
                            onclick="togglePassword('new_password_confirmation', this)"></i>
                    </div>

                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-2">

                    <button class="btn-outline w-1/2 py-2.5 flex items-center justify-center text-sm font-medium"
                        onclick="showLogin(); closeForgetModal();">
                        Login
                    </button>

                    <button class="btn-outline w-1/2 py-2.5 flex items-center justify-center text-sm font-medium"
                        onclick="submitNewPassword()">
                        Reset Password
                    </button>

                </div>

            </div>

        </div>
    </div>

    <script>
        /**
         * 1. AXIOS CONFIGURATION
         * We create two instances to prevent the Laravel CSRF token
         * from being sent to the external BDAPIS, which causes CORS errors.
         */

        // Instance for your Laravel Backend
        const localApi = axios.create({
            baseURL: '/',
            withCredentials: true
        });

        // Instance for Bangladesh Geolocation API (Strictly NO CSRF header)
        const geoApi = axios.create({
            baseURL: 'https://bdapis.com/api/v1.2'
        });

        /* ================= BANGLADESH GEOLOCATION (BDAPIS.COM) ================= */

        async function loadLocations() {
            try {
                // Use geoApi instance
                const res = await geoApi.get('/divisions');
                const divisions = res.data.data;

                document.querySelectorAll('select[id*="division"]').forEach(s => {
                    s.innerHTML = '<option value="">Select Division</option>';
                    divisions.forEach(d => {
                        let opt = document.createElement('option');
                        opt.value = d.division;
                        opt.textContent = d.division;
                        s.appendChild(opt);
                    });
                });
            } catch (err) {
                console.error("Location API Error (Divisions):", err);
            }
        }

        async function populateDistrict(selectDivisionId, selectDistrictId, selectUpazilaId) {
            const divisionName = document.getElementById(selectDivisionId).value;
            const districtSelect = document.getElementById(selectDistrictId);
            const upazilaSelect = document.getElementById(selectUpazilaId);

            districtSelect.innerHTML = '<option value="">Loading...</option>';
            upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';

            if (!divisionName) {
                districtSelect.innerHTML = '<option value="">Select District</option>';
                return;
            }

            try {
                // Use geoApi instance
                const res = await geoApi.get(`/division/${divisionName}`);
                const districts = res.data.data;

                districtSelect.innerHTML = '<option value="">Select District</option>';
                districts.forEach(d => {
                    let opt = document.createElement('option');
                    opt.value = d.district;
                    opt.textContent = d.district;
                    districtSelect.appendChild(opt);
                });
            } catch (err) {
                console.error("Location API Error (Districts):", err);
                districtSelect.innerHTML = '<option value="">Error loading</option>';
            }
        }

        async function populateUpazila(selectDistrictId, selectUpazilaId) {
            const districtName = document.getElementById(selectDistrictId).value;
            const upazilaSelect = document.getElementById(selectUpazilaId);

            upazilaSelect.innerHTML = '<option value="">Loading...</option>';

            if (!districtName) {
                upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
                return;
            }

            try {
                // Use geoApi instance
                const res = await geoApi.get(`/district/${districtName}`);
                const upazillas = res.data.data[0].upazillas;

                upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
                if (upazillas && upazillas.length > 0) {
                    upazillas.forEach(u => {
                        let opt = document.createElement('option');
                        opt.value = u;
                        opt.textContent = u;
                        upazilaSelect.appendChild(opt);
                    });
                }
            } catch (err) {
                console.error("Location API Error (Upazilas):", err);
                upazilaSelect.innerHTML = '<option value="">Error loading</option>';
            }
        }

        /* ================= PASSWORD TOGGLE ================= */
        function togglePassword(id, icon) {
            let input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        /* ================= IMAGE PREVIEW ================= */
        function previewImage(input, id) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let img = document.getElementById(id);
                    img.src = e.target.result;
                    img.classList.remove("hidden");
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        /* ================= SHOW/HIDE LOGIN/REGISTER ================= */
        function showRegister() {
            document.getElementById("loginForm").classList.add("hidden");
            document.getElementById("registerContainer").classList.remove("hidden");

            document.body.classList.remove("login-active");
            document.body.classList.add("register-active"); // ✅ add bottom spacing

            let cardWrapper = document.querySelector('.card-wrapper');
            if (cardWrapper) cardWrapper.style.animation = 'fadeUp 0.5s ease forwards';

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }


        function showLogin() {
            document.getElementById("registerContainer").classList.add("hidden");
            document.getElementById("loginForm").classList.remove("hidden");

            document.body.classList.remove("register-active"); // ✅ remove bottom spacing
            document.body.classList.add("login-active"); // ✅ center login

            let cardWrapper = document.querySelector('.card-wrapper');
            if (cardWrapper) cardWrapper.style.animation = 'fadeUp 0.5s ease forwards';

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.body.classList.add("login-active");
        });

        /* ================= SWITCH REGISTER TYPE ================= */
        function switchType(type) {
            const schoolBtn = document.getElementById("schoolBtn");
            const admissionBtn = document.getElementById("admissionBtn");
            const schoolReg = document.getElementById("schoolRegister");
            const admissionReg = document.getElementById("admissionRegister");

            if (type === 'school') {
                schoolReg.classList.remove("hidden");
                admissionReg.classList.add("hidden");
                schoolBtn.classList.add("switch-active");
                admissionBtn.classList.remove("switch-active");
            } else {
                schoolReg.classList.add("hidden");
                admissionReg.classList.remove("hidden");
                admissionBtn.classList.add("switch-active");
                schoolBtn.classList.remove("switch-active");
            }
        }

        /* ================= STEP NAVIGATION ================= */
        function showStep(step) {
            for (let i = 1; i <= 4; i++) {
                const stepEl = document.getElementById("step" + i);
                const markEl = document.getElementById("step" + i + "mark");
                if (stepEl) stepEl.classList.add("hidden");
                if (markEl) markEl.classList.remove("step-active");
            }
            document.getElementById("step" + step).classList.remove("hidden");
            document.getElementById("step" + step + "mark").classList.add("step-active");
        }

        function validateStep(step) {
            let valid = true;
            document.querySelectorAll("#step" + step + " .required-field").forEach(el => {
                if (!el.value) {
                    valid = false;
                    el.style.borderColor = "red";
                } else {
                    el.style.borderColor = "#d1d5db";
                }
            });
            return valid;
        }

        function nextStep(step) {
            if (!validateStep(step)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Form',
                    text: 'Please fill all required fields first.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            let mark = document.getElementById("step" + step + "mark");
            mark.classList.remove("step-active");
            mark.classList.add("step-complete");
            mark.innerHTML = '<i class="fa fa-check"></i>';
            showStep(step + 1);
        }

        /* ================= ERROR HANDLING ================= */
        function showErrors(errors) {
            Object.keys(errors).forEach(key => {
                const field = document.getElementById(key);
                const errDiv = document.getElementById(key + '_error');
                if (field) {
                    field.classList.add('input-error');
                    if (errDiv) {
                        errDiv.innerText = errors[key][0];
                        errDiv.classList.remove('hidden');
                    }
                }
            });
        }

        function clearErrors() {
            document.querySelectorAll('.error-text').forEach(e => e.classList.add('hidden'));
            document.querySelectorAll('.form-input, select').forEach(f => f.classList.remove('input-error'));
        }

        /* ================= API CALLS ================= */
        async function loginUser() {
            clearErrors();

            const identifier = document.getElementById('login_identifier').value.trim();
            const password = document.getElementById('loginPass').value.trim();
            const remember = document.getElementById('rememberMe').checked;

            if (!identifier || !password) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please enter both your ID and Password.'
                });
                return;
            }

            const btn = document.getElementById('loginSubmitBtn');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = 'Logging in...';

            try {
                await localApi.get('/sanctum/csrf-cookie');

                const res = await localApi.post('/api/login', {
                    identifier,
                    password,
                    remember
                });

                // Store the redirect URL before showing SweetAlert
                const redirectUrl = res.data.redirect;
                console.log('Login successful, will redirect to:', redirectUrl);

                // Store token in localStorage for API calls
                if (res.data.token) {
                    localStorage.setItem('auth_token', res.data.token);
                    // Set default authorization header for future API calls
                    localApi.defaults.headers.common['Authorization'] = `Bearer ${res.data.token}`;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: 'Redirecting to dashboard...',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then((result) => {
                    // This will run when timer completes or user closes alert
                    console.log('SweetAlert closed, redirecting to:', redirectUrl);

                    // Fallback: if SweetAlert doesn't work, redirect anyway
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 500);

                    window.location.href = redirectUrl;
                });

            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = originalText;

                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: err.response?.data?.message || err.message || 'Something went wrong'
                });
            }
        }

        function registerSchool() {

            clearErrors();

            const btn = document.getElementById("registerBtn");
            const text = document.getElementById("registerBtnText");
            const loader = document.getElementById("registerBtnLoader");

            // START LOADER
            loader.classList.remove("hidden");
            text.innerText = "Processing...";
            btn.disabled = true;

            let formData = new FormData();
            formData.append('school_name', document.getElementById('school_name').value);
            formData.append('division', document.getElementById('division').value);
            formData.append('district', document.getElementById('district').value);
            formData.append('upazila', document.getElementById('upazila').value);
            formData.append('village', document.getElementById('village').value);

            formData.append('eiin_number', document.getElementById('eiin_number').value);
            formData.append('mobile', document.getElementById('mobile').value);
            formData.append('email', document.getElementById('email').value);

            formData.append('package_id', document.getElementById('package_id').value);
            formData.append('duration_months', document.getElementById('duration_months').value);
            formData.append('start_date', document.getElementById('start_date').value);

            formData.append('password', document.getElementById('schoolPass').value);
            formData.append('password_confirmation', document.getElementById('schoolPass2').value);

            let logo = document.getElementById('school_logo').files[0];
            if (logo) formData.append('logo', logo);

            localApi.post('/api/school-register', formData)

                .then(res => {

                    // STOP LOADER
                    loader.classList.add("hidden");
                    text.innerText = "Registration";
                    btn.disabled = false;

                    window.location.href = res.data.redirect;

                })

                .catch(err => {

                    // STOP LOADER
                    loader.classList.add("hidden");
                    text.innerText = "Registration";
                    btn.disabled = false;

                    if (err.response?.data?.errors) {
                        showErrors(err.response.data.errors);
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: err.response?.data?.message || 'Something went wrong',
                        confirmButtonColor: '#dc2626'
                    });

                });
        }

        function registerAdmission() {
            clearErrors();
            let formData = new FormData();
            // STEP 1
            ['a_division', 'a_district', 'a_upazila', 'a_school', 'a_class', 'a_group', 'a_session', 'a_fee', 'a_date']
            .forEach(id => formData.append(id, document.getElementById(id).value));
            // STEP 2
            ['p_school', 'p_class', 'p_group', 'p_section', 'p_session', 'last_exam_result']
            .forEach(id => formData.append(id, document.getElementById(id).value));
            // STEP 3
            ['g_name', 'g_relation', 'g_division', 'g_district', 'g_upazila', 'g_village', 'g_mobile']
            .forEach(id => formData.append(id, document.getElementById(id).value));
            // STEP 4
            ['student_name', 'father_name', 'mother_name', 'c_division', 'c_district', 'c_upazila',
                'c_village',
                'p_division2', 'p_district2', 'p_upazila2', 'p_village2', 'student_mobile', 'studentPass', 'studentPass2'
            ]
            .forEach(id => formData.append(
                id === 'studentPass' ? 'password' : id === 'studentPass2' ? 'password_confirmation' : id,
                document.getElementById(id).value
            ));
            let studentImg = document.getElementById('student_image').files[0];
            if (studentImg) formData.append('image', studentImg);

            // Use localApi instance
            localApi.post('/api/admission-register', formData)
                .then(res => window.location.href = res.data.redirect)
                .catch(err => {

                    if (err.response?.data?.errors) {
                        showErrors(err.response.data.errors);
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Admission Failed',
                        text: err.response?.data?.message || 'Something went wrong',
                        confirmButtonColor: '#dc2626'
                    });
                });

        }

        /* ================= EVENT LISTENERS ================= */
        document.addEventListener('DOMContentLoaded', function() {
            loadLocations();

            const locationMapping = [
                ['a_division', 'a_district', 'a_upazila'],
                ['g_division', 'g_district', 'g_upazila'],
                ['c_division', 'c_district', 'c_upazila'],
                ['p_division2', 'p_district2', 'p_upazila2'],
                ['division', 'district', 'upazila']
            ];

            locationMapping.forEach(ids => {
                const divEl = document.getElementById(ids[0]);
                const distEl = document.getElementById(ids[1]);

                if (divEl) {
                    divEl.addEventListener('change', () => populateDistrict(ids[0], ids[1], ids[2]));
                }
                if (distEl) {
                    distEl.addEventListener('change', () => populateUpazila(ids[1], ids[2]));
                }
            });
        });


        /* ================= PACKAGE SCRIPT ================= */
        let selectedPackage = null;

        function updatePackageCard() {
            const dropdown = document.getElementById('package_dropdown');
            const selectedOption = dropdown.options[dropdown.selectedIndex];

            if (!selectedOption.value) {
                document.getElementById('packageCard').classList.add('hidden');
                selectedPackage = null;
                document.getElementById('package_id').value = '';
                calculatePrice();
                return;
            }

            selectedPackage = {
                id: parseInt(selectedOption.value),
                price: parseFloat(selectedOption.dataset.price),
                students: parseInt(selectedOption.dataset.students),
                teachers: parseInt(selectedOption.dataset.teachers),
                discount: parseFloat(selectedOption.dataset.discount),
                sms: parseInt(selectedOption.dataset.sms),
                trial: parseInt(selectedOption.dataset.trial || 0),
                monthly: parseFloat(selectedOption.dataset.monthly || 0),
                type: selectedOption.text
            };

            document.getElementById('package_id').value = selectedPackage.id;

            document.getElementById('cardType').innerText = selectedPackage.type;
            document.getElementById('cardStudents').innerText = `Students: ${selectedPackage.students}`;
            document.getElementById('cardTeachers').innerText = `Teachers: ${selectedPackage.teachers}`;
            document.getElementById('cardSMS').innerText =
                `SMS Limit: ${selectedPackage.sms.toLocaleString()} (One Time)`;
            document.getElementById('cardTrial').innerText =
                `Free Trial: ${selectedPackage.trial} Days`;
            document.getElementById('cardMonthly').innerText =
                `Monthly Fee: ৳${selectedPackage.monthly}`;
            document.getElementById('cardDiscount').innerText =
                selectedPackage.discount > 0 ?
                `${selectedPackage.discount}% Annual Discount (Only for 12 Months)` :
                '';

            document.getElementById('packageCard').classList.remove('hidden');

            calculatePrice();
        }

        function calculatePrice() {
            if (!selectedPackage) {
                document.getElementById('originalPrice').innerText = '৳0';
                document.getElementById('finalPrice').innerText = '৳0';
                return;
            }

            let duration = parseInt(document.getElementById('duration_months').value);

            // Total price
            let total = selectedPackage.price *
                selectedPackage.students *
                duration;

            // Discounted price logic
            let discounted = 0;

            if (duration === 12 && selectedPackage.discount > 0) {
                discounted = total - (total * selectedPackage.discount / 100);
            }

            document.getElementById('originalPrice').innerText =
                `৳${total.toFixed(2)}`;

            document.getElementById('finalPrice').innerText =
                `৳${discounted.toFixed(2)}`;
        }



        /* ================= ADMISSION DROPDOWN LOGIC ================= */

        async function fetchApprovedSchools() {
            try {
                const res = await localApi.get('/api/get-approved-schools');
                const schoolSelect = document.getElementById('a_school');
                schoolSelect.innerHTML = '<option value="">Select School</option>';

                res.data.forEach(school => {
                    let opt = document.createElement('option');
                    opt.value = school.id;
                    opt.textContent = school.school_name;
                    schoolSelect.appendChild(opt);
                });
            } catch (err) {
                console.error("Error fetching schools", err);
            }
        }

        async function updateClasses(schoolId) {
            const classSelect = document.getElementById('a_class');
            classSelect.innerHTML = '<option value="">Loading...</option>';

            try {
                const res = await localApi.get(`/api/get-school-classes/${schoolId}`);
                classSelect.innerHTML = '<option value="">Select Class</option>';
                res.data.forEach(c => {
                    let opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.class_name;
                    classSelect.appendChild(opt);
                });
            } catch (err) {
                classSelect.innerHTML = '<option value="">Error</option>';
            }
        }

        async function updateGroupsAndSessions(classId) {
            const schoolId = document.getElementById('a_school').value;
            const groupSelect = document.getElementById('a_group');
            const sessionSelect = document.getElementById('a_session');
            const feeSelect = document.getElementById('a_fee');

            // Fetch Groups
            localApi.get(`/api/get-groups/${schoolId}/${classId}`).then(res => {
                groupSelect.innerHTML = '<option value="">Select Group</option>';
                res.data.forEach(g => {
                    let opt = document.createElement('option');
                    opt.value = g.id;
                    opt.textContent = g.group_name;
                    groupSelect.appendChild(opt);
                });
            });

            // Fetch Sessions
            localApi.get(`/api/get-sessions/${schoolId}/${classId}`).then(res => {
                sessionSelect.innerHTML = '<option value="">Select Session</option>';
                res.data.forEach(s => {
                    let opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.session_year;
                    sessionSelect.appendChild(opt);
                });
            });

            // Fetch Fees
            localApi.get(`/api/get-fees/${schoolId}/${classId}`).then(res => {
                feeSelect.innerHTML = '<option value="">Select Fee Type</option>';
                res.data.forEach(f => {
                    let opt = document.createElement('option');
                    opt.value = f.amount; // Store amount as value for easy submission
                    opt.textContent = `${f.fee_type_name} - ৳${f.amount}`;
                    feeSelect.appendChild(opt);
                });
            });
        }

        // Add to Event Listeners section
        document.addEventListener('DOMContentLoaded', function() {
            fetchApprovedSchools();

            document.getElementById('a_school').addEventListener('change', function() {
                if (this.value) updateClasses(this.value);
            });

            document.getElementById('a_class').addEventListener('change', function() {
                if (this.value) updateGroupsAndSessions(this.value);
            });
        });


        /* ================= FORGET PASSWORD RESET MODAL LOGIC ================= */
        let resetMobileNumber = '';
        let otpExpiryInterval = null;

        // Get CSRF token
        const getCsrfToken = () => {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        };

        // Global Swal Helper
        const showToast = (msg, icon = 'error') => {
            Swal.fire({
                title: icon === 'error' ? 'Oops!' : 'Success',
                text: msg,
                icon: icon,
                confirmButtonColor: '#3085d6'
            });
        };

        // Open modal
        function openForgetModal() {
            document.getElementById('forgetPasswordModal').classList.remove('hidden');
            resetModalSteps();
        }

        // Close modal
        function closeForgetModal() {
            document.getElementById('forgetPasswordModal').classList.add('hidden');
            clearOtpTimer();
        }

        // Reset modal steps
        function resetModalSteps() {
            document.getElementById('step_request_otp').classList.remove('hidden');
            document.getElementById('step_verify_otp').classList.add('hidden');
            document.getElementById('step_new_password').classList.add('hidden');

            // Reset inputs
            document.getElementById('reset_mobile').value = '';
            document.getElementById('reset_otp').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';

            clearOtpTimer();
        }

        // ================= STEP 1: SEND OTP =================
        async function sendResetOTP() {
            const btn = event.target;
            const mobile = document.getElementById('reset_mobile').value.trim();

            if (!mobile) {
                showToast("Please enter your mobile number");
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';

            try {
                const response = await fetch('/api/password/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        mobile: mobile
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    resetMobileNumber = mobile;
                    document.getElementById('step_request_otp').classList.add('hidden');
                    document.getElementById('step_verify_otp').classList.remove('hidden');

                    startOtpTimer(2); // 2-minute countdown

                    Swal.fire('Sent!', 'OTP has been sent to your mobile.', 'success');
                } else {
                    showToast(data.message || "Something went wrong");
                }
            } catch (error) {
                showToast("Network error. Please try again.");
            } finally {
                btn.disabled = false;
                btn.innerText = "Send OTP";
            }
        }

        // ================= OTP TIMER =================
        function startOtpTimer(minutes) {
            let remaining = minutes * 60; // total seconds
            const timerEl = document.getElementById('otp_timer');

            clearOtpTimer();

            otpExpiryInterval = setInterval(() => {
                const m = Math.floor(remaining / 60).toString().padStart(2, '0');
                const s = (remaining % 60).toString().padStart(2, '0');
                timerEl.innerText = `OTP expires in ${m}:${s}`;

                if (remaining <= 0) {
                    clearOtpTimer();
                    timerEl.innerText = 'OTP has expired. Please resend.';
                }
                remaining--;
            }, 1000);
        }

        function clearOtpTimer() {
            if (otpExpiryInterval) {
                clearInterval(otpExpiryInterval);
                otpExpiryInterval = null;
            }
        }

        // ================= STEP 2: VERIFY OTP =================
        async function verifyResetOTP() {
            const btn = event.target;
            const otp = document.getElementById('reset_otp').value.trim();

            if (!otp) {
                showToast("Please enter the 4-digit OTP");
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Verifying...';

            try {
                const response = await fetch('/api/password/verify-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        mobile: resetMobileNumber,
                        otp: otp
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    document.getElementById('step_verify_otp').classList.add('hidden');
                    document.getElementById('step_new_password').classList.remove('hidden');
                    clearOtpTimer();
                } else {
                    showToast(data.message || "Invalid OTP");
                }
            } catch (error) {
                showToast("Verification failed.");
            } finally {
                btn.disabled = false;
                btn.innerText = "Verify OTP";
            }
        }

        // ================= STEP 3: RESET PASSWORD =================
        async function submitNewPassword() {
            const btn = event.target;
            const password = document.getElementById('new_password').value;
            const confirm = document.getElementById('new_password_confirmation').value;
            const otp = document.getElementById('reset_otp').value;

            if (!password || password.length < 6) {
                showToast("Password must be at least 6 characters.");
                return;
            }
            if (password !== confirm) {
                showToast("Passwords do not match!");
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';

            try {
                const response = await fetch('/api/password/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        mobile: resetMobileNumber,
                        otp: otp,
                        password: password,
                        password_confirmation: confirm
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your password has been updated.',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    showToast(data.message || "Reset failed");
                }
            } catch (error) {
                showToast("Error updating password.");
            } finally {
                btn.disabled = false;
                btn.innerText = "Reset Password";
            }
        }

        // ================= PASSWORD TOGGLE =================
        function togglePassword(inputId, iconEl) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                iconEl.classList.remove('fa-eye');
                iconEl.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                iconEl.classList.remove('fa-eye-slash');
                iconEl.classList.add('fa-eye');
            }
        }


        // Global Axios Interceptor (Works for all API calls)
        window.axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response && error.response.status === 401) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Session Expired',
                        text: 'Your session has timed out. Please login again to continue.',
                        confirmButtonText: 'Go to Login',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/'; // Redirect to landing/login page
                        }
                    });
                }
                return Promise.reject(error);
            }
        );

        //Custom Select System link the UI to existing logic Script
        document.addEventListener('DOMContentLoaded', function() {
            function initCustomSelects() {
                document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
                    const select = wrapper.querySelector('select');
                    const optionsContainer = wrapper.querySelector('.custom-select-options');
                    const display = wrapper.querySelector('.custom-select-display span');

                    // Sync Options from Select to Custom UI
                    const syncOptions = () => {
                        optionsContainer.innerHTML = '';
                        Array.from(select.options).forEach(opt => {
                            const div = document.createElement('div');
                            div.className = 'custom-option';
                            div.innerText = opt.text;
                            div.onclick = (e) => {
                                e.stopPropagation();
                                select.value = opt.value;
                                display.innerText = opt.text;
                                display.classList.remove('placeholder');
                                wrapper.classList.remove('active');
                                optionsContainer.style.display = 'none';
                                // Trigger existing onchange logic
                                select.dispatchEvent(new Event('change'));
                            };
                            optionsContainer.appendChild(div);
                        });
                    };

                    syncOptions();

                    // Handle dropdown toggle
                    wrapper.querySelector('.custom-select-display').onclick = (e) => {
                        e.stopPropagation();
                        const isOpen = optionsContainer.style.display === 'block';

                        // Close all other dropdowns
                        document.querySelectorAll('.custom-select-options').forEach(el => el.style
                            .display = 'none');
                        document.querySelectorAll('.custom-select-wrapper').forEach(el => el.classList
                            .remove('active'));

                        if (!isOpen) {
                            syncOptions(); // Re-sync in case options changed (like districts)
                            optionsContainer.style.display = 'block';
                            wrapper.classList.add('active');
                        }
                    };
                });

                // Close on outside click
                window.onclick = () => {
                    document.querySelectorAll('.custom-select-options').forEach(el => el.style.display =
                        'none');
                    document.querySelectorAll('.custom-select-wrapper').forEach(el => el.classList.remove(
                        'active'));
                };
            }

            initCustomSelects();

            // Observer to re-init if division/district scripts add new options dynamically
            const observer = new MutationObserver(() => initCustomSelects());
            document.querySelectorAll('.hidden-select').forEach(s => observer.observe(s, {
                childList: true
            }));
        });


    </script>
</body>

</html>
