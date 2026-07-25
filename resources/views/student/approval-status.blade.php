<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Submitted</title>

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

        /* Success Animation */
        .checkmark-svg {
            width: 50px;
            height: 50px;
            display: block;
            margin: 0 auto 15px;
        }

        .checkmark-path {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            stroke: #10b981;
            stroke-width: 8;
            stroke-linecap: round;
            fill: none;
            animation: draw 0.8s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        @keyframes draw {
            to {
                stroke-dashoffset: 0;
            }
        }

        /* Button */
        .btn-sharp {
            border: 1.5px solid #2563eb;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            color: #2563eb;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .btn-sharp:hover {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            transform: translateY(-1px);
        }

        /* Toast */
        #copy-toast {
            visibility: hidden;
            min-width: 140px;
            background-color: #1e293b;
            color: #fff;
            text-align: center;
            padding: 8px 16px;
            position: fixed;
            z-index: 50;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            font-size: 10px;
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
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-[340px] animate-in fade-in duration-700">

        <div class="bg-white border border-slate-200 p-8 shadow-[0_20px_50px_rgba(0,0,0,0.04)]">

            <!-- Success Check -->
            <svg class="checkmark-svg" viewBox="0 0 52 52">
                <path class="checkmark-path" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
            </svg>

            <h1 class="text-xl font-[800] uppercase tracking-tight mb-3 text-center text-slate-900 leading-none">
                Admission Submitted
            </h1>

            <p class="text-slate-500 text-[11px] font-[600] mb-8 text-center leading-relaxed">
                Your admission request is pending for admin approval. SMS confirmation sent.
            </p>

            <!-- Student ID -->
            <div class="flex justify-between items-end mb-8 border-b border-slate-100 pb-4">
                <div>
                    <span class="block text-[9px] font-[800] text-slate-400 uppercase tracking-widest mb-1">
                        Student ID
                    </span>

                    <span id="student-id"
                        class="text-3xl font-mono font-[700] tracking-tighter text-slate-900 leading-none">

                        {{ request('id') }}

                    </span>
                </div>

                <button onclick="copyID()"
                    class="text-slate-400 hover:text-blue-500 transition-colors active:scale-90 pb-1">

                    <i class="mdi mdi-content-copy text-xl"></i>

                </button>
            </div>

            <!-- Support -->
            <div class="mb-8">

                <span class="block text-[9px] font-[800] text-slate-400 uppercase tracking-widest mb-2">
                    Support
                </span>

                <a href="https://wa.me/8801878738818"
                    class="flex items-center text-sm font-[700] text-slate-700 no-underline hover:text-emerald-600 transition-colors">

                    <i class="mdi mdi-whatsapp mr-2 text-emerald-500 text-lg"></i>

                    01878738818

                </a>

            </div>

            <!-- Return Button -->
            <a href="{{ url('/') }}" class="btn-sharp w-full">

                Return to Login

                <i class="mdi mdi-login-variant ml-2 text-sm"></i>

            </a>

        </div>
    </div>

    <!-- Copy Toast -->
    <div id="copy-toast">ID Copied</div>

    <script>
        function copyID() {

            const idText = document.getElementById('student-id').innerText;

            navigator.clipboard.writeText(idText).then(() => {

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
