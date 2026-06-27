<x-app-layout>
    @php
        $user = auth()->user();
        $averageScoreValue = $completedExams == 0 || !$averageScore ? null : number_format($averageScore, 2);
    @endphp

    <div class="dashboard-page min-h-screen py-8 sm:py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section class="order-1 dashboard-hero overflow-hidden rounded-[28px] border border-white/10">
                <div class="px-6 py-8 sm:px-8 lg:px-10 lg:py-9">
                    <div class="space-y-5 dashboard-reveal dashboard-reveal-delay-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-100 shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] dashboard-shimmer">
                            Student Dashboard
                        </div>

                        <div class="space-y-3">
                            <p class="text-lg font-medium text-amber-100/90 sm:text-xl">
                                Welcome back, {{ $user->name }}
                            </p>
                            <h1 class="dashboard-title max-w-5xl text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                                Stay prepared, track performance and move through your exams with confidence.
                            </h1>
                        </div>

                        <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                            <article class="dashboard-stat dashboard-stat-total dashboard-reveal dashboard-reveal-delay-2">
                                <span class="dashboard-stat-label dashboard-stat-label-gold">Total exams</span>
                                <strong class="dashboard-stat-value dashboard-stat-value-total counter" data-value="{{ $totalExams }}">0</strong>
                            </article>

                            <article class="dashboard-stat dashboard-stat-upcoming dashboard-reveal dashboard-reveal-delay-3">
                                <span class="dashboard-stat-label dashboard-stat-label-soft">Upcoming</span>
                                <strong class="dashboard-stat-value dashboard-stat-value-upcoming counter" data-value="{{ $upcomingExams }}">0</strong>
                            </article>

                            <article class="dashboard-stat dashboard-stat-completed dashboard-reveal dashboard-reveal-delay-4">
                                <span class="dashboard-stat-label dashboard-stat-label-soft">Completed</span>
                                <strong class="dashboard-stat-value dashboard-stat-value-completed counter" data-value="{{ $completedExams }}">0</strong>
                            </article>

                            <article class="dashboard-stat dashboard-stat-score dashboard-reveal dashboard-reveal-delay-4">
                                <span class="dashboard-stat-label dashboard-stat-label-soft">Average score</span>
                                @if ($averageScoreValue)
                                    <strong class="dashboard-stat-value dashboard-stat-value-score counter" data-value="{{ $averageScoreValue }}" data-type="percentage">0</strong>
                                @else
                                    <strong class="dashboard-stat-value dashboard-stat-empty">Not Attempted</strong>
                                @endif
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <section class="order-3 md:order-2 dashboard-panel p-5 sm:p-7 dashboard-reveal dashboard-reveal-delay-2">
                <div class="flex flex-col gap-3 border-b border-white/10 pb-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Exam Feed</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">Upcoming Exams</h2>
                    </div>
                    <a href="{{ route('student.exams.index') }}" class="inline-flex items-center justify-center rounded-xl border border-white/12 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-100 transition hover:bg-white/10">
                        View All Exams
                    </a>
                </div>

                @if($nextExams->count())
                    <div class="dashboard-slider auto-scroll mt-6 flex gap-4 overflow-x-auto overflow-y-hidden pb-2 scroll-smooth" style="scrollbar-width: thin;">
                        @foreach($nextExams as $exam)
                            <article class="dashboard-exam-card dashboard-upcoming-card dashboard-reveal dashboard-reveal-delay-3">
                                <p class="dashboard-feed-label text-xs font-semibold uppercase tracking-[0.22em] text-amber-100/80">Scheduled Exam</p>
                                <h3 class="dashboard-feed-title mt-3 text-xl font-semibold text-white">{{ $exam->title }}</h3>

                                <div class="dashboard-feed-meta mt-5 space-y-2 text-sm text-slate-200/90">
                                    <p>{{ \Carbon\Carbon::parse($exam->start_time)->format('d M Y, h:i A') }}</p>
                                    <p>{{ $exam->duration_minutes }} mins duration</p>
                                    <p>{{ $exam->questions_count }} questions</p>
                                </div>

                                <a href="{{ route('student.exams.start', $exam->id) }}"
                                   class="dashboard-feed-action mt-5 inline-flex items-center justify-center rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400">
                                    Start Exam
                                </a>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-6 text-sm text-slate-200">
                        No upcoming exams are scheduled right now.
                    </div>
                @endif
            </section>

            <div class="order-4 grid gap-6 xl:grid-cols-2">
                <section class="dashboard-panel p-5 sm:p-7 dashboard-reveal dashboard-reveal-delay-3">
                    <div class="border-b border-white/10 pb-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Quick Access</p>
                        <h3 class="mt-2 text-2xl font-semibold text-white">Student Actions</h3>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <a href="{{ route('student.exams.index') }}" class="dashboard-action-card">
                            <span class="dashboard-action-title">Browse Exams</span>
                            <span class="dashboard-action-copy">Review available and upcoming assessments.</span>
                        </a>
                        <a href="{{ route('student.results.index') }}" class="dashboard-action-card">
                            <span class="dashboard-action-title">View Results</span>
                            <span class="dashboard-action-copy">Check completed attempts and score history.</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="dashboard-action-card">
                            <span class="dashboard-action-title">Update Profile</span>
                            <span class="dashboard-action-copy">Keep identity, contact and academic details current.</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="dashboard-action-card">
                            <span class="dashboard-action-title">Security Settings</span>
                            <span class="dashboard-action-copy">Change your password and manage account controls.</span>
                        </a>
                    </div>
                </section>

                <section class="dashboard-panel p-5 sm:p-7 dashboard-reveal dashboard-reveal-delay-4">
                    <div class="border-b border-white/10 pb-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Exam Guidance</p>
                        <h3 class="mt-2 text-2xl font-semibold text-white">Before You Start</h3>
                    </div>

                    <div class="mt-6 grid gap-4">
                        <div class="dashboard-guidance-card">
                            <span class="dashboard-guidance-title">One attempt per exam</span>
                            <p class="dashboard-guidance-copy">Each published exam can be attempted only once, so review instructions carefully before starting.</p>
                        </div>
                        <div class="dashboard-guidance-card">
                            <span class="dashboard-guidance-title">Timed submissions</span>
                            <p class="dashboard-guidance-copy">The timer is enforced strictly and your exam will auto-submit when the duration ends.</p>
                        </div>
                        <div class="dashboard-guidance-card">
                            <span class="dashboard-guidance-title">Stay in the exam window</span>
                            <p class="dashboard-guidance-copy">Avoid refreshing or using back navigation during an active exam session.</p>
                        </div>
                    </div>
                </section>
            </div>
            
            {{-- 🏆 LEADERBOARD --}}
            <section class="order-2 md:order-3 dashboard-panel p-5 sm:p-7 dashboard-reveal dashboard-reveal-delay-4">
                <div class="border-b border-white/10 pb-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Leaderboard</p>
                        <h3 class="mt-2 text-2xl font-semibold text-white">Top Performers</h3>
                    <p class="mt-2 text-sm text-slate-400">
                        {{ $user->department ?? 'All Departments' }} | {{ $user->semester ?? 'All Semesters' }}
                    </p>
                </div>

    <div class="mt-6 space-y-4">
        @foreach ($topStudents as $index => $row)

        <div class="flex items-center justify-between dashboard-stat leaderboard-card">

            {{-- LEFT --}}
            <div class="flex items-center gap-3">

                {{-- Rank --}}
                <div class="text-lg font-bold w-8
                    @if($loop->first) text-yellow-300
                    @elseif($loop->iteration == 2) text-gray-300
                    @elseif($loop->iteration == 3) text-orange-300
                    @endif
                ">
                    @if($loop->first) 🥇
                    @elseif($loop->iteration == 2) 🥈
                    @elseif($loop->iteration == 3) 🥉
                    @else #{{ $index + 1 }}
                    @endif
                </div>

                {{-- Profile --}}
                <img 
                    src="{{ $row->user->profilePhotoUrl() }}" 
                    class="w-10 h-10 rounded-full object-cover border border-white/20"
                >

                {{-- Name --}}
                <span class="text-white font-semibold">
                    {{ $row->user->name }}
                </span>
            </div>

            {{-- RIGHT --}}
            <div class="text-green-400 font-bold text-lg">
                {{ number_format($row->avg_percentage, 2) }}%
            </div>

        </div>

        @endforeach
    </div>
</section>
            
        </div>
    </div>

    <style>
        .dashboard-page {
            position: relative;
        }

        .dashboard-hero {
            position: relative;
            background:
                linear-gradient(135deg, rgba(10, 16, 30, 0.78), rgba(15, 23, 42, 0.58)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.04));
            backdrop-filter: blur(12px) saturate(125%);
            -webkit-backdrop-filter: blur(12px) saturate(125%);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.18),
                inset 0 -1px 0 rgba(255, 255, 255, 0.06),
                0 28px 70px rgba(2, 6, 23, 0.45);
        }

        .dashboard-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 20%, rgba(255, 255, 255, 0.16) 40%, transparent 58%);
            transform: translateX(-120%);
            animation: dashboardHeroSweep 8s ease-in-out infinite;
            pointer-events: none;
        }

        .dashboard-panel,
        .dashboard-sidecard {
            position: relative;
            min-width: 0;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.06)),
                rgba(15, 23, 42, 0.70);
            backdrop-filter: blur(10px) saturate(125%);
            -webkit-backdrop-filter: blur(10px) saturate(125%);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.18),
                0 20px 50px rgba(2, 6, 23, 0.38);
            border-radius: 24px;
        }

        .dashboard-panel::before,
        .dashboard-sidecard::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.14), transparent 34%);
            pointer-events: none;
        }

        .dashboard-stat,
        .dashboard-exam-card,
        .dashboard-action-card,
        .dashboard-guidance-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.06)),
                rgba(15, 23, 42, 0.42);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            padding: 1rem;
            transition: transform 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease;
        }

        .dashboard-stat:hover,
        .dashboard-exam-card:hover,
        .dashboard-action-card:hover,
        .dashboard-guidance-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.18);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.14),
                0 16px 28px rgba(2, 6, 23, 0.2);
        }

        .dashboard-stat-total {
            background:
                linear-gradient(180deg, rgba(250, 204, 21, 0.22), rgba(255, 255, 255, 0.06)),
                rgba(15, 23, 42, 0.42);
        }

        .dashboard-stat-upcoming {
            background:
                linear-gradient(180deg, rgba(56, 189, 248, 0.22), rgba(255, 255, 255, 0.06)),
                rgba(15, 23, 42, 0.42);
        }

        .dashboard-stat-completed {
            background:
                linear-gradient(180deg, rgba(52, 211, 153, 0.22), rgba(255, 255, 255, 0.06)),
                rgba(15, 23, 42, 0.42);
        }

        .dashboard-stat-score {
            background:
                linear-gradient(180deg, rgba(244, 114, 182, 0.22), rgba(255, 255, 255, 0.06)),
                rgba(15, 23, 42, 0.42);
        }

        .dashboard-stat-label,
        .dashboard-mini-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgb(162 173 194);
        }

        .dashboard-stat-label-gold {
            color: rgb(253 224 71);
        }

        .dashboard-stat-label-soft {
            color: rgb(226 232 240);
        }

        .dashboard-stat-value,
        .dashboard-mini-value {
            display: block;
            margin-top: 0.7rem;
            color: white;
            font-size: 1.85rem;
            line-height: 1.1;
            font-weight: 600;
        }

        .dashboard-stat-value-total {
            color: rgb(255 243 176);
            text-shadow: 0 0 20px rgba(251, 191, 36, 0.18);
        }

        .dashboard-stat-value-upcoming {
            color: rgb(186 230 253);
            text-shadow: 0 0 18px rgba(56, 189, 248, 0.18);
        }

        .dashboard-stat-value-completed {
            color: rgb(209 250 229);
            text-shadow: 0 0 18px rgba(52, 211, 153, 0.18);
        }

        .dashboard-stat-value-score {
            color: rgb(251 207 232);
            text-shadow: 0 0 18px rgba(244, 114, 182, 0.2);
        }

        .dashboard-stat-empty {
            font-size: 1.15rem;
            line-height: 1.4;
            color: rgb(251 191 36);
        }

        .dashboard-title {
            text-shadow: 0 6px 24px rgba(15, 23, 42, 0.34);
        }

        .dashboard-action-card,
        .dashboard-guidance-card {
            display: grid;
            gap: 0.4rem;
            text-decoration: none;
        }

        .dashboard-action-title,
        .dashboard-guidance-title {
            color: white;
            font-size: 1rem;
            font-weight: 600;
        }

        .dashboard-action-copy,
        .dashboard-guidance-copy {
            color: rgb(226 232 240);
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .dashboard-slider {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            scrollbar-color: rgba(255,255,255,0.18) transparent;
            overscroll-behavior-x: contain;
        }

        .dashboard-upcoming-card {
            flex: 0 0 300px;
            min-width: 300px;
            max-width: 300px;
        }

        .dashboard-feed-label {
            font-size: 0.68rem;
            letter-spacing: 0.18em;
        }

        .dashboard-feed-title {
            font-size: 1.08rem;
            line-height: 1.35;
        }

        .dashboard-feed-meta {
            font-size: 0.9rem;
            line-height: 1.45;
        }

        .dashboard-feed-action {
            padding: 0.65rem 1rem;
            font-size: 0.88rem;
        }

        .dashboard-exam-card h3 {
            text-wrap: balance;
            word-break: break-word;
        }

        .dashboard-reveal {
            opacity: 0;
            transform: translateY(22px);
            animation: dashboardReveal 0.75s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            will-change: transform, opacity;
        }

        .dashboard-reveal-delay-1 { animation-delay: 0.06s; }
        .dashboard-reveal-delay-2 { animation-delay: 0.14s; }
        .dashboard-reveal-delay-3 { animation-delay: 0.22s; }
        .dashboard-reveal-delay-4 { animation-delay: 0.3s; }

        .dashboard-shimmer {
            position: relative;
            overflow: hidden;
        }

        .dashboard-shimmer::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 20%, rgba(255, 255, 255, 0.22) 45%, transparent 70%);
            transform: translateX(-135%);
            animation: dashboardBadgeShimmer 6.5s ease-in-out infinite;
        }

        @keyframes dashboardReveal {
            from {
                opacity: 0;
                transform: translateY(22px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes dashboardHeroSweep {
            0%, 100% {
                transform: translateX(-120%);
            }
            45%, 55% {
                transform: translateX(120%);
            }
        }

        @keyframes dashboardBadgeShimmer {
            0%, 100% {
                transform: translateX(-135%);
            }
            48%, 60% {
                transform: translateX(135%);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .dashboard-hero::after,
            .dashboard-shimmer::after,
            .dashboard-reveal {
                animation: none !important;
                opacity: 1;
                transform: none;
            }

            .dashboard-stat,
            .dashboard-mini-card,
            .dashboard-exam-card,
            .dashboard-action-card,
            .dashboard-guidance-card {
                transition: none;
            }
        }

        @media (max-width: 640px) {
            .dashboard-page {
                overflow-x: hidden;
            }

            .dashboard-stat-value {
                margin-top: 0.55rem;
                font-size: 1.35rem;
            }

            .dashboard-stat {
                padding: 0.9rem;
            }

            .dashboard-stat-label {
                font-size: 0.62rem;
                letter-spacing: 0.16em;
            }

            .dashboard-stat-empty {
                font-size: 0.95rem;
            }

            .dashboard-slider {
                display: grid;
                gap: 0.9rem;
                overflow-x: hidden;
                overflow-y: visible;
                padding-bottom: 0.35rem;
                scroll-padding-inline: 0;
            }

            .dashboard-upcoming-card {
                flex: 1 1 auto;
                width: 100%;
                min-width: 0;
                max-width: 100%;
                padding: 0.8rem;
                border-radius: 18px;
            }

            .dashboard-feed-label {
                font-size: 0.6rem;
                letter-spacing: 0.14em;
            }

            .dashboard-feed-title {
                margin-top: 0.55rem;
                font-size: 0.94rem;
                line-height: 1.28;
            }

            .dashboard-feed-meta {
                margin-top: 0.8rem;
                font-size: 0.78rem;
                line-height: 1.32;
            }

            .dashboard-feed-action {
                margin-top: 0.8rem;
                padding: 0.5rem 0.85rem;
                font-size: 0.78rem;
            }
        }

        /* 🏆 Leaderboard Glow Animation */
.leaderboard-card {
    position: relative;
    transition: all 0.3s ease;
}

/* 🥇 GOLD GLOW */
.leaderboard-card:nth-child(1) {
    animation: goldGlow 2.5s ease-in-out infinite alternate;
    transform: scale(1.03);
}

/* 🥈 SILVER GLOW */
.leaderboard-card:nth-child(2) {
    animation: silverGlow 3s ease-in-out infinite alternate;
}

/* 🥉 BRONZE GLOW */
.leaderboard-card:nth-child(3) {
    animation: bronzeGlow 3.5s ease-in-out infinite alternate;
}

@keyframes goldGlow {
    from {
        box-shadow: 0 0 10px rgba(250, 204, 21, 0.2);
    }
    to {
        box-shadow: 0 0 25px rgba(250, 204, 21, 0.6);
    }
}

@keyframes silverGlow {
    from {
        box-shadow: 0 0 10px rgba(209, 213, 219, 0.2);
    }
    to {
        box-shadow: 0 0 25px rgba(209, 213, 219, 0.5);
    }
}

@keyframes bronzeGlow {
    from {
        box-shadow: 0 0 10px rgba(251, 146, 60, 0.2);
    }
    to {
        box-shadow: 0 0 25px rgba(251, 146, 60, 0.5);
    }
}
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const counters = document.querySelectorAll(".counter");

        counters.forEach((counter) => {
            const target = parseFloat(counter.getAttribute("data-value"));
            const isPercentage = counter.getAttribute("data-type") === "percentage";

            let count = 0;
            const speed = Math.max(20, 1000 / (target || 1));

            const update = () => {
                if (count < target) {
                    count += target / 60;

                    if (isPercentage) {
                        counter.innerText = count.toFixed(2) + "%";
                    } else {
                        counter.innerText = Math.ceil(count);
                    }

                    setTimeout(update, speed);
                } else {
                    counter.innerText = isPercentage
                        ? target.toFixed(2) + "%"
                        : target;
                }
            };

            update();
        });
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const slider = document.querySelector(".auto-scroll");
        if (!slider) return;

        let scrollSpeed = 0.5;
        let isHovered = false;

        slider.addEventListener("mouseenter", () => isHovered = true);
        slider.addEventListener("mouseleave", () => isHovered = false);

        function autoScroll() {
            if (!isHovered) {
                slider.scrollLeft += scrollSpeed;

                if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 5) {
                    slider.scrollLeft = 0;
                }
            }
            requestAnimationFrame(autoScroll);
        }

        autoScroll();
    });
    </script>
</x-app-layout>
