<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Elite Blue Button */
        .btn-sharp {
            border: 1.5px solid #2563eb;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            color: #2563eb;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .btn-sharp:hover {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
            transform: translateY(-2px);
        }

        #copy-toast {
            visibility: hidden;
            min-width: 140px;
            background-color: #1e293b;
            color: #fff;
            text-align: center;
            padding: 10px 18px;
            position: fixed;
            z-index: 50;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        #copy-toast.show {
            visibility: visible;
            animation: pop 0.3s ease-out;
        }

        @keyframes pop {
            from {
                bottom: 0;
                opacity: 0;
            }

            to {
                bottom: 30px;
                opacity: 1;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .text-xl {
                font-size: 1rem;
                /* Slightly smaller headline */
            }

            .font-mono {
                font-size: 0.9rem;
                /* Adjust ID/password font */
            }

            .btn-sharp {
                font-size: 11px;
                height: 32px;
                /* Reduced button height for mobile */
            }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4 sm:p-6">

    @php
        $school_name = request()->query('name') ?? 'Your School';
        $id = request()->query('id') ?? '—';
        $password = request()->query('password') ?? '—';
        $school_name_formatted = "'" . ucwords(strtolower($school_name)) . "'";
    @endphp

    <div class="w-full max-w-[360px] sm:max-w-[400px] animate-in fade-in duration-700">

        <div class="bg-white border border-slate-200 p-6 sm:p-8 shadow-[0_20px_50px_rgba(0,0,0,0.04)]">

            <h1 class="text-lg sm:text-xl font-[800] tracking-tight mb-3 text-center text-slate-900 leading-snug">
                {{ $school_name_formatted }} Your registration has been successful!
            </h1>

            <p class="text-slate-500 text-[10px] sm:text-[11px] font-[600] mb-6 sm:mb-8 text-center leading-relaxed">
                Your account is pending admin review. After approval you will be able to access your school dashboard.
            </p>

            <!-- ID Section -->
            <div class="flex justify-between items-center mb-3 border-b border-slate-100 pb-2">
                <div>
                    <span class="block text-[8px] sm:text-[9px] font-[600] text-slate-400 tracking-widest mb-1">Your
                        ID</span>
                    <span id="school-id"
                        class="text-lg sm:text-xl font-mono font-[700] tracking-tighter text-slate-900 leading-none">
                        {{ $id }}
                    </span>
                </div>
                <button onclick="copyToClipboard('school-id')"
                    class="text-slate-400 hover:text-blue-500 transition-colors active:scale-90 pb-1">
                    <i class="mdi mdi-content-copy text-base sm:text-lg"></i>
                </button>
            </div>

            <!-- Password Section -->
            <div class="flex justify-between items-center mb-5 sm:mb-6">
                <div>
                    <span
                        class="block text-[8px] sm:text-[9px] font-[600] text-slate-400 tracking-widest mb-1">Password</span>
                    <span id="school-password"
                        class="text-lg sm:text-xl font-mono font-[700] tracking-tighter text-slate-900 leading-none">
                        {{ $password }}
                    </span>
                </div>
                <button onclick="copyToClipboard('school-password')"
                    class="text-slate-400 hover:text-blue-500 transition-colors active:scale-90 pb-1">
                    <i class="mdi mdi-content-copy text-base sm:text-lg"></i>
                </button>
            </div>

            <!-- Support Section -->
            <div class="mb-4 sm:mb-6">
                <span
                    class="block text-[8px] sm:text-[9px] font-[600] text-slate-400 tracking-widest mb-2">Support</span>
                <a href="tel:09606102050"
                    class="flex items-center text-sm sm:text-base font-[700] text-slate-700 no-underline hover:text-emerald-600 transition-colors">
                    <i class="mdi mdi-phone mr-2 text-emerald-500 text-base sm:text-lg"></i>
                    09606102050
                </a>
            </div>

            <a href="{{ url('/') }}" class="btn-sharp w-full justify-center">
                Return to Login
                <i class="mdi mdi-login-variant ml-2 text-sm sm:text-base"></i>
            </a>
        </div>
    </div>

    <div id="copy-toast">Copied</div>

    <script>
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.getElementById("copy-toast");
                toast.className = "show";
                setTimeout(() => {
                    toast.className = toast.className.replace("show", "");
                }, 2000);
            });
        }
    </script>

</body>

</html>
