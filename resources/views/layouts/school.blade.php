<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School Dashboard')</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/pages/expense.css'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background: #f4f6fb;
            margin: 0;
        }


        /* Modified on 2026-07-09: Remove default anchor underlines globally */
        a {
            text-decoration: none !important;
        }

        .show {
            display: block !important;
        }

        @media (min-width: 768px) {
            #editProfileModal[data-mode="principal"]>div {
                max-height: min(520px, calc(100dvh - 2.5rem)) !important;
            }
        }

        /* Sidebar */
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
                top: 0;
                width: min(16rem, 72vw);
                height: 100vh;
                height: 100dvh;
                z-index: 60;
                transform: translateX(-100%);
                box-shadow: 8px 0 24px rgba(15, 23, 42, 0.16);
            }

            .sidebar-brand {
                display: flex;
                height: 4rem;
                flex-shrink: 0;
                align-items: center;
                justify-content: space-between;
                padding: 8px 12px !important;
            }

            .sidebar-brand-logo {
                width: 128px !important;
                height: 48px !important;
                border: 0 !important;
            }

            .sidebar-brand-logo img {
                width: 128px !important;
                height: auto !important;
                max-width: none !important;
                flex-shrink: 0;
            }

            .sidebar-mobile-close {
                display: inline-flex !important;
            }

            .sidebar-header-divider {
                margin: 0;
            }
        }

        .sidebar-header-divider {
            border-bottom: 1px solid #e5e7eb;
            margin: 0 1.5rem;
        }


        /* CSS FOR INCREASE OR DECREASE SPACE BETWEEN MENU SUBMENU */
        .sidebar-item,
        .sidebar-subitem,
        .sidebar-group-toggle {
            margin-bottom: 12px;
            /* INCREASED from 6px */
            color: #415a80;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            margin-left: 8px !important;
            margin-right: 5px !important;
            padding-top: 5px;
            /* ADDED: Vertical Padding for taller hover box */
            padding-bottom: 5px;
            /* ADDED: Vertical Padding for taller hover box */
        }


        /* SIDEBAR SUBMENU SPACE & HOVER BOX SIZE , START POINT FIX DESKTOP MODE*/
        .sidebar-subitem {
            margin-left: 36px !important;
            /* This creates the indentation */
            padding-left: 10px !important;
            /* This is the space between hover edge and icon */
            margin-right: 5px !important;
            font-weight: 400;
            position: relative;
            gap: 12px;
            margin-bottom: 12px;
            transition: none !important;
            display: flex;
            align-items: center;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        /* SIDEBAR SUBMENU SPACE & HOVER BOX SIZE , START POINT FIX MOBILE MODE*/
        @media (max-width: 767px) {

            .sidebar-item,
            .sidebar-group-toggle {
                margin-left: 12px !important;
                margin-right: 12px !important;
                padding-left: 10px !important;
                width: auto;
            }

            /* Forces submenus to stay indented while keeping hover box tight */
            .sidebar-subitem {
                margin-left: 36px !important;
                /* Indent the whole element */
                margin-right: 12px !important;
                padding-left: 10px !important;
                /* Hover box starts 10px before the icon */
                width: auto;
            }
        }

        /* Hover Left Padding Of Menus*/
        .sidebar-item,
        .sidebar-group-toggle,
        .nav-header {
            padding-left: 10px !important;
        }

        .sidebar-item {
            font-weight: 500;
            gap: 12px;
        }

        /* Groups */
        .sidebar-group {
            margin-bottom: 8px;
        }

        .sidebar-group-toggle {
            justify-content: space-between;
            font-weight: 500;
            padding-right: 14px;
            border-radius: 10px;
        }

        .sidebar-group-toggle span {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }


        /* Vertical line */
        .sidebar-group-content {
            display: none;
            flex-direction: column;
            margin-top: 4px;
            position: relative;
        }

        .sidebar-group.open .sidebar-group-content {
            display: flex;
        }


        /* Vertical connection line removed */
        .sidebar-group-content::before {
            display: none;
        }


        /* Dot indicators removed */
        .sidebar-subitem::before,
        .sidebar-subitem.active::before {
            display: none;
        }

        /* Icons */
        .sidebar-subitem i,
        .sidebar-item i,
        .sidebar-group-toggle i {
            width: 16px;
            flex-shrink: 0;
        }

        /* Premium Hover State */
        .sidebar-item:hover,
        .sidebar-subitem:hover,
        .sidebar-group-toggle:hover {
            background: #edf4ff !important;
            color: #0a0aa1 !important;
            transition: none !important;
        }

        /* Specific Icon swap on hover */
        .sidebar-item:hover i,
        .sidebar-subitem:hover i,
        .sidebar-group-toggle:hover span i:first-child {
            background: transparent !important;
            color: #2563eb !important;
            border-color: transparent !important;
        }


        /* Active */
        .sidebar-item.active,
        .sidebar-subitem.active {
            background: #edf4ff;
            color: #1d4ed8;
            box-shadow: 0 4px 12px rgba(30, 64, 175, .08);
        }

        .sidebar-item.active i,
        .sidebar-subitem.active i {
            color: #2563eb;
        }

        /* Circular icon containers - Blue box with Orange icon */
        .sidebar-subitem i,
        .sidebar-item i,
        .sidebar-group-toggle span i:first-child {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 24px;
            /* Slightly smaller since no border needed */
            height: 24px;
            background: transparent !important;
            /* Removed background */
            border: none !important;
            /* Removed border */
            color: #5a7193 !important;
            font-size: 1.1rem;
            /* Slightly larger for visibility */
            flex-shrink: 0;
        }

        /* ================= Sidebar Polishing ================= */

        /* Ensure main items align perfectly with group titles */
        .sidebar-item {
            padding-left: 14px;
            /* already set */
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Add consistent spacing between groups */
        .sidebar-group {
            margin-bottom: 20px;
            /* increased from 8px for better spacing */
        }

        /* Optional: separate dashboard from first group slightly */
        .sidebar-item:first-child {
            margin-bottom: 12px;
        }

        /* Optional: make subitem icons and text aligned with main items */
        .sidebar-subitem {
            padding-left: 36px;
            /* already set */
            gap: 12px;
        }

        /* Remove extra left shift of Dashboard icon */
        .sidebar-item i {
            margin-left: 0;
        }

        /* Flat high-performance hover state */
        .sidebar-item:hover,
        .sidebar-subitem:hover,
        .sidebar-group-toggle:hover {
            background: #edf4ff !important;
            color: #1d4ed8 !important;
            transition: none !important;
        }

        /* Optional: slightly round the group toggle for polish */
        .sidebar-group-toggle {
            border-radius: 12px;
            padding-left: 14px;
            padding-right: 14px;
        }

        /* --- REMOVE BORDER RADIUS ON HOVER & ACTIVE STATES --- */

        /* Target every clickable item in the sidebar */
        .sidebar-item,
        .sidebar-subitem,
        .sidebar-group-toggle,
        .sidebar-item:hover,
        .sidebar-subitem:hover,
        .sidebar-group-toggle:hover,
        .sidebar-item.active,
        .sidebar-subitem.active {
            border-radius: 0 !important;
            /* Force sharp corners */
        }

        /* Specific fix for the group toggle which had 12px set previously */
        .sidebar-group-toggle {
            border-radius: 0 !important;
        }

        /* Ensure the active state box is also sharp */
        .sidebar-item.active,
        .sidebar-subitem.active {
            border-radius: 0 !important;
            box-shadow: none !important;
            /* Optional: remove shadow for a flatter, cleaner elite look */
        }

        /* Optional: extra space before group headers (if using nav-header) */
        .nav-header {
            margin-top: 2rem;
            margin-bottom: 0.75rem;
        }

        /* Move Dashboard text slightly left */
        .sidebar-item:first-child {
            padding-left: 8px;
            /* reduce left padding for Dashboard */
        }

        /* Keep the icon same size but align it left nicely */
        .sidebar-item:first-child i {
            margin-left: 0;
        }

        /* Arrow stays normal, no border */
        .sidebar-group-toggle i:last-child {
            border: none;
            background: transparent;
            width: auto;
            height: auto;
            font-size: 0.65rem;
            /* optional smaller size */
        }


        /* Keeps dashboard text aligned on mobile */
        .sidebar-item:first-child {
            padding-left: 10px !important;
        }


        /* Headers */
        .nav-header {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            margin-top: 1.5rem;
            margin-bottom: .5rem;
        }

        /* Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* Mobile sidebar */
        .sidebar-mobile-hidden {
            transform: translateX(-100%);
        }

        .sidebar-mobile-show {
            transform: translateX(0);
        }

        .sidebar-mobile-close {
            display: none;
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 0;
            background: #eff6ff;
            color: #60a5fa;
            font-size: 12px;
        }

        .sidebar-mobile-close:hover {
            background: #dbeafe;
            color: #3b82f6;
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            z-index: 50;
            visibility: hidden;
            background: rgba(15, 23, 42, 0.28);
            opacity: 0;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        .sidebar-backdrop.is-visible {
            visibility: visible;
            opacity: 1;
        }

        @media(min-width:768px) {
            .sidebar-backdrop {
                display: none;
            }
        }

        /* Topbar */
        .topbar {
            background: #f9fafb;
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
            background: #f9fafb;
        }

        @media(min-width:768px) {
            .main-scroll {
                padding-left: 2rem;
                padding-right: 2rem;
                margin-left: 16rem;
                background: #f9fafb;
            }
        }

        .sidebar .logout-wrapper {
            margin-top: auto;
            margin-bottom: 2rem;
        }

        .logout-wrapper button {
            border-radius: 0 !important;
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


        /* Only rotate the Chevron (last icon), keep the circular icon static */
        .sidebar-group-toggle i:last-child {
            transition: transform 180ms ease-out, color 180ms ease-out;
        }

        .sidebar-group.open .sidebar-group-toggle i:last-child {
            transform: rotate(90deg);
            color: #0000ff !important;
        }

        /* Force the main circular icon to stay upright */
        .sidebar-group.open .sidebar-group-toggle i:first-child {
            transform: none !important;
        }

        /* Dropdown */
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

        #profileMenu {
            animation: profileMenuIn 0.2s ease-out;
            transform-origin: top right;
            width: 180px;
            margin-top: 0;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
        }

        @media(max-width:767px) {
            #profileMenu {
                width: 110px;
            }
        }

        @keyframes profileMenuIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .profile-menu-school {
            min-height: 34px;
            padding: 9px 12px 7px;
            overflow: hidden;
            color: #64748b;
            font-size: 10px;
            font-weight: 400;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .profile-dropdown-link {
            display: flex;
            width: 100%;
            min-height: 32px;
            align-items: center;
            gap: 9px;
            padding: 7px 12px;
            border: 0;
            background: #ffffff;
            color: #52657b;
            font-size: 12px;
            font-weight: 400;
            line-height: 1.2;
            text-align: left;
            transition: background-color 150ms ease-out, color 150ms ease-out;
        }

        .profile-dropdown-link i {
            display: inline-flex;
            width: 14px;
            flex: 0 0 14px;
            align-items: center;
            justify-content: center;
            color: #46627f;
            font-size: 10px;
        }

        .profile-dropdown-link:hover,
        .profile-dropdown-link:focus-visible {
            background: #f8fafc;
            color: #2563eb;
            outline: none;
        }

        .profile-dropdown-link:hover i,
        .profile-dropdown-link:focus-visible i {
            color: #2563eb;
        }

        .profile-dropdown-logout,
        .profile-dropdown-logout i {
            color: #ef4444;
        }

        .profile-dropdown-logout:hover,
        .profile-dropdown-logout:focus-visible {
            background: #fef2f2;
            color: #dc2626;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Modal Styles */
        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .preview-container {
            width: 100px;
            height: 100px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .preview-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* NEWLY ADDED CSS */

        /* Smaller circular topbar buttons */
        .topbar button a {
            width: 32px;
            /* 8rem ~ 32px */
            height: 32px;
            border-radius: 50%;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .topbar button i {
            font-size: 0.875rem;
            /* Slightly smaller icon */
        }

        /* Compact square sidebar toggle */
        #hamburger {
            width: 28px;
            height: 28px;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: #eff6ff;
            color: #60a5fa !important;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none;
            transition: background-color 0.16s ease, color 0.16s ease;
        }

        #hamburger:hover {
            background: #dbeafe;
            color: #3b82f6 !important;
        }

        #hamburger:focus-visible {
            outline: 2px solid #93c5fd;
            outline-offset: 2px;
        }


        #hamburger i {
            font-size: 12px;
        }

        .sidebar-desktop-toggle {
            display: none;
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 0;
            background: #eff6ff;
            color: #60a5fa;
            transition: background-color 160ms ease-out, color 160ms ease-out;
        }

        .sidebar-desktop-toggle:hover {
            background: #dbeafe;
            color: #3b82f6;
        }

        .sidebar-desktop-toggle:focus-visible {
            outline: 2px solid #93c5fd;
            outline-offset: 2px;
        }

        .sidebar-desktop-toggle i {
            font-size: 12px;
        }

        /* Only target top-level buttons and links in the topbar, not those inside dropdowns */
        .topbar .topbar-actions>a,
        .topbar .topbar-actions>button,
        .topbar .topbar-actions>.relative>button {
            width: 32px !important;
            height: 32px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50% !important;
            border: 1px solid #cbd5e1;
            transition: all 0.2s ease;
        }

        .topbar .topbar-actions>a i,
        .topbar .topbar-actions>button i,
        .topbar .topbar-actions>.relative>button i {
            font-size: 0.875rem !important;
        }

        .topbar .topbar-actions {
            gap: 8px !important;
        }

        .topbar .topbar-actions .topbar-action {
            width: 28px !important;
            height: 28px !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 9999px !important;
            box-shadow: none !important;
            transition: background-color 160ms ease-out, color 160ms ease-out, transform 160ms ease-out !important;
        }

        .topbar .topbar-actions>.relative>.topbar-action {
            width: 28px !important;
            height: 28px !important;
            border: 0 !important;
            border-radius: 9999px !important;
        }

        .topbar .topbar-actions .topbar-action:hover {
            transform: translateY(-1px);
        }

        .topbar .topbar-actions .topbar-action:focus-visible {
            outline: 2px solid currentColor;
            outline-offset: 2px;
        }

        .topbar .topbar-actions .topbar-action i {
            font-size: 12px !important;
            color: inherit !important;
        }

        .topbar .topbar-actions .topbar-action svg {
            width: 12px;
            height: 12px;
        }

        .topbar .topbar-actions .topbar-action-support {
            background: #e2e8f0 !important;
            color: #2563eb !important;
        }

        .topbar .topbar-actions .topbar-action-support:hover {
            background: #cbd5e1 !important;
        }

        .school-support-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            z-index: 80;
            width: min(260px, calc(100vw - 16px));
            max-height: calc(100dvh - 72px);
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            transform: translateX(-50%);
            animation: supportMenuIn 0.2s ease-out;
            transform-origin: top center;
        }

        @keyframes supportMenuIn {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(8px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0) scale(1);
            }
        }

        .school-support-card {
            padding: 0;
        }

        .school-support-heading {
            display: grid;
            gap: 2px;
            border-bottom: 1px solid #eef2f7;
            background: #ffffff;
            padding: 10px 12px;
        }

        .school-support-title {
            margin: 0;
            color: #334155;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.2;
        }

        .school-support-text {
            margin: 0;
            color: #94a3b8;
            font-size: 10px;
            line-height: 1.35;
        }

        .school-support-list {
            padding: 0;
        }

        .school-support-item {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
            min-height: 32px;
            padding: 7px 12px;
            background: #ffffff;
            color: #52657b;
            font-size: 12px;
            font-weight: 400;
            line-height: 1.2;
            text-decoration: none;
            transition: background-color 150ms ease-out, color 150ms ease-out;
        }

        .school-support-item:hover,
        .school-support-item:focus-visible {
            background: #f8fafc;
            color: #2563eb;
            outline: none;
        }

        .school-support-icon {
            display: flex;
            height: 20px;
            width: 14px;
            flex: 0 0 20px;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #2563eb;
        }

        .school-support-icon svg {
            height: 12px;
            width: 12px;
        }

        .school-support-item:nth-child(2) .school-support-icon {
            background: #ecfdf5;
            color: #16a34a;
        }

        .school-support-item:nth-child(3) .school-support-icon {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .school-support-item:hover .school-support-icon,
        .school-support-item:focus-visible .school-support-icon {
            filter: brightness(0.94);
        }

        .topbar .topbar-actions .topbar-action-notification {
            background: #e2e8f0 !important;
            color: #f59e0b !important;
        }

        .topbar .topbar-actions .topbar-action-notification:hover {
            background: #cbd5e1 !important;
        }

        .topbar .topbar-actions .topbar-action-notification span {
            top: 3px !important;
            right: 3px !important;
            width: 6px !important;
            height: 6px !important;
            border: 0 !important;
            background: #ef4444 !important;
        }

        .topbar .topbar-actions .topbar-action-language {
            background: #e2e8f0 !important;
            color: #9333ea !important;
        }

        .topbar .topbar-actions .topbar-action-language:hover {
            background: #cbd5e1 !important;
        }

        .school-language-menu {
            position: absolute;
            top: 100%;
            right: -44px;
            z-index: 80;
            width: 130px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            border-radius: 0;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            animation: profileMenuIn 0.2s ease-out;
            transform-origin: top right;
        }

        .school-language-option {
            display: flex;
            width: 100%;
            min-height: 32px;
            align-items: center;
            gap: 9px;
            padding: 7px 12px;
            border: 0;
            background: #ffffff;
            color: #52657b;
            font-size: 12px;
            font-weight: 400;
            line-height: 1.2;
            text-align: left;
            transition: background-color 150ms ease-out, color 150ms ease-out;
        }

        .school-language-option-label {
            min-width: 0;
            flex: 1;
        }

        .school-language-option:hover,
        .school-language-option:focus-visible {
            background: #f8fafc;
            color: #2563eb;
            outline: none;
        }

        .school-language-dot {
            width: 4px;
            height: 4px;
            flex: 0 0 4px;
            border-radius: 9999px;
            background: transparent;
        }

        .school-language-option.is-active {
            color: #2563eb;
            font-weight: 600;
        }

        .school-language-option.is-active .school-language-dot {
            background: #2563eb;
        }

        .school-notification-menu {
            position: absolute;
            top: 100%;
            right: -88px;
            z-index: 80;
            width: min(300px, calc(100vw - 16px));
            border: 1px solid #e5e7eb;
            border-radius: 0;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            animation: profileMenuIn 0.2s ease-out;
            transform-origin: top right;
        }

        .school-notification-panel {
            display: flex;
            max-height: min(520px, calc(100dvh - 80px));
            flex-direction: column;
            overflow: hidden;
            background: #ffffff;
        }

        .school-notification-header {
            padding: 13px 12px 9px;
            color: #0f172a;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
        }

        .school-notification-tabs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin: 0 12px 10px;
            border: 1px solid #eef2f7;
            background: #ffffff;
        }

        .school-notification-tab {
            display: flex;
            min-height: 34px;
            align-items: center;
            justify-content: center;
            gap: 5px;
            border: 0;
            border-right: 1px solid #f1f5f9;
            background: #ffffff;
            color: #334155;
            font-size: 11px;
            font-weight: 400;
        }

        .school-notification-tab:last-child {
            border-right: 0;
        }

        .school-notification-tab span {
            color: #2563eb;
            font-size: 9px;
        }

        .school-notification-tab.is-active {
            color: #0f172a;
            font-weight: 500;
        }

        .school-notification-list {
            min-height: 0;
            overflow-y: auto;
            padding: 10px 12px 12px;
            border-top: 1px solid #f1f5f9;
        }

        .school-notification-item {
            position: relative;
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 14px 15px 14px 47px;
            border: 1px solid #eef2f7;
            border-left: 3px solid transparent;
            background: #ffffff;
            color: #64748b;
            text-align: left;
            transition: background-color 150ms ease-out, border-color 150ms ease-out;
        }

        .school-notification-item:hover,
        .school-notification-item:focus-visible {
            border-color: #dbeafe;
            background: #f8fbff;
            outline: none;
        }

        .school-notification-item:last-child {
            margin-bottom: 0;
        }

        .school-notification-item.is-unread {
            border-left-color: #1597ff;
            background: #fbfdff;
        }

        .school-notification-type-icon {
            position: absolute;
            top: 14px;
            left: 13px;
            display: inline-flex;
            width: 24px;
            height: 24px;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #2563eb;
            font-size: 10px;
        }

        .school-notification-type-icon.is-payment {
            background: #ecfdf5;
            color: #059669;
        }

        .school-notification-type-icon.is-student {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .school-notification-type-icon.is-subscription {
            background: #fff7ed;
            color: #ea580c;
        }

        .school-notification-title {
            color: #0f172a;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.3;
        }

        .school-notification-dot {
            width: 6px;
            height: 6px;
            flex: 0 0 6px;
            margin-top: 4px;
            border-radius: 9999px;
            background: #249dff;
        }

        .school-notification-message {
            margin: 5px 0 9px;
            overflow: hidden;
            color: #64748b;
            font-size: 10px;
            line-height: 1.4;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .school-notification-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #94a3b8;
            font-size: 9px;
            font-weight: 400;
            line-height: 1.2;
        }

        .school-notification-empty {
            min-height: 150px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 8px;
            color: #94a3b8;
            font-size: 11px;
        }

        .school-notification-empty:not(.hidden) {
            display: flex;
        }

        .school-notification-empty i {
            font-size: 20px;
        }

        .school-notification-footer {
            display: flex;
            min-height: 38px;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 12px;
            border-top: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .school-notification-footer button,
        .school-notification-footer a {
            border: 0;
            background: transparent;
            color: #2563eb;
            font-size: 10px;
            font-weight: 500;
        }

        @media (max-width: 479px) {
            .school-notification-list {
                padding: 8px;
            }

            .school-notification-item {
                margin-bottom: 8px;
                padding-right: 10px;
            }
        }

        .topbar .topbar-actions .topbar-action-theme {
            background: #e2e8f0 !important;
            color: #4f46e5 !important;
        }

        .topbar .topbar-actions .topbar-action-theme:hover {
            background: #cbd5e1 !important;
        }

        .topbar .topbar-actions .topbar-action-profile {
            background: #e2e8f0 !important;
            color: #22c55e !important;
        }

        .topbar .topbar-actions .topbar-action-profile:hover {
            background: #cbd5e1 !important;
        }

        /* --- Sidebar subscription card --- */
        .subscription-box {
            background: #0868f7 !important;
            border: 0 !important;
            border-radius: 0 !important;
            padding: 12px !important;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            margin: 24px 12px 20px !important;
            box-shadow: none;
        }

        .subscription-box .nav-header {
            color: #ffffff !important;
            font-size: 9px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            line-height: 1.3 !important;
            margin-bottom: 5px !important;
            font-weight: 800 !important;
            margin-left: 0px !important;
            padding-left: 0px !important;
            display: block;
        }

        .subscription-box #sidePlanName {
            color: #ffffff !important;
            font-weight: 800 !important;
            font-size: 13px !important;
            line-height: 1.3 !important;
            margin-bottom: 3px !important;
            overflow-wrap: anywhere;
        }

        .subscription-box #sideExpiryDate {
            color: #ffffff !important;
            font-size: 9px !important;
            font-weight: 500 !important;
            line-height: 1.35 !important;
            opacity: 0.95;
            margin-bottom: 11px !important;
            overflow-wrap: anywhere;
        }

        .subscription-box .bg-gray-200 {
            background-color: rgba(255, 255, 255, 0.16) !important;
            height: 3px !important;
            border: none !important;
            margin-bottom: 9px !important;
        }

        .subscription-box #sideProgressBar {
            background-color: #dcecff !important;
            box-shadow: none;
        }

        .subscription-box .upgrade-btn {
            background-color: #ffffff !important;
            color: #0759d5 !important;
            border-radius: 0 !important;
            border: 1px solid rgba(255, 255, 255, 0.9) !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            width: 100% !important;
            min-height: 28px !important;
            padding: 4px 8px !important;
            margin-top: 0 !important;
            transition: background-color 0.2s ease, color 0.2s ease !important;
        }

        .subscription-box .upgrade-btn:hover {
            background-color: #eaf3ff !important;
            color: #0649b4 !important;
        }

        /* --- COLLAPSED RAIL STATE --- */
        @media (min-width: 768px) {
            .collapsed-mode .subscription-box {
                width: 50px !important;
                height: 50px !important;
                margin: 10px auto !important;
                /* PERFECTLY CENTERED */
                padding: 0 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            /* Hide everything except the icon button in rail mode */
            .collapsed-mode .subscription-box .plan-info>p,
            .collapsed-mode .subscription-box .bg-gray-200,
            .collapsed-mode .subscription-box .upgrade-btn span {
                display: none !important;
            }

            .collapsed-mode .subscription-box .upgrade-btn {
                width: 100% !important;
                height: 100% !important;
                background: transparent !important;
                /* Let gradient show through */
                display: flex !important;
                align-items: center;
                justify-content: center;
            }

            .collapsed-mode .subscription-box .upgrade-btn i {
                font-size: 1.25rem !important;
                color: #1e40af !important;
                /* Icon stays blue */
            }
        }

        /* Sidebar Base - Add Transition */
        .sidebar {
            transition: width .35s cubic-bezier(.4, 0, .2, 1), transform .35s cubic-bezier(.4, 0, .2, 1) !important;
        }


        /* DESKTOP RAIL MODE (768px+) */
        @media (min-width: 768px) {

            #hamburger {
                display: none;
            }

            .sidebar-desktop-toggle {
                display: inline-flex;
            }

            /* When collapsed class is present */
            .collapsed-mode .sidebar {
                width: 72px !important;
            }

            /* FIX: Ensure Topbar and Main Content align to the new sidebar width */
            .collapsed-mode .topbar {
                left: 72px !important;
                width: calc(100% - 72px) !important;
            }

            .collapsed-mode .main-scroll {
                margin-left: 72px !important;
                width: calc(100% - 72px) !important;
                padding-left: 2rem;
                padding-right: 2rem;
            }

            /* Hide text and elements - Use opacity and visibility for smoother transitions */
            .collapsed-mode .sidebar span,
            .collapsed-mode .sidebar .fa-chevron-right,
            .collapsed-mode .sidebar .nav-header,
            .collapsed-mode .sidebar .subscription-box,
            .collapsed-mode .sidebar .sidebar-header-divider,
            .collapsed-mode .sidebar-item {
                font-size: 0;
                white-space: nowrap;
            }

            /* Specifically hide the dropdown arrow in collapsed mode */
            .collapsed-mode .sidebar .sidebar-group-toggle i:last-child {
                display: none;
            }

            /* Center the icons in the rail */
            .collapsed-mode .sidebar-item,
            .collapsed-mode .sidebar-group-toggle {
                width: 40px !important;
                min-height: 40px !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 0 !important;
                margin: 0 auto 2px !important;
                gap: 0 !important;
            }

            /* Center the icons exactly in the middle of the hover box */
            .collapsed-mode .sidebar-item i,
            .collapsed-mode .sidebar-group-toggle span i:first-child {
                margin: 0 !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                width: 20px !important;
                height: 20px !important;
            }

            .collapsed-mode .sidebar-group-toggle>span {
                width: 20px !important;
                flex: 0 0 20px;
                justify-content: center;
                gap: 0 !important;
            }

            /* Hide sub-menu items entirely */
            .collapsed-mode .sidebar-group-content {
                display: none !important;
            }

            /* --- 1. Symmetrical Logo Container --- */
            .collapsed-mode .sidebar .p-6 {
                min-height: 96px;
                padding: 8px 0 !important;
                flex-direction: column;
                gap: 6px;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin: 0 !important;
            }

            .collapsed-mode .sidebar .w-full.h-20 {
                position: relative;
                width: 60px !important;
                height: 48px !important;
                margin: 0 auto !important;
                border: 0 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
            }

            .collapsed-mode .sidebar-brand-logo img {
                position: absolute;
                top: 50%;
                left: -10px;
                width: 200px !important;
                height: auto !important;
                max-width: none !important;
                flex-shrink: 0;
                transform: translateY(-50%);
            }

            .collapsed-mode .sidebar-desktop-toggle {
                width: 28px;
                height: 28px;
            }

            /* --- 2. Symmetrical Subscription Box --- */
            .collapsed-mode .subscription-box {
                width: 50px !important;
                height: 50px !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                background: #0868f7 !important;
                border: none !important;
                box-shadow: 0 4px 10px rgba(8, 104, 247, 0.25);
                margin: 10px auto !important;
            }

            /* Hide all text/progress bars inside the box */
            .collapsed-mode .subscription-box .plan-info>p,
            .collapsed-mode .subscription-box .bg-gray-200 {
                display: none !important;
            }

            /* --- 3. Transform Upgrade Button --- */
            .collapsed-mode .subscription-box .plan-info {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .collapsed-mode .subscription-box .upgrade-btn {
                width: 100% !important;
                height: 100% !important;
                background: transparent !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                border: none !important;
            }

            .collapsed-mode .subscription-box .upgrade-btn span {
                display: none !important;
            }

            .collapsed-mode .subscription-box .upgrade-btn i {
                font-size: 1.25rem !important;
                /* Prominent icon */
                margin: 0 !important;
                color: white !important;
            }

            .topbar,
            .main-scroll {
                transition: all .35s cubic-bezier(.4, 0, .2, 1);
            }
        }

        /* Reference-aligned expanded sidebar styling */
        .sidebar {
            width: 14rem;
            border-right-color: #e5eaf0;
            box-shadow: 2px 0 8px rgba(15, 23, 42, 0.05);
        }

        body:not(.collapsed-mode) .sidebar-brand {
            display: flex;
            height: 4.25rem;
            flex-shrink: 0;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px !important;
        }

        body:not(.collapsed-mode) .sidebar-brand-logo {
            width: 128px !important;
            height: 50px !important;
            margin: 0 !important;
            border: 0 !important;
            flex-shrink: 0;
        }

        body:not(.collapsed-mode) .sidebar-brand-logo img {
            width: 128px !important;
            height: auto !important;
            max-width: none !important;
        }

        body:not(.collapsed-mode) .sidebar-header-divider {
            margin: 0 8px;
            border-bottom-color: #eef2f6;
        }

        body:not(.collapsed-mode) #menuNav {
            padding: 6px 8px 16px !important;
        }

        body:not(.collapsed-mode) #menuNav>.sidebar-item,
        body:not(.collapsed-mode) #menuNav .sidebar-group-toggle {
            min-height: 40px;
            margin: 0 0 2px !important;
            padding: 7px 8px !important;
            gap: 8px;
            color: #4b6480;
            font-size: 12px;
            font-weight: 400;
            line-height: 1.25;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-group-toggle span {
            gap: 8px;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-group {
            margin: 0 !important;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-item i,
        body:not(.collapsed-mode) #menuNav .sidebar-group-toggle span i:first-child,
        body:not(.collapsed-mode) #menuNav .sidebar-subitem i {
            width: 20px !important;
            height: 20px !important;
            color: #506987;
            font-size: 14px !important;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-group-toggle i:last-child {
            width: auto !important;
            height: auto !important;
            color: #71849c;
            font-size: 11px !important;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-subitem {
            min-height: 36px;
            margin: 0 0 2px 28px !important;
            padding: 6px 8px !important;
            gap: 7px;
            color: #60758f;
            font-size: 11px;
            font-weight: 400;
            line-height: 1.25;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-item:hover,
        body:not(.collapsed-mode) #menuNav .sidebar-subitem:hover,
        body:not(.collapsed-mode) #menuNav .sidebar-group-toggle:hover {
            background: #f5f8fc !important;
            color: #2563eb !important;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-group.open>.sidebar-group-toggle {
            color: #111827 !important;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-group.open>.sidebar-group-toggle span i:first-child {
            color: #111827 !important;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-item.active,
        body:not(.collapsed-mode) #menuNav .sidebar-subitem.active {
            background: transparent !important;
            color: #0000ff !important;
        }

        body:not(.collapsed-mode) #menuNav .sidebar-item.active>i,
        body:not(.collapsed-mode) #menuNav .sidebar-subitem.active>i {
            width: 28px !important;
            height: 28px !important;
            background: #dbeafe !important;
            color: #0000ff !important;
        }

        @media (min-width: 768px) {
            body:not(.collapsed-mode) .topbar {
                left: 14rem;
                width: calc(100% - 14rem);
            }

            body:not(.collapsed-mode) .main-scroll {
                margin-left: 14rem;
                width: calc(100% - 14rem);
            }
        }

        @media (max-width: 767px) {
            .sidebar {
                width: min(14rem, calc(100vw - 48px));
            }
        }

        /* Semantic color tokens for top-level sidebar icons. */
        #menuNav .hgi-dashboard-browsing {
            --sidebar-icon-bg: #dbeafe;
            --sidebar-icon-color: #2563eb;
        }

        #menuNav .hgi-teacher {
            --sidebar-icon-bg: #ffedd5;
            --sidebar-icon-color: #ea580c;
        }

        #menuNav .hgi-student {
            --sidebar-icon-bg: #d1fae5;
            --sidebar-icon-color: #059669;
        }

        #menuNav .hgi-book-open-01 {
            --sidebar-icon-bg: #e0e7ff;
            --sidebar-icon-color: #4f46e5;
        }

        #menuNav .hgi-file-02 {
            --sidebar-icon-bg: #ffe4e6;
            --sidebar-icon-color: #e11d48;
        }

        #menuNav .hgi-wallet-02 {
            --sidebar-icon-bg: #cffafe;
            --sidebar-icon-color: #0891b2;
        }

        #menuNav .hgi-wallet-add-01 {
            --sidebar-icon-bg: #fef3c7;
            --sidebar-icon-color: #d97706;
        }

        #menuNav .fa-boxes {
            --sidebar-icon-bg: #ecfccb;
            --sidebar-icon-color: #65a30d;
        }

        #menuNav .hgi-user-multiple-02 {
            --sidebar-icon-bg: #ede9fe;
            --sidebar-icon-color: #7c3aed;
        }

        #menuNav .hgi-book-02 {
            --sidebar-icon-bg: #ccfbf1;
            --sidebar-icon-color: #0d9488;
        }

        #menuNav .hgi-notification-03 {
            --sidebar-icon-bg: #fee2e2;
            --sidebar-icon-color: #dc2626;
        }

        #menuNav .hgi-home-10 {
            --sidebar-icon-bg: #e0f2fe;
            --sidebar-icon-color: #0284c7;
        }

        #menuNav .hgi-global {
            --sidebar-icon-bg: #fae8ff;
            --sidebar-icon-color: #c026d3;
        }

        #menuNav .hgi-identity-card {
            --sidebar-icon-bg: #fce7f3;
            --sidebar-icon-color: #db2777;
        }

        #menuNav .hgi-grid-view {
            --sidebar-icon-bg: #e2e8f0;
            --sidebar-icon-color: #475569;
        }

        #menuNav .hgi-artificial-intelligence-04 {
            --sidebar-icon-bg: #f3e8ff;
            --sidebar-icon-color: #9333ea;
        }

        #menuNav :is(.hgi-user-add-01, .hgi-user-account, .hgi-user-group, .hgi-search-01) {
            --sidebar-icon-bg: #dbeafe;
            --sidebar-icon-color: #2563eb;
        }

        #menuNav :is(.hgi-calendar-check-in-01, .hgi-task-done-01, .hgi-leaf-01, .hgi-charity) {
            --sidebar-icon-bg: #dcfce7;
            --sidebar-icon-color: #16a34a;
        }

        #menuNav :is(.hgi-lock-password, .hgi-chair-01, .hgi-tag-01, .hgi-percent) {
            --sidebar-icon-bg: #ffedd5;
            --sidebar-icon-color: #ea580c;
        }

        #menuNav :is(.hgi-calendar-remove-01, .hgi-cancel-circle, .hgi-alert-circle, .hgi-money-send-01) {
            --sidebar-icon-bg: #fee2e2;
            --sidebar-icon-color: #dc2626;
        }

        #menuNav :is(.hgi-graduation-scroll, .hgi-certificate-01, .hgi-award-01, .hgi-ranking) {
            --sidebar-icon-bg: #e0e7ff;
            --sidebar-icon-color: #4f46e5;
        }

        #menuNav :is(.hgi-edit-01, .hgi-document-validation, .hgi-clock-01, .hgi-time-03) {
            --sidebar-icon-bg: #ccfbf1;
            --sidebar-icon-color: #0d9488;
        }

        #menuNav :is(.hgi-file-import, .hgi-database, .hgi-grid-table, .hgi-layers-01) {
            --sidebar-icon-bg: #cffafe;
            --sidebar-icon-color: #0891b2;
        }

        #menuNav :is(.hgi-dollar-circle, .hgi-credit-card, .hgi-arrow-up-01, .fa-credit-card, .fa-money-check) {
            --sidebar-icon-bg: #fef3c7;
            --sidebar-icon-color: #d97706;
        }

        #menuNav :is(.hgi-megaphone-02, .hgi-message-01, .hgi-news, .hgi-paint-board) {
            --sidebar-icon-bg: #fae8ff;
            --sidebar-icon-color: #c026d3;
        }

        #menuNav :is(.hgi-link-01, .hgi-link-square-02, .fa-link) {
            --sidebar-icon-bg: #e0f2fe;
            --sidebar-icon-color: #0284c7;
        }

        #menuNav :is(.hgi-file-01, .hgi-scroll, .hgi-briefcase-01) {
            --sidebar-icon-bg: #f1f5f9;
            --sidebar-icon-color: #475569;
        }

        #menuNav :is(.hgi-shopping-bag-03, .fa-box, .fa-shopping-cart, .fa-undo, .fa-chart-line) {
            --sidebar-icon-bg: #ecfccb;
            --sidebar-icon-color: #65a30d;
        }

        #menuNav>.sidebar-item>i,
        #menuNav>.sidebar-item:hover>i,
        #menuNav>.sidebar-item.active>i,
        #menuNav>.sidebar-group>.sidebar-group-toggle>span>i:first-child,
        #menuNav>.sidebar-group>.sidebar-group-toggle:hover>span>i:first-child,
        #menuNav>.sidebar-group.open>.sidebar-group-toggle>span>i:first-child {
            background: transparent !important;
        }

        #menuNav .sidebar-subitem>i,
        #menuNav .sidebar-subitem:hover>i,
        body:not(.collapsed-mode) #menuNav .sidebar-subitem.active>i {
            background: transparent !important;
        }
    </style>

    @stack('styles')
</head>

@php
    $school = \App\Models\School::where('user_id', auth()->id())->first();
    $schoolLogo = asset('images/astha-academy-logo.png');
@endphp

<body>

    <div class="flex">
        <x-layout.school.sidebar :school-logo="$schoolLogo" />

        <div id="sidebarBackdrop" class="sidebar-backdrop" aria-hidden="true"></div>

        <div class="flex-1 flex flex-col">

            <x-layout.school.header />

            <main class="main-scroll" id="mainContent">
                @yield('content')
            </main>

        </div>
    </div>


    <x-modal.form id="editProfileModal" form-id="profileUpdateForm" title="Profile"
        close-button-id="closeProfileModal" submit-label="Save">
        <div class="profile-modal-field relative pt-1">
            <x-input.photo id="profileLogo" name="logo" preview-id="profileLogoPreview" />
            <x-input.floating-label for="profileLogo" :floating="false">
                Institution logo
            </x-input.floating-label>
        </div>

        <div class="profile-modal-field relative">
            <x-input.control id="profileInstituteName" name="school_name" class="peer placeholder:text-transparent"
                placeholder=" " :value="$school?->school_name ?? auth()->user()->school_name" required />
            <x-input.floating-label for="profileInstituteName">
                Institute name
            </x-input.floating-label>
        </div>

        <div class="profile-modal-field relative">
            <x-input.control id="profileVillage" name="village" class="peer placeholder:text-transparent"
                placeholder=" " :value="$school?->village" />
            <x-input.floating-label for="profileVillage">
                Village/Area
            </x-input.floating-label>
        </div>

        <div class="profile-modal-field relative">
            <x-input.control id="profileMobile" name="mobile" class="peer placeholder:text-transparent"
                placeholder=" " :value="$school?->mobile ?? auth()->user()->mobile" required />
            <x-input.floating-label for="profileMobile">
                Mobile number
            </x-input.floating-label>
        </div>

        <div class="profile-modal-field relative">
            <x-input.control id="profileEmail" type="email" name="email"
                class="peer placeholder:text-transparent" placeholder=" " :value="$school?->email ?? auth()->user()->email" required />
            <x-input.floating-label for="profileEmail">
                Email address
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative hidden">
            <x-input.control id="principalName" name="name" data-principal-field
                class="peer placeholder:text-transparent" placeholder=" " />
            <x-input.floating-label for="principalName">
                Principal name
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative hidden">
            <x-input.control id="principalDesignation" name="designation" data-principal-field
                class="peer placeholder:text-transparent" placeholder=" " />
            <x-input.floating-label for="principalDesignation">
                Designation
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative hidden">
            <x-input.control id="principalPhone" name="phone" data-principal-field
                class="peer placeholder:text-transparent" placeholder=" " />
            <x-input.floating-label for="principalPhone">
                Mobile number
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative hidden">
            <x-input.control id="principalEmail" type="email" name="email" data-principal-field
                class="peer placeholder:text-transparent" placeholder=" " />
            <x-input.floating-label for="principalEmail">
                Email address
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative hidden">
            <x-input.control id="principalIdNumber" name="id_number" data-principal-field
                class="peer placeholder:text-transparent bg-slate-50" placeholder=" " readonly />
            <x-input.floating-label for="principalIdNumber">
                ID number
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative hidden">
            <x-input.password id="principalPin" name="pin" data-principal-field
                class="peer placeholder:text-transparent" placeholder=" " inputmode="numeric" maxlength="8"
                autocomplete="new-password" />
            <x-input.floating-label for="principalPin">
                PIN code
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative hidden">
            <x-input.control id="principalJoiningDate" type="date" name="joining_date" data-principal-field
                class="peer" />
            <x-input.floating-label for="principalJoiningDate" :floating="false">
                Joining date
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative hidden">
            <select id="principalStatus" name="is_active" data-principal-field
                class="h-9 w-full border border-slate-300 bg-white px-3 text-xs text-slate-700 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                style="border-radius: 0;">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            <x-input.floating-label for="principalStatus" :floating="false">
                Status
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative pt-1 hidden">
            <x-input.photo id="principalPhoto" name="photo" preview-id="principalPhotoPreview"
                data-principal-field />
            <x-input.floating-label for="principalPhoto" :floating="false">
                Image
            </x-input.floating-label>
        </div>

        <div class="principal-modal-field relative pt-1 hidden">
            <x-input.photo id="principalSignature" name="signature" preview-id="principalSignaturePreview"
                data-principal-field />
            <x-input.floating-label for="principalSignature" :floating="false">
                Signature
            </x-input.floating-label>
        </div>

        <div class="profile-pin-field relative hidden">
            <x-input.password id="profileCurrentPin" name="current_password"
                class="peer placeholder:text-transparent" placeholder=" " autocomplete="current-password" />
            <x-input.floating-label for="profileCurrentPin">
                Old PIN
            </x-input.floating-label>
        </div>

        <div class="profile-pin-field relative hidden">
            <x-input.password id="profileNewPin" name="new_password" class="peer placeholder:text-transparent"
                placeholder=" " autocomplete="new-password" />
            <x-input.floating-label for="profileNewPin">
                New PIN
            </x-input.floating-label>
        </div>

        <div class="profile-pin-field relative hidden">
            <x-input.password id="profileConfirmPin" name="new_password_confirmation"
                class="peer placeholder:text-transparent" placeholder=" " autocomplete="new-password" />
            <x-input.floating-label for="profileConfirmPin">
                Confirm PIN
            </x-input.floating-label>
        </div>

        <x-slot:footer>
            <div class="grid grid-cols-3 gap-2 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="closeProfileModal" type="button" class="w-full">
                    Cancel
                </x-button.secondary>

                <x-button.secondary id="editProfileFields" type="button" class="w-full">
                    Edit
                </x-button.secondary>

                <x-button.primary type="submit" class="w-full">
                    Save
                </x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    <div id="globalModalBackdrop" class="global-modal-backdrop"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePass(id, trigger) {
            const input = document.getElementById(id);
            const icon = trigger?.querySelector('i');
            const isPassword = input?.type === 'password';

            input?.setAttribute('type', isPassword ? 'text' : 'password');
            icon?.classList.toggle('mdi-eye', !isPassword);
            icon?.classList.toggle('mdi-eye-off', isPassword);
            trigger?.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        }

        /* -------------------------------
                                                                                                                                                                                                                                                                    INITIALIZE ELEMENTS SAFELY
        --------------------------------*/
        // We fetch these inside functions or check for null to prevent crashes
        const getEl = (id) => document.getElementById(id);

        /* -------------------------------
            IMAGE PREVIEW
        --------------------------------*/
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = getEl('logoPreview');
                if (output) output.src = reader.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        function previewPrincipleImage(event, previewId) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = getEl(previewId);
                if (output) output.src = reader.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        /* -----------------------------------------------------------
        PROFILE UPDATE (API) - FULL DYNAMIC SYNC
        ----------------------------------------------------------- */
        const profileForm = getEl('profileUpdateForm');

        if (profileForm) {
            profileForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(e.target);
                const btn = e.target.querySelector('button[type="submit"]');
                if (!btn) return;
                const isPrincipal = getEl('editProfileModal')?.dataset.mode === 'principal';

                // UI Loading State
                const originalBtnText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                btn.disabled = true;

                try {
                    const res = await fetch(
                        isPrincipal ? '/api/school/principal-update' : '/api/school/update-profile', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        }
                    );

                    const data = await res.json();

                    if (res.ok && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: data.message || 'Profile updated successfully!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });

                        const newName = formData.get('school_name');

                        if (!isPrincipal && data.data && data.data.logo_url) {
                            const topbarLogo = document.querySelector('#profileBtn img');
                            // Update Topbar Image
                            if (topbarLogo) topbarLogo.src = data.data.logo_url;

                            // Update Modal Preview only
                            const logoPreview = document.getElementById('logoPreview');
                            if (logoPreview) logoPreview.src = data.data.logo_url;

                            // NOTE: Sidebar logo (sideSchoolLogo) is intentionally
                            // left untouched to preserve system branding.
                        }

                        const topbarName = document.querySelector('#profileBtn span');
                        if (!isPrincipal && topbarName && newName) {
                            topbarName.textContent = newName;
                        }

                        const modalEl = getEl('editProfileModal');
                        if (modalEl) {
                            modalEl.classList.add('hidden');
                            document.body.classList.remove('overflow-hidden');
                        }

                    } else {
                        // Handle Validation Errors from Laravel
                        let errorMsg = data.message || 'Update Failed';
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('<br>');
                        }
                        throw new Error(errorMsg);
                    }

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Error',
                        html: error.message || 'Could not reach server.'
                    });
                } finally {
                    // Restore Button State
                    btn.innerHTML = originalBtnText;
                    btn.disabled = false;
                }
            });
        }


        /* -------------------------------
            MOBILE SIDEBAR & TOGGLES
        --------------------------------*/
        function setMobileSidebar(open) {
            const sidebar = getEl('sidebar');
            const backdrop = getEl('sidebarBackdrop');
            const hamburger = getEl('hamburger');

            if (!sidebar) return;

            sidebar.classList.toggle('sidebar-mobile-show', open);
            sidebar.classList.toggle('sidebar-mobile-hidden', !open);
            backdrop?.classList.toggle('is-visible', open);
            backdrop?.setAttribute('aria-hidden', String(!open));
            hamburger?.setAttribute('aria-expanded', String(open));
            document.body.style.overflow = open ? 'hidden' : '';
        }

        function updateDesktopSidebarToggle() {
            const toggle = getEl('sidebarDesktopToggle');
            const icon = toggle?.querySelector('i');
            const isCollapsed = document.body.classList.contains('collapsed-mode');

            toggle?.setAttribute('aria-expanded', String(!isCollapsed));
            toggle?.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
            icon?.classList.toggle('fa-bars', isCollapsed);
            icon?.classList.toggle('fa-times', !isCollapsed);
        }

        function toggleDesktopSidebar() {
            const body = document.body;

            body.classList.toggle('collapsed-mode');

            const isCollapsed = body.classList.contains('collapsed-mode');
            localStorage.setItem('sidebar_collapsed', String(isCollapsed));
            updateDesktopSidebarToggle();
        }

        getEl('hamburger')?.addEventListener('click', () => {
            const sidebar = getEl('sidebar');

            if (window.innerWidth < 768) {
                setMobileSidebar(!sidebar.classList.contains('sidebar-mobile-show'));
            } else {
                toggleDesktopSidebar();
            }
        });

        getEl('sidebarDesktopToggle')?.addEventListener('click', toggleDesktopSidebar);

        getEl('mobileSidebarClose')?.addEventListener('click', () => setMobileSidebar(false));
        getEl('sidebarBackdrop')?.addEventListener('click', () => setMobileSidebar(false));

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && window.innerWidth < 768) {
                setMobileSidebar(false);
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                setMobileSidebar(false);
            }
        });

        // Auto-restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', () => {
            if (window.innerWidth >= 768) {
                if (localStorage.getItem('sidebar_collapsed') === 'true') {
                    document.body.classList.add('collapsed-mode');
                }

                updateDesktopSidebarToggle();
            }
        });
        document.querySelectorAll('.sidebar-group-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const group = toggle.parentElement;
                if (group) {
                    group.classList.toggle('open');
                    const groups = [...document.querySelectorAll('.sidebar-group')];
                    localStorage.setItem('sidebar_open_group', groups.indexOf(group));
                }
            });
        });

        getEl('menuNav')?.addEventListener('scroll', (e) => {
            localStorage.setItem('sidebar_scroll_position', e.target.scrollTop);
        });


        /* -------------------------------
        RESTORE & HIGHLIGHT STATES
        --------------------------------*/
        function highlightActiveMenu() {
            const menuNav = document.getElementById('menuNav'); // your sidebar wrapper
            if (!menuNav) return;

            const links = menuNav.querySelectorAll('[data-link]');
            const currentPath = window.location.pathname;

            const pageHeader = document.getElementById('pageHeader');
            const pageTitle = document.getElementById('pageTitle');

            // Close all groups first
            document.querySelectorAll('.sidebar-group').forEach(group => group.classList.remove('open'));

            links.forEach(link => {
                link.classList.remove('active');

                const hrefAttr = link.getAttribute('href');
                if (!hrefAttr || hrefAttr === '#') return;

                try {
                    const linkPath = new URL(link.href).pathname;

                    if (currentPath === linkPath) {
                        link.classList.add('active');

                        // Get parent group toggle text for h2
                        const parentGroup = link.closest('.sidebar-group');
                        let groupTitle = 'Dashboard';
                        if (parentGroup) {
                            const toggleSpan = parentGroup.querySelector('.sidebar-group-toggle > span');
                            if (toggleSpan) {
                                // Remove any inner <i> icons from text
                                groupTitle = toggleSpan.innerText.trim();
                            }
                            parentGroup.classList.add('open');
                        }

                        // Set h2 as sidebar group title
                        if (pageHeader && !pageHeader.hasAttribute('data-keep-header')) pageHeader.innerText =
                            groupTitle;

                        // Set breadcrumb as active submenu text
                        if (pageTitle) pageTitle.innerText = link.innerText.trim();
                    }
                } catch (e) {
                    /* skip invalid URLs */
                }
            });
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', highlightActiveMenu);


        function restoreSidebarState() {
            const menuNav = getEl('menuNav');

            // Restore Scroll Position
            const savedScroll = localStorage.getItem('sidebar_scroll_position');
            if (menuNav && savedScroll !== null) {
                menuNav.scrollTop = savedScroll;
            }

            // Run the highlighter to open the correct group based on URL
            highlightActiveMenu();
        }

        /* -------------------------------
            DROPDOWNS & NOTIFICATIONS
        --------------------------------*/
        getEl('notificationBtn')?.addEventListener('click', (e) => {
            e.stopPropagation();
            getEl('notificationBox')?.classList.toggle('hidden');
        });

        /* -------------------------------
            LOGOUT
        --------------------------------*/

        async function logoutUser(e) {
            if (e) e.preventDefault();

            const btn = e.currentTarget;
            const originalContent = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.style.pointerEvents = 'none';

            try {
                const res = await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await res.json();

                Swal.fire({
                    icon: 'success',
                    title: data.message || 'Logged out successfully!',
                    text: 'Redirecting to login page...',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = data.redirect ?? '/';
                });


            } catch (error) {
                // alert('Logout failed! Check your connection.');

                btn.innerHTML = originalContent;
                btn.style.pointerEvents = 'auto';
            }
        }

        /* -------------------------------
        PAGE LOAD INIT
        --------------------------------*/
        window.addEventListener('DOMContentLoaded', () => {
            highlightActiveMenu();
            restoreSidebarState();

            const logoutButtons = [
                'logoutBtn',
                'logoutBtnDropdown'
            ];

            logoutButtons.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.removeEventListener('click', logoutUser);
                    el.addEventListener('click', logoutUser);
                }
            });
        });
    </script>

    @stack('scripts')


    {{-- GLOBAL SCRIPT FOR SUBSCRIPTION PLAN DATA & DYNAMIC LOGO --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        (function() {
            window.addEventListener('load', function() {
                const sideName = document.getElementById('sidePlanName');
                const sideExpiry = document.getElementById('sideExpiryDate');
                const sideBar = document.getElementById('sideProgressBar');

                if (!sideName) return;

                const token = document.querySelector('meta[name="csrf-token"]');
                if (token) {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                }

                axios.get('/api/current-subscription')
                    .then(res => {
                        const s = res.data.subscription;
                        if (s) {
                            const planNames = document.querySelectorAll('#sidePlanName, #modalPlanName');
                            const expiryDates = document.querySelectorAll(
                                '#sideExpiryDate, #modalExpiryDate');

                            planNames.forEach(el => el.innerText = s.package_name);
                            expiryDates.forEach(el => el.innerText = "Expires: " + s.expires_at);

                            // Precise Progress Calculation
                            const start = new Date(s.start_date_raw);
                            const end = new Date(s.expires_at_raw || s.expires_at);
                            const today = new Date();

                            const totalDuration = end - start;
                            const timeElapsed = today - start;

                            // Calculate percentage of time remaining
                            let percent = 100 - Math.floor((timeElapsed / totalDuration) * 100);

                            // Safety bounds
                            percent = Math.min(100, Math.max(0, percent));

                            if (sideBar) sideBar.style.width = percent + "%";

                            // Danger UI: Less than 5 days left
                            let days = parseInt(s.days_remaining) || 0;
                            if (days < 5) {
                                if (sideBar) sideBar.style.backgroundColor = "#ef4444";
                                planNames.forEach(el => el.style.color = "#ef4444");
                            }
                        }
                    })
                    .catch(() => {
                        // Keep the static subscription preview when live data is unavailable.
                    });
            });
        })();


        // Dynamic Logo Setup Script
        (function() {
            window.addEventListener('load', function() {
                const logoImg = document.getElementById('sideSchoolLogo');

                if (!logoImg) return;

                axios.get('/api/dynamic-operation')
                    .then(res => {
                        const settings = res.data;
                        // If the specific dashboard logo exists in the DB
                        if (settings && settings.school_dashboard_logo) {
                            // Update src with storage path
                            logoImg.src = window.location.origin + '/storage/' + settings
                                .school_dashboard_logo;
                        }
                    })
                    .catch(err => {
                        console.warn("Dynamic logo fetch failed, using default.");
                    });
            });
        })();


        // Notification box appearing script(Static)
        document.addEventListener('DOMContentLoaded', function() {
            const notifyBtn = document.getElementById('topbarNotificationBtn');
            const notifyBox = document.getElementById('notificationBox');
            const tabs = [...notifyBox.querySelectorAll('[data-notification-tab]')];
            const items = [...notifyBox.querySelectorAll('[data-notification-item]')];
            const emptyState = notifyBox.querySelector('[data-notification-empty]');
            const markAllButton = notifyBox.querySelector('[data-mark-all-read]');
            const timeLabels = ['5 min ago', '25 min ago', '2 days ago', '3 days ago'];
            let activeFilter = 'all';

            const itemMeta = (item) => {
                const directSpans = [...item.querySelectorAll(':scope > span')];
                return directSpans.at(-1);
            };

            const updateItemMeta = (item, index) => {
                const meta = itemMeta(item);
                if (!meta) return;

                meta.className = 'school-notification-meta';
                meta.textContent =
                    `${timeLabels[index] || 'Recently'} | ${item.dataset.status === 'unread' ? 'Unread' : 'Read'}`;
            };

            const refreshCounts = () => {
                const unreadCount = items.filter((item) => item.dataset.status === 'unread').length;
                const counts = {
                    all: items.length,
                    unread: unreadCount,
                    read: items.length - unreadCount,
                };

                tabs.forEach((tab) => {
                    const count = tab.querySelector('span');
                    if (count) count.textContent = counts[tab.dataset.notificationTab];
                });
            };

            const applyFilter = (filter) => {
                activeFilter = filter;
                let visibleCount = 0;

                items.forEach((item) => {
                    const visible = filter === 'all' || item.dataset.status === filter;
                    item.classList.toggle('hidden', !visible);
                    if (visible) visibleCount += 1;
                });

                emptyState?.classList.toggle('hidden', visibleCount !== 0);
                tabs.forEach((tab) => {
                    tab.classList.toggle('is-active', tab.dataset.notificationTab === filter);
                });
            };

            const markAsRead = (item) => {
                if (item.dataset.status === 'read') return;

                item.dataset.status = 'read';
                item.classList.remove('is-unread');
                item.querySelector('.school-notification-dot')?.remove();
                updateItemMeta(item, items.indexOf(item));
                refreshCounts();
                applyFilter(activeFilter);
            };

            function toggleNotifications(e) {
                if (e) e.stopPropagation();
                notifyBox.classList.toggle('hidden');
                document.getElementById('schoolLanguageMenu')?.classList.add('hidden');
                document.getElementById('profileMenu')?.classList.add('hidden');
                document.getElementById('supportMenu')?.classList.add('hidden');
                document.getElementById('topbarSupportBtn')?.setAttribute('aria-expanded', 'false');
            }

            function closeNotifications() {
                notifyBox.classList.add('hidden');
            }

            // Main Toggle
            notifyBtn.addEventListener('click', toggleNotifications);

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    applyFilter(tab.dataset.notificationTab);
                });
            });

            items.forEach((item, index) => {
                updateItemMeta(item, index);
                item.addEventListener('click', () => markAsRead(item));
                item.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        markAsRead(item);
                    }
                });
            });

            markAllButton?.addEventListener('click', () => {
                items.forEach((item) => markAsRead(item));
            });

            refreshCounts();
            applyFilter('all');

            document.addEventListener('click', (e) => {
                if (!notifyBox.contains(e.target) && !notifyBtn.contains(e.target)) {
                    closeNotifications();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeNotifications();
            });
        });
        /* -------------------------------
            SUPPORT FORM
        --------------------------------*/
        document.addEventListener('DOMContentLoaded', function() {
            const supportButton = getEl('topbarSupportBtn');
            const supportMenu = getEl('supportMenu');
            const supportContent = getEl('supportMenuContent');

            const setSupportOpen = (open) => {
                supportMenu?.classList.toggle('hidden', !open);
                supportButton?.setAttribute('aria-expanded', String(open));

                if (open) {
                    getEl('notificationBox')?.classList.add('hidden');
                    getEl('schoolLanguageMenu')?.classList.add('hidden');
                    getEl('profileMenu')?.classList.add('hidden');
                    supportContent?.focus();
                }
            };

            supportButton?.addEventListener('click', (event) => {
                event.stopPropagation();
                setSupportOpen(supportMenu?.classList.contains('hidden'));
            });

            document.addEventListener('click', (event) => {
                if (!supportMenu?.contains(event.target) && !supportButton?.contains(event.target)) {
                    setSupportOpen(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !supportMenu?.classList.contains('hidden')) {
                    setSupportOpen(false);
                }
            });
        });

        /* -------------------------------
            LANGUAGE DROPDOWN
        --------------------------------*/
        document.addEventListener('DOMContentLoaded', function() {
            const languageButton = getEl('topbarLanguageBtn');
            const languageMenu = getEl('schoolLanguageMenu');

            const setLanguageMenuOpen = (open) => {
                languageMenu?.classList.toggle('hidden', !open);
                languageButton?.setAttribute('aria-expanded', String(open));
            };

            languageButton?.addEventListener('click', (event) => {
                event.stopPropagation();
                getEl('notificationBox')?.classList.add('hidden');
                getEl('profileMenu')?.classList.add('hidden');
                getEl('supportMenu')?.classList.add('hidden');
                getEl('topbarSupportBtn')?.setAttribute('aria-expanded', 'false');
                setLanguageMenuOpen(languageMenu?.classList.contains('hidden'));
            });

            languageMenu?.querySelectorAll('[data-language-option]').forEach((option) => {
                option.addEventListener('click', () => {
                    languageMenu.querySelectorAll('[data-language-option]').forEach((item) => {
                        item.classList.toggle('is-active', item === option);
                    });
                });
            });

            document.addEventListener('click', (event) => {
                if (!languageMenu?.contains(event.target) && !languageButton?.contains(event.target)) {
                    setLanguageMenuOpen(false);
                }
            });
        });

        /* -------------------------------
            PROFILE DROPDOWN & MODAL
        --------------------------------*/
        document.addEventListener('DOMContentLoaded', function() {
            const profileBtn = getEl('profileBtn');
            const profileMenu = getEl('profileMenu');
            const profileModal = getEl('editProfileModal');
            const closeProfileModal = getEl('closeProfileModal');
            const editProfileFields = getEl('editProfileFields');
            const profileLogo = getEl('profileLogo');
            const profilePhoneControls = profileModal?.querySelectorAll(
                '[data-phone-country], [data-phone-local]'
            ) ?? [];
            const profileFields = profileModal?.querySelectorAll(
                'input:not([type="file"]):not([type="hidden"]):not([data-principal-field])'
            ) ?? [];
            const principalFields = profileModal?.querySelectorAll('[data-principal-field]') ?? [];
            const principalSections = profileModal?.querySelectorAll('.principal-modal-field') ?? [];
            const profileSections = profileModal?.querySelectorAll('.profile-modal-field') ?? [];
            const profilePinFields = profileModal?.querySelectorAll('.profile-pin-field') ?? [];

            const setProfileFieldsEditable = (editable) => {
                profileFields.forEach((field) => {
                    field.readOnly = !editable;
                });
            };

            const setPrincipalFieldsEditable = (editable) => {
                principalFields.forEach((field) => {
                    if (field.id === 'principalIdNumber') {
                        field.readOnly = true;
                        return;
                    }

                    if (field.tagName === 'SELECT' || field.type === 'file') {
                        field.disabled = !editable;
                        return;
                    }

                    field.readOnly = !editable;
                });
            };

            const setPrincipalPreview = (previewId, path) => {
                if (!path) return;

                const preview = getEl(previewId);
                if (!preview) return;

                const image = document.createElement('img');
                image.src = `/storage/${path}`;
                image.alt = '';
                image.className = 'h-full w-full object-contain';
                preview.replaceChildren(image);
            };

            const loadPrincipal = async () => {
                try {
                    const response = await fetch(`/api/school/principal?_=${Date.now()}`, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    });
                    const result = await response.json();
                    const principal = result.data || {};
                    const joiningDate = typeof principal.joining_date === 'string' ?
                        principal.joining_date.slice(0, 10) :
                        '';

                    getEl('principalName').value = principal.name || '';
                    getEl('principalDesignation').value = principal.designation || '';
                    getEl('principalPhone').value = principal.phone || '';
                    getEl('principalEmail').value = principal.email || '';
                    getEl('principalIdNumber').value = principal.id_number || '';
                    getEl('principalPin').value = principal.has_pin ? '' : '00000000';
                    getEl('principalJoiningDate').value = joiningDate;
                    getEl('principalStatus').value = principal.is_active === false ? '0' : '1';
                    setPrincipalPreview('principalPhotoPreview', principal.photo);
                    setPrincipalPreview('principalSignaturePreview', principal.signature);
                } catch (error) {
                    console.warn('Could not load principal information.', error);
                }
            };

            const setModalMode = (mode) => {
                const isPrincipal = mode === 'principal';
                profileModal.dataset.mode = mode;
                getEl('editProfileModalTitle').textContent = isPrincipal ? 'Principal' : 'Profile';

                principalSections.forEach((section) => {
                    section.classList.toggle('hidden', !isPrincipal);
                });
                profileSections.forEach((section) => {
                    section.classList.toggle('hidden', isPrincipal);
                });
                profilePinFields.forEach((field) => field.classList.add('hidden'));
                principalFields.forEach((field) => {
                    field.disabled = !isPrincipal;
                });
                profileFields.forEach((field) => field.disabled = isPrincipal);
                if (profileLogo) profileLogo.disabled = isPrincipal;
                profilePhoneControls.forEach((field) => field.disabled = isPrincipal);
                setPrincipalFieldsEditable(false);

                if (isPrincipal) loadPrincipal();
            };

            const setProfileModalOpen = (open) => {
                profileModal?.classList.toggle('hidden', !open);
                document.body.classList.toggle('overflow-hidden', open);

                if (open) {
                    setModalMode('profile');
                    setProfileFieldsEditable(false);
                    if (profileLogo) {
                        profileLogo.disabled = true;
                    }
                    profilePinFields.forEach((field) => field.classList.add('hidden'));
                }
            };

            profileBtn?.addEventListener('click', (event) => {
                event.stopPropagation();
                getEl('notificationBox')?.classList.add('hidden');
                getEl('schoolLanguageMenu')?.classList.add('hidden');
                getEl('supportMenu')?.classList.add('hidden');
                getEl('topbarSupportBtn')?.setAttribute('aria-expanded', 'false');
                profileMenu?.classList.toggle('hidden');
            });

            document.querySelectorAll('.profile-menu-item').forEach((item) => {
                item.addEventListener('click', (event) => {
                    event.preventDefault();
                    profileMenu?.classList.add('hidden');
                    setProfileModalOpen(true);
                    setModalMode(item.dataset.tab === 'principle' ? 'principal' : 'profile');
                });
            });

            closeProfileModal?.addEventListener('click', () => setProfileModalOpen(false));
            editProfileFields?.addEventListener('click', () => {
                const isPrincipal = profileModal?.dataset.mode === 'principal';

                if (isPrincipal) {
                    setPrincipalFieldsEditable(true);
                    getEl('principalName')?.focus();
                    return;
                }

                setProfileFieldsEditable(true);
                if (profileLogo) profileLogo.disabled = false;
                profilePhoneControls.forEach((field) => field.disabled = false);
                profilePinFields.forEach((field) => field.classList.remove('hidden'));
                getEl('profileInstituteName')?.focus();
            });

            profileModal?.addEventListener('click', (event) => {
                if (event.target === profileModal) {
                    setProfileModalOpen(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !profileModal?.classList.contains('hidden')) {
                    setProfileModalOpen(false);
                }
            });

            document.addEventListener('click', (event) => {
                if (!profileMenu?.contains(event.target) && !profileBtn?.contains(event.target)) {
                    profileMenu?.classList.add('hidden');
                }
            });
        });
    </script>

</body>

</html>
