@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        /* 1. Global Reset & Sharp Edges */
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden !important;
            margin: 0;
            padding: 0;
        }

        /* 2. Container Padding */
        .main-view-container {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            width: 100%;
            padding: 0.75rem;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }


        /* 3. Table Sharp Style */
        .table-card {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            width: 100%;
            overflow: hidden;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                    <div class="w-full lg:w-auto">
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight"></h2>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500"></span>
                        </div>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="teacherSearchMobile" placeholder="Search Faculty..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            <div class="table-card p-4">
                <div class="flex flex-col items-center justify-center my-10 sm:my-20 p-8 bg-white rounded-2xl shadow-sm border border-gray-100 gap-6 max-w-2xl mx-auto">
                    <!-- icon with pulse animation -->
                    <div class="relative">
                        <i class="fas fa-tools text-6xl text-blue-500 animate-bounce"></i>
                        <div class="absolute -inset-1 bg-blue-100 rounded-full blur opacity-30 animate-pulse"></div>
                    </div>

                    <!-- text section -->
                    <div class="text-center space-y-3">
                        <h3 class="text-2xl md:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-800 to-gray-500 bg-clip-text text-transparent">
                            সিস্টেম আপডেট চলছে
                        </h3>

                        <p class="text-lg text-gray-500 font-medium leading-relaxed">
                            আমাদের এই ফিচারের কাজ বর্তমানে প্রক্রিয়াধীন। আপডেটটি সম্পন্ন হবে:
                            <span class="block mt-2 py-1 px-4 bg-blue-50 text-blue-700 rounded-full inline-block font-semibold">
                                ১৫/০৮/২০২৬, রাত ১২:০০ টায়
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
