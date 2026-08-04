<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <link   href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"rel="stylesheet">
   <link rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('admin/style.css') }}">
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6fb;
            margin: 0;
        }
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 16rem;
            display: flex;
            flex-direction: column;
            z-index: 10;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
            transition: transform .35s cubic-bezier(.4, 0, .2, 1);
        }
        @media(min-width:768px) {
            .sidebar {
                transform: translateX(0) !important;
            }
        }
        @media(max-width:767px) {
            .sidebar {
                top: 4rem;
                height: calc(100vh - 4rem);
            }
        }
        .sidebar-header-divider {
            border-bottom: 1px solid #e5e7eb;
            margin: 0 1.5rem;
        }
        .sidebar-item,
        .sidebar-subitem,
        .sidebar-group-toggle {
            margin-bottom: 6px;
            color: #334155;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .sidebar-item,
        .sidebar-group-toggle,
        .nav-header {
            padding-left: 14px;
        }
        .sidebar-item {
            font-weight: 500;
            gap: 12px;
            padding: 8px 14px;
        }
        .sidebar-group {
            margin-bottom: 8px;
        }
        .sidebar-group-toggle {
            justify-content: space-between;
            font-weight: 500;
            padding-right: 14px;
            padding-top: 8px;
            padding-bottom: 8px;
            border-radius: 10px;
        }
        .sidebar-group-toggle span {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }
        .sidebar-subitem {
            padding-left: 36px;
            padding-top: 6px;
            padding-bottom: 6px;
            font-weight: 400;
            position: relative;
            gap: 12px;
        }
        .sidebar-group-content {
            display: none;
            flex-direction: column;
            margin-top: 4px;
            position: relative;
        }
        .sidebar-group.open .sidebar-group-content {
            display: flex;
        }
        .sidebar-group-content::before {
            content: '';
            position: absolute;
            left: 26px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }
        .sidebar-subitem::before {
            content: '';
            position: absolute;
            left: 21px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
        }
        .sidebar-subitem.active::before {
            background: #1e40af;
        }
        .sidebar-subitem i,
        .sidebar-item i,
        .sidebar-group-toggle i {
            width: 16px;
            flex-shrink: 0;
        }
        .sidebar-item:hover,
        .sidebar-subitem:hover,
        .sidebar-group-toggle:hover {
            background: #f1f5f9;
            color: #1e40af;
        }
        .sidebar-item.active,
        .sidebar-subitem.active {
            background: #eaf1ff;
            color: #1e40af;
            box-shadow: 0 4px 12px rgba(30, 64, 175, .08);
        }
        .sidebar-item.active i,
        .sidebar-subitem.active i {
            color: #1e40af;
        }
        .nav-header {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            margin-top: 1.5rem;
            margin-bottom: .5rem;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .sidebar-mobile-hidden {
            transform: translateX(-100%);
        }
        .sidebar-mobile-show {
            transform: translateX(0);
        }
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            height: 4rem;
            left: 0;
            right: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }
        @media(min-width:768px) {
            .topbar {
                left: 16rem;
                width: calc(100% - 16rem);
                padding: 0 2rem;
            }
        }
        .main-scroll {
            overflow-y: auto;
            height: 100vh;
            padding-top: 4rem;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        @media(min-width:768px) {
            .main-scroll {
                padding-left: 2rem;
                padding-right: 2rem;
                margin-left: 16rem;
            }
        }
        .sidebar .logout-wrapper {
            margin-top: auto;
            margin-bottom: 2rem;
        }
        .global-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 40;
            display: none;
        }
        html {
            scroll-behavior: smooth;
        }
        .sidebar-group-toggle i {
            transition: transform .25s ease;
        }
        .sidebar-group.open .sidebar-group-toggle i {
            transform: rotate(90deg);
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            z-index: 50;
        }
        .dropdown-menu.show {
            display: block;
        }
        .profile-img-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #eef2ff;
        }
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            z-index: 10;
        }
                        /* ========================================
                Sidebar Configuration Submenu
                ======================================== */

                .submenu {
                    padding-left: 18px;
                    margin-top: 6px;
                }

                .submenu .nav-item {
                    width: 100%;
                }

                .submenu-link {
                    display: flex !important;
                    align-items: center;
                    width: 100%;
                    padding: 9px 12px !important;
                    margin-bottom: 4px;
                    border-radius: 8px;
                    font-size: 14px;
                    color: #64748b !important;
                    text-decoration: none;
                    transition: all 0.2s ease;
                }

                .submenu-link i {
                    width: 20px;
                    font-size: 15px;
                }

                .submenu-link:hover {
                    background: #f1f5f9;
                    color: #4f46e5 !important;
                }

                .submenu-link.active {
                    background: #eef2ff;
                    color: #4f46e5 !important;
                    font-weight: 600;
                }

                /* Configuration arrow animation */
                .nav-link[aria-expanded="true"] .bi-chevron-down {
                    transform: rotate(180deg);
                }

                .bi-chevron-down {
                    transition: transform 0.2s ease;
                }
    </style>

    @stack('styles')
</head>

<body>
    <div class="flex">

        <aside id="sidebar" class="sidebar sidebar-mobile-hidden">

            <div class="p-6 text-center">
                <h2 class="text-2xl font-black tracking-tight text-slate-800 mt-2">
                    SCHOOL<span class="text-blue-600">SAAS</span>
                </h2>
                <p class="text-[10px] font-bold uppercase tracking-[3px] text-slate-400">
                    Admin Panel
                </p>
            </div>

            <div class="sidebar-header-divider"></div>

            <nav class="flex-1 overflow-y-auto custom-scrollbar p-3" id="menuNav">

                <a href="{{ route('admin.dashboard') }}" data-title="Dashboard" data-link class="sidebar-item">
                    <i class="fas fa-th-large w-4"></i> Dashboard
                </a>

                    <p class="nav-header">Management</p>

                    {{-- Configuration Menu --}}
                    <div class="sidebar-group {{ request()->routeIs('admin.configuration.*') ? 'open' : '' }}">

                        <div class="sidebar-group-toggle">
                            <span> <i class="fas fa-cog w-4"></i> Configuration </span>
                            <i class="fas fa-chevron-right text-xs"></i>
                        </div>

                        <div class="sidebar-group-content">

                                {{-- Fingerprint Device --}}
                            <a href="{{ route('admin.configuration.devices.index') }}" data-title="Fingerprint Device" data-link
                               class="sidebar-subitem {{ request()->routeIs('admin.configuration.devices.*') ? 'active' : '' }}">
                                <i class="fas fa-microchip"></i>Device
                            </a>
                         {{-- Teacher --}}
                            <a href="{{ route('admin.configuration.teachers') }}" data-title="Teacher" data-link
                               class="sidebar-subitem {{ request()->routeIs('admin.configuration.teachers.') ? 'active' : '' }}">
                                <i class="fas fa-chalkboard-teacher"></i> Teacher
                            </a>
                        </div>

                    </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span><i class="fas fa-school w-4"></i> Schools</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                    <div class="sidebar-group-content">
                        <a href="{{ url('/admin/approval-schools') }}" data-title="Approval School" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-check-circle"></i> Approval School
                        </a>
                        <a href="{{ url('/admin/registered-schools') }}" data-title="Registered Schools" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-school"></i> Registered Schools
                        </a>
                    </div>
                </div>

                <a href="{{ url('/admin/create-plan') }}" data-title="Create Plan" data-link class="sidebar-item">
                    <i class="fas fa-plus-circle w-4"></i> Create Plan
                </a>

                <a href="{{ route('admin.subscriptions') }}" data-title="Subscriptions" data-link class="sidebar-item">
                    <i class="fas fa-file-invoice-dollar w-4"></i> Subscriptions
                </a>

                <a href="{{ route('admin.blogs.index') }}" data-title="Blogs" data-link class="sidebar-item">
                    <i class="fas fa-newspaper w-4"></i> Blogs
                </a>

                <a href="{{ route('admin.features.index') }}" data-title="Features" data-link class="sidebar-item">
                    <i class="fas fa-layer-group w-4"></i> Features
                </a>

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span><i class="fas fa-circle-info w-4"></i> About</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                    <div class="sidebar-group-content">
                        <a href="{{ route('admin.about.people.index') }}" data-title="About People" data-link class="sidebar-subitem">
                            <i class="fas fa-users"></i> About People
                        </a>
                        <a href="{{ route('admin.about.settings.edit') }}" data-title="About Settings" data-link class="sidebar-subitem">
                            <i class="fas fa-sliders-h"></i> About Settings
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.showcases.index') }}" data-title="Showcases" data-link class="sidebar-item">
                    <i class="fas fa-images w-4"></i> Showcases
                </a>

                <a href="{{ route('admin.demo-requests.index') }}" data-title="Demo Requests" data-link class="sidebar-item">
                    <i class="fas fa-calendar-check w-4"></i> Demo Requests
                </a>

                <a href="{{ route('admin.dashboard-news.index') }}" data-title="Dashboard News" data-link class="sidebar-item">
                    <i class="fas fa-bullhorn w-4"></i> Dashboard News
                </a>

                <a href="{{ url('/admin/sms-packages') }}" data-title="SMS Packages" data-link class="sidebar-item">
                    <i class="fas fa-sms w-4"></i> SMS Package
                </a>

                <a href="{{ url('/admin/sms-package-activation-requests') }}"
                    data-title="SMS Package Activation Request" data-link class="sidebar-item">
                    <i class="fas fa-check-circle w-4"></i> SMS Package Activation Request
                </a>

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span><i class="fas fa-sms w-4"></i> SMS Management</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                    <div class="sidebar-group-content">
                        <a href="{{ route('admin.sms-credentials') }}" data-title="SMS Credentials" data-link class="sidebar-subitem">
                            <i class="fas fa-key"></i> SMS Credentials
                        </a>
                        <a href="{{ route('admin.sms-templates') }}" data-title="SMS Templates" data-link class="sidebar-subitem">
                            <i class="fas fa-file-alt"></i> SMS Templates
                        </a>
                        <a href="{{ route('admin.sms-activations') }}" data-title="SMS Activations" data-link class="sidebar-subitem">
                            <i class="fas fa-toggle-on"></i> Activation List
                        </a>
                    </div>
                </div>

                <p class="nav-header">System</p>

                <div class="sidebar-group open">
                    <div class="sidebar-group-toggle">
                        <span><i class="fas fa-cogs w-4"></i> Dynamic Operation</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                    <div class="sidebar-group-content">
                        <a href="{{ url('/admin/dynamic-operation/general') }}" data-title="General Operation" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-sliders-h"></i> General Operation
                        </a>
                        <a href="{{ url('/admin/dynamic-operation/home') }}" data-title="Home Operation" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-house"></i> Home Operation
                        </a>
                        <a href="{{ route('admin.dynamic-operation.translations') }}" data-title="Translation Operation" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-language"></i> Translation Operation
                        </a>
                    </div>
                </div>

            </nav>

            <div class="p-4 logout-wrapper">
                <button id="logoutBtn"
                    class="w-full flex items-center justify-center gap-2 p-2 border border-red-500 text-red-500 text-sm font-semibold">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </aside>

        <div class="flex-1 flex flex-col">

            <header class="topbar">

                <div class="flex items-center gap-3">

                    <button id="hamburger" class="md:hidden text-blue-600 text-xl">
                        <i id="hamburgerIcon" class="fas fa-bars"></i>
                    </button>

                    <div class="flex items-center text-slate-500 text-sm">
                        <span>Admin</span>
                        <i class="fas fa-chevron-right mx-2 text-[10px]"></i>
                        <span id="pageTitle" class="font-semibold text-slate-800">
                            @yield('page-title', 'Dashboard')
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4 relative z-50">

                    <div class="relative">
                        <button id="notificationBtn" class="relative text-gray-400 hover:text-gray-700">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div id="notificationBox"
                            class="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-lg py-2 hidden">
                            <p class="px-4 py-2 text-sm text-gray-700">No notifications</p>
                        </div>
                    </div>

                    <div class="dropdown relative">
                        <button id="profileBtn" class="flex items-center gap-2">
                            <img id="navAvatar"
                                src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('images/avatar.svg') }}"
                                class="w-8 h-8 rounded-full object-cover">
                            <span class="hidden md:block text-sm font-semibold">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <ul id="profileMenu" class="dropdown-menu bg-white shadow-lg rounded-lg py-2 w-48">
                            <li><a href="javascript:void(0)" onclick="openProfileModal()"
                                    class="dropdown-item px-4 py-2 text-sm hover:bg-gray-100">Profile</a>
                            </li>
                            <li>
                                <hr class="my-1">
                            </li>
                            <li><a href="#" id="logoutBtnDropdown"
                                    class="dropdown-item px-4 py-2 text-sm hover:bg-gray-100">Logout</a></li>
                        </ul>
                    </div>

                </div>

            </header>

            <main class="main-scroll" id="mainContent">
                @yield('content')
            </main>

        </div>
    </div>

    <div class="modal fade premium-modal" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mx-auto" style="width: 95%; max-width: 480px;">
            <div class="modal-content modal-content-sharp shadow-2xl"
                style="border-radius: 0; border: 1.5px solid #1e293b !important; background: #fff;">

                <form id="profileUpdateForm" enctype="multipart/form-data">
                    @csrf
                    <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between bg-white">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-7 h-7 bg-slate-900 text-white flex-shrink-0 flex items-center justify-center">
                                <i class="fas fa-user-circle text-md"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-[11px] font-black text-gray-800 uppercase tracking-widest">Account
                                    Profile</h3>
                            </div>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-800 transition-colors shadow-none"
                            data-bs-dismiss="modal">
                            <i class="mdi mdi-close text-lg"></i>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="flex flex-col items-center mb-4">
                            <div class="relative">
                                <div class="image-preview-box"
                                    style="width: 70px; height: 70px; border: 1.5px solid #cbd5e1 !important; background: #f8fafc; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                    <img id="previewImg"
                                        src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('images/avatar.svg') }}"
                                        class="w-full h-full object-cover">
                                </div>
                                <label for="profile_image"
                                    class="absolute -bottom-1 -right-1 bg-blue-600 text-white w-6 h-6 flex items-center justify-center cursor-pointer shadow-md hover:bg-black border-2 border-white transition-all">
                                    <i class="fas fa-camera text-[9px]"></i>
                                </label>
                                <input type="file" id="profile_image" name="profile_image" class="hidden"
                                    accept="image/*"
                                    onchange="document.getElementById('previewImg').src = window.URL.createObjectURL(this.files[0])">
                            </div>
                            <h5 class="mt-2 text-[10px] font-black text-slate-800 uppercase tracking-wide">
                                {{ auth()->user()->name }}</h5>
                        </div>

                        <div class="space-y-3">
                            <div class="w-full">
                                <label
                                    class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Full
                                    Name</label>
                                <input type="text" name="name" value="{{ auth()->user()->name }}"
                                    class="form-control"
                                    style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;"
                                    required>
                            </div>

                            <div class="w-full">
                                <label
                                    class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Email
                                    Address</label>
                                <input type="email" value="{{ auth()->user()->email }}" class="form-control"
                                    style="border: 1.5px solid #e2e8f0 !important; height: 34px; padding: 0 10px; font-size: 12px; background-color: #f8fafc; color: #94a3b8; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;"
                                    readonly>
                            </div>

                            <div class="w-full">
                                <label
                                    class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Mobile
                                    Number</label>
                                <input type="text" name="mobile" value="{{ auth()->user()->mobile }}"
                                    class="form-control"
                                    style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;">
                            </div>

                            <div class="w-full">
                                <label
                                    class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">New
                                    Password</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="passInput" class="form-control"
                                        style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;">
                                    <i class="fas fa-eye toggle-password" data-target="passInput"
                                        style="position: absolute; right: 10px; top: 9px; cursor: pointer; color: #94a3b8; font-size: 11px;"></i>
                                </div>
                            </div>

                            <div class="w-full">
                                <label
                                    class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Confirm
                                    Password</label>
                                <div class="position-relative">
                                    <input type="password" name="password_confirmation" id="confirmInput"
                                        class="form-control"
                                        style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; display: block; width: 100%; box-sizing: border-box;">
                                    <i class="fas fa-eye toggle-password" data-target="confirmInput"
                                        style="position: absolute; right: 10px; top: 9px; cursor: pointer; color: #94a3b8; font-size: 11px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2 border-t border-gray-100 p-3 bg-gray-50/50">
                        <button type="button"
                            class="w-full sm:w-auto px-4 py-2 text-[9px] font-black uppercase tracking-widest transition-all"
                            style="border: 1.5px solid #64748b !important; background: transparent; color: #64748b; border-radius: 0;"
                            data-bs-dismiss="modal">Discard</button>

                        <button type="submit" id="saveProfileBtn"
                            class="w-full sm:w-auto px-6 py-2 text-[9px] font-black uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-blue-700 transition-all shadow-md"
                            style="border: 1.5px solid #2563eb !important; background: #2563eb; color: #ffffff; border-radius: 0;">
                            <i class="mdi mdi-check-circle-outline text-xs"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="globalModalBackdrop" class="global-modal-backdrop"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function getStoredAuthToken() {
            const fromLocalStorage = localStorage.getItem('auth_token')
            if (fromLocalStorage) {
                return fromLocalStorage
            }

            const cookieMatch = document.cookie.match(/(?:^|;\s*)auth_token=([^;]+)/)
            return cookieMatch ? decodeURIComponent(cookieMatch[1]) : ''
        }

        function adminApiHeaders(existingHeaders = {}, requestUrl = '') {
            const headers = new Headers(existingHeaders || {})

            if (!headers.has('Accept')) {
                headers.set('Accept', 'application/json')
            }

            if (!headers.has('X-Requested-With')) {
                headers.set('X-Requested-With', 'XMLHttpRequest')
            }

            const token = getStoredAuthToken()
            if (token && !headers.has('Authorization')) {
                headers.set('Authorization', `Bearer ${token}`)
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            if (csrfToken && !headers.has('X-CSRF-TOKEN')) {
                headers.set('X-CSRF-TOKEN', csrfToken)
            }

            return headers
        }

        $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
            const url = options.url || ''

            if (url.startsWith('/api/') || url.startsWith('{{ url('/api') }}')) {
                jqXHR.setRequestHeader('Accept', 'application/json')
                jqXHR.setRequestHeader('X-Requested-With', 'XMLHttpRequest')

                const token = getStoredAuthToken()
                if (token) {
                    jqXHR.setRequestHeader('Authorization', `Bearer ${token}`)
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                if (csrfToken) {
                    jqXHR.setRequestHeader('X-CSRF-TOKEN', csrfToken)
                }
            }
        })

        const nativeFetch = window.fetch.bind(window)
        window.fetch = function(resource, config = {}) {
            const requestUrl = typeof resource === 'string' ? resource : resource.url
            const shouldAttachAuth = requestUrl.startsWith('/api/') ||
                requestUrl.startsWith('{{ url('/api') }}')

            if (!shouldAttachAuth) {
                return nativeFetch(resource, config)
            }

            return nativeFetch(resource, {
                ...config,
                credentials: 'same-origin',
                headers: adminApiHeaders(config.headers, requestUrl),
            })
        }

        const sidebar = document.getElementById('sidebar')
        const hamburger = document.getElementById('hamburger')
        const icon = document.getElementById('hamburgerIcon')
        const menuNav = document.getElementById('menuNav')
        const pageTitle = document.getElementById('pageTitle')
        const logoutBtn = document.getElementById('logoutBtn')

        /* Mobile toggle */
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-mobile-show')
            sidebar.classList.toggle('sidebar-mobile-hidden')
            icon.classList.toggle('fa-bars')
            icon.classList.toggle('fa-times')
        })

        /* Sidebar group toggle */
        document.querySelectorAll('.sidebar-group-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const group = toggle.parentElement
                group.classList.toggle('open')
            })
        })

        /* Highlight active menu */
        function highlightActiveMenu() {
            const links = menuNav.querySelectorAll('[data-link]')
            const current = window.location.pathname

            links.forEach(link => {
                link.classList.remove('active')
                if (current === new URL(link.href).pathname) {
                    link.classList.add('active')
                    pageTitle.innerText = link.getAttribute('data-title')
                    const parent = link.closest('.sidebar-group')
                    if (parent) parent.classList.add('open')
                    menuNav.scrollTop = link.offsetTop - 100
                }
            })
        }

        window.addEventListener('DOMContentLoaded', highlightActiveMenu)

        /* Profile dropdown */
        const profileBtn = document.getElementById('profileBtn')
        const profileMenu = document.getElementById('profileMenu')
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('show')
        })
        document.addEventListener('click', () => profileMenu.classList.remove('show'));

        /* Notifications */
        const notificationBtn = document.getElementById('notificationBtn')
        const notificationBox = document.getElementById('notificationBox')
        notificationBtn.addEventListener('click', () => {
            notificationBox.classList.toggle('hidden')
        })

        /* --- Profile Modal Logic --- */
        function openProfileModal() {
            new bootstrap.Modal(document.getElementById('profileModal')).show();
        }

        // Image Preview
        $('#profile_image').on('change', function() {
            const reader = new FileReader();
            reader.onload = (e) => $('#previewImg').attr('src', e.target.result);
            reader.readAsDataURL(this.files[0]);
        });

        // Password Eye Toggle
        $('.toggle-password').on('click', function() {
            const input = $('#' + $(this).data('target'));
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Update via AJAX
        $('#profileUpdateForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#saveProfileBtn');
            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: '/api/admin/profile-update',
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': `Bearer ${getStoredAuthToken()}`
                },
                success: function(res) {
                    if (res.success) {
                        $('#navAvatar').attr('src', res.new_img);
                        Swal.fire({
                            icon: 'success',
                            title: 'Profile Updated',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        bootstrap.Modal.getInstance(document.getElementById('profileModal')).hide();
                        // Reset password fields
                        $('input[name="password"], input[name="password_confirmation"]').val('');
                    }
                },
                error: (err) => Swal.fire('Error', 'Update failed', 'error'),
                complete: () => btn.prop('disabled', false).text('Save Changes')
            });
        });

        /* Logout */
        logoutBtn.addEventListener('click', logoutUser)
        document.getElementById('logoutBtnDropdown')?.addEventListener('click', logoutUser)

        async function logoutUser() {
            try {
                const res = await fetch('{{ url('/api/logout') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': `Bearer ${getStoredAuthToken()}`
                    }
                })
                const data = await res.json()
                window.location.href = data.redirect ?? '{{ route('landing') }}'
            } catch (e) {
                alert('Logout failed! Please try again.')
            }
        }
    </script>
    <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
   </script>

    @stack('scripts')
</body>

</html>
