@extends('layouts.school')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        .premium-view-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 80vh;
            padding: 1rem;
            background: #f8fafc;
        }

        .status-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            width: 100%;
            max-width: 450px;
            padding: 2.5rem 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        /* Sharp border accents */
        .status-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: #2563eb;
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background: #f1f5f9;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .countdown-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin: 2rem 0;
        }

        .timer-unit {
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            padding: 0.75rem 0.25rem;
            text-align: center;
        }

        .timer-val {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e293b;
            font-family: 'Inter', monospace;
        }

        .timer-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            font-weight: 700;
            margin-top: 2px;
        }

        .badge-premium {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            padding: 4px 12px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }
    </style>

    <div class="premium-view-container">
        <div class="status-card">

            <div class="icon-box">
                <i class="mdi mdi-application-cog-outline text-3xl"></i>
            </div>

            <span class="badge-premium">System Update</span>

            <h1 class="text-xl font-black text-gray-900 uppercase tracking-tight mb-2">
                {{ $title ?? 'Module Under Development' }}
            </h1>

            <p class="text-xs text-gray-500 leading-relaxed max-w-xs mx-auto mb-6">
                We are currently refining the logic for this feature. <br>
                Estimated deployment is scheduled for the date below.
            </p>

            <div class="countdown-grid">
                <div class="timer-unit">
                    <div id="days" class="timer-val">00</div>
                    <div class="timer-label">Days</div>
                </div>
                <div class="timer-unit">
                    <div id="hours" class="timer-val">00</div>
                    <div class="timer-label">Hrs</div>
                </div>
                <div class="timer-unit">
                    <div id="minutes" class="timer-val">00</div>
                    <div class="timer-label">Min</div>
                </div>
                <div class="timer-unit">
                    <div id="seconds" class="timer-val">00</div>
                    <div class="timer-label">Sec</div>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 pt-4 border-t border-gray-100">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                    Release: March 17, 2026
                </span>
            </div>
        </div>
    </div>

    <script>
        // Target Date updated to March 17, 2026
        const targetDate = new Date("March 17, 2026 00:00:00").getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            // Time calculations
            const d = Math.floor(distance / (1000 * 60 * 60 * 24));
            const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((distance % (1000 * 60)) / 1000);

            // Update with zero padding
            document.getElementById("days").innerHTML = d < 10 ? "0" + d : d;
            document.getElementById("hours").innerHTML = h < 10 ? "0" + h : h;
            document.getElementById("minutes").innerHTML = m < 10 ? "0" + m : m;
            document.getElementById("seconds").innerHTML = s < 10 ? "0" + s : s;

            if (distance < 0) {
                clearInterval(timerInterval);
                document.querySelector(".countdown-grid").innerHTML =
                    "<div class='col-span-4 text-xs font-bold text-blue-600 py-4 uppercase tracking-widest'>Deployment in progress...</div>";
            }
        }

        const timerInterval = setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial call
    </script>
@endsection
