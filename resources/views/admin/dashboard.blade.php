@extends('layouts.admin')

@section('content')
<div class="admin-dashboard-page min-h-screen py-2 sm:py-4">
    <section class="admin-dashboard-hero overflow-hidden rounded-[28px] border border-white/10">
        <div class="px-6 py-8 sm:px-8 lg:px-10 lg:py-9">
            <div class="space-y-5 admin-dashboard-reveal admin-dashboard-reveal-delay-1">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-100 shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] admin-dashboard-shimmer">
                    Admin Command Center
                </div>

                <div class="admin-dashboard-hero-top">
                    <div class="space-y-3">
                        <p class="text-lg font-medium text-amber-100/90 sm:text-xl">Control exams, students and system visibility</p>
                        <h1 class="admin-dashboard-title max-w-5xl text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                            Admin Dashboard
                        </h1>
                        <p class="max-w-3xl text-base leading-7 text-slate-200 sm:text-lg">
                            Review platform activity, manage academic workflows and move quickly across the most important admin tools.
                        </p>
                    </div>

                    <div class="admin-profile-request-card">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-100/80">Notifications</p>
                                <!-- <h2 class="mt-2 text-lg font-semibold text-white">Academic Change Requests</h2> -->
                            </div>
                            <span class="admin-profile-request-count">{{ $pendingProfileRequestCount ?? 0 }}</span>
                        </div>

                        <div class="mt-4 rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                            <p class="text-sm leading-6 text-slate-200">
                                {{ ($pendingProfileRequestCount ?? 0) > 0
                                    ? 'Review student department/semester change requests in one place.'
                                    : 'No pending academic change requests right now.' }}
                            </p>
                            <a
                                href="{{ route('admin.profile_requests.index') }}"
                                class="admin-profile-request-view-btn"
                            >
                                View Requests
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <a href="{{ route('admin.students.index') }}" class="admin-dashboard-stat admin-dashboard-stat-students admin-dashboard-reveal admin-dashboard-reveal-delay-2">
                        <span class="admin-dashboard-stat-label admin-dashboard-stat-label-gold">Total Students</span>
                        <strong class="admin-dashboard-stat-value admin-dashboard-stat-value-students counter" data-value="{{ $totalStudents }}">0</strong>
                        <span class="admin-dashboard-stat-copy">Open student management</span>
                    </a>

                    <a href="{{ route('admin.exams.index') }}" class="admin-dashboard-stat admin-dashboard-stat-exams admin-dashboard-reveal admin-dashboard-reveal-delay-3">
                        <span class="admin-dashboard-stat-label admin-dashboard-stat-label-soft">Total Exams</span>
                        <strong class="admin-dashboard-stat-value admin-dashboard-stat-value-exams counter" data-value="{{ $totalExams }}">0</strong>
                        <span class="admin-dashboard-stat-copy">Manage exam catalog</span>
                    </a>

                    <a href="{{ route('admin.exams.index') }}" class="admin-dashboard-stat admin-dashboard-stat-active admin-dashboard-reveal admin-dashboard-reveal-delay-4">
                        <span class="admin-dashboard-stat-label admin-dashboard-stat-label-soft">Active Exams</span>
                        <strong class="admin-dashboard-stat-value admin-dashboard-stat-value-active counter" data-value="{{ $activeExams }}">0</strong>
                        <span class="admin-dashboard-stat-copy">View published exams</span>
                    </a>

                    <a href="{{ route('admin.results.index') }}" class="admin-dashboard-stat admin-dashboard-stat-results admin-dashboard-reveal admin-dashboard-reveal-delay-4">
                        <span class="admin-dashboard-stat-label admin-dashboard-stat-label-soft">Results</span>
                        <strong class="admin-dashboard-stat-value admin-dashboard-stat-value-results counter" data-value="{{ $totalResults }}">0</strong>
                        <span class="admin-dashboard-stat-copy">Check result summaries</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="admin-dashboard-panel p-5 sm:p-7 admin-dashboard-reveal admin-dashboard-reveal-delay-2">
            <div class="border-b border-white/10 pb-5">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Administration</p>
                <h2 class="mt-2 text-2xl font-semibold text-white">Admin Capabilities</h2>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="admin-dashboard-card">
                    <span class="admin-dashboard-card-title">Exam Operations</span>
                    <p class="admin-dashboard-card-copy">Create and manage exams, configure timing and publish availability windows.</p>
                </div>
                <div class="admin-dashboard-card">
                    <span class="admin-dashboard-card-title">Question Management</span>
                    <p class="admin-dashboard-card-copy">Add question banks, organize subjects and maintain marking rules.</p>
                </div>
                <div class="admin-dashboard-card">
                    <span class="admin-dashboard-card-title">Student Access</span>
                    <p class="admin-dashboard-card-copy">Manage student records, permissions and account lifecycle actions.</p>
                </div>
                <div class="admin-dashboard-card">
                    <span class="admin-dashboard-card-title">Scheduling Control</span>
                    <p class="admin-dashboard-card-copy">Coordinate start windows, deadlines and academic access visibility.</p>
                </div>
            </div>
        </section>

        <section class="admin-dashboard-panel p-5 sm:p-7 admin-dashboard-reveal admin-dashboard-reveal-delay-3">
            <div class="border-b border-white/10 pb-5">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Oversight</p>
                <h2 class="mt-2 text-2xl font-semibold text-white">Monitoring and Results</h2>
            </div>

            <div class="mt-6 grid gap-4">
                <div class="admin-dashboard-card">
                    <span class="admin-dashboard-card-title">Live Attempt Visibility</span>
                    <p class="admin-dashboard-card-copy">Review student exam attempts and follow activity across active assessments.</p>
                </div>
                <div class="admin-dashboard-card">
                    <span class="admin-dashboard-card-title">Performance Insights</span>
                    <p class="admin-dashboard-card-copy">Track pass trends, review result summaries and support reporting workflows.</p>
                </div>
                <div class="admin-dashboard-card">
                    <span class="admin-dashboard-card-title">Audit Responsibility</span>
                    <p class="admin-dashboard-card-copy">Review suspicious activity, protect fairness and keep academic records reliable.</p>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    .admin-dashboard-page {
        position: relative;
    }

    .admin-dashboard-hero {
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

    .admin-dashboard-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent 20%, rgba(255, 255, 255, 0.16) 40%, transparent 58%);
        transform: translateX(-120%);
        animation: adminDashboardHeroSweep 8s ease-in-out infinite;
        pointer-events: none;
    }

    .admin-dashboard-panel {
        position: relative;
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

    .admin-dashboard-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), transparent 34%);
        pointer-events: none;
    }

    .admin-dashboard-stat,
    .admin-dashboard-card {
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

    .admin-dashboard-hero-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.5rem;
    }

    .admin-profile-request-card {
        position: relative;
        width: min(100%, 24rem);
        border: 1px solid rgba(250, 204, 21, 0.18);
        border-radius: 1.5rem;
        background:
            linear-gradient(180deg, rgba(250, 204, 21, 0.09), rgba(255, 255, 255, 0.04)),
            rgba(15, 23, 42, 0.5);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.16);
        padding: 1rem;
    }

    .admin-profile-request-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.6rem;
        height: 2.6rem;
        border-radius: 999px;
        border: 1px solid rgba(250, 204, 21, 0.25);
        background: rgba(250, 204, 21, 0.12);
        color: rgb(253 224 71);
        font-size: 1rem;
        font-weight: 700;
    }

    .admin-profile-request-view-btn,
    .admin-profile-request-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.8rem;
        padding: 0.55rem 0.85rem;
        font-size: 0.78rem;
        font-weight: 700;
        color: white;
        transition: filter 0.2s ease;
    }

    .admin-profile-request-view-btn {
        margin-top: 0.9rem;
        background: rgba(37, 99, 235, 0.9);
    }

    .admin-profile-request-btn:hover {
        filter: brightness(1.08);
    }

    .admin-profile-request-approve {
        background: rgba(5, 150, 105, 0.9);
    }

    .admin-profile-request-reject {
        background: rgba(220, 38, 38, 0.88);
    }

    .admin-dashboard-stat:hover,
    .admin-dashboard-card:hover {
        transform: translateY(-4px);
        border-color: rgba(255, 255, 255, 0.22);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.18),
            0 16px 28px rgba(2, 6, 23, 0.28);
    }

    .admin-dashboard-stat-students {
        background:
            linear-gradient(180deg, rgba(250, 204, 21, 0.22), rgba(255, 255, 255, 0.06)),
            rgba(15, 23, 42, 0.42);
    }

    .admin-dashboard-stat-exams {
        background:
            linear-gradient(180deg, rgba(56, 189, 248, 0.22), rgba(255, 255, 255, 0.06)),
            rgba(15, 23, 42, 0.42);
    }

    .admin-dashboard-stat-active {
        background:
            linear-gradient(180deg, rgba(34, 197, 94, 0.22), rgba(255, 255, 255, 0.06)),
            rgba(15, 23, 42, 0.42);
    }

    .admin-dashboard-stat-results {
        background:
            linear-gradient(180deg, rgba(244, 114, 182, 0.22), rgba(255, 255, 255, 0.06)),
            rgba(15, 23, 42, 0.42);
    }

    .admin-dashboard-stat-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: rgb(203 213 225);
    }

    .admin-dashboard-stat-label-gold {
        color: rgb(253 224 71);
    }

    .admin-dashboard-stat-label-soft {
        color: rgb(236 245 255);
    }

    .admin-dashboard-stat-value {
        display: block;
        margin-top: 0.7rem;
        font-size: 1.85rem;
        line-height: 1.1;
        font-weight: 700;
    }

    .admin-dashboard-stat-value-students {
        color: rgb(255 243 176);
        text-shadow: 0 0 22px rgba(251, 191, 36, 0.24);
    }

    .admin-dashboard-stat-value-exams {
        color: rgb(179 229 252);
        text-shadow: 0 0 20px rgba(56, 189, 248, 0.24);
    }

    .admin-dashboard-stat-value-active {
        color: rgb(187 247 208);
        text-shadow: 0 0 22px rgba(34, 197, 94, 0.24);
    }

    .admin-dashboard-stat-value-results {
        color: rgb(251 207 232);
        text-shadow: 0 0 22px rgba(244, 114, 182, 0.24);
    }

    .admin-dashboard-stat-copy {
        display: block;
        margin-top: 0.65rem;
        color: rgb(229 231 235);
        font-size: 0.88rem;
        line-height: 1.5;
    }

    .admin-dashboard-title {
        text-shadow: 0 6px 24px rgba(15, 23, 42, 0.34);
    }

    .admin-dashboard-card {
        display: grid;
        gap: 0.4rem;
    }

    .admin-dashboard-card-title {
        color: white;
        font-size: 1rem;
        font-weight: 600;
    }

    .admin-dashboard-card-copy {
        color: rgb(226 232 240);
        font-size: 0.92rem;
        line-height: 1.6;
    }

    .admin-dashboard-reveal {
        opacity: 0;
        transform: translateY(22px);
        animation: adminDashboardReveal 0.75s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        will-change: transform, opacity;
    }

    .admin-dashboard-reveal-delay-1 { animation-delay: 0.06s; }
    .admin-dashboard-reveal-delay-2 { animation-delay: 0.14s; }
    .admin-dashboard-reveal-delay-3 { animation-delay: 0.22s; }
    .admin-dashboard-reveal-delay-4 { animation-delay: 0.3s; }

    .admin-dashboard-shimmer {
        position: relative;
        overflow: hidden;
    }

    .admin-dashboard-shimmer::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(110deg, transparent 20%, rgba(255, 255, 255, 0.22) 45%, transparent 70%);
        transform: translateX(-135%);
        animation: adminDashboardBadgeShimmer 6.5s ease-in-out infinite;
    }

    @keyframes adminDashboardReveal {
        from {
            opacity: 0;
            transform: translateY(22px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes adminDashboardHeroSweep {
        0%, 100% {
            transform: translateX(-120%);
        }
        45%, 55% {
            transform: translateX(120%);
        }
    }

    @keyframes adminDashboardBadgeShimmer {
        0%, 100% {
            transform: translateX(-135%);
        }
        48%, 60% {
            transform: translateX(135%);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-dashboard-hero::after,
        .admin-dashboard-shimmer::after,
        .admin-dashboard-reveal {
            animation: none !important;
            opacity: 1;
            transform: none;
        }

        .admin-dashboard-stat,
        .admin-dashboard-card {
            transition: none;
        }
    }

    @media (max-width: 640px) {
        .admin-dashboard-hero-top {
            flex-direction: column;
        }

        .admin-profile-request-card {
            width: 100%;
        }

        .admin-dashboard-stat {
            padding: 0.9rem;
        }

        .admin-dashboard-stat-label {
            font-size: 0.62rem;
            letter-spacing: 0.16em;
        }

        .admin-dashboard-stat-value {
            margin-top: 0.55rem;
            font-size: 1.5rem;
        }

        .admin-dashboard-stat-copy {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            line-height: 1.35;
        }
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".counter");

    counters.forEach((counter) => {
        const target = parseFloat(counter.getAttribute("data-value")) || 0;
        let count = 0;
        const speed = Math.max(20, 1000 / (target || 1));

        const update = () => {
            if (count < target) {
                count += Math.ceil(target / 40);
                if (count > target) count = target;
                counter.textContent = Math.floor(count);
                setTimeout(update, speed);
            } else {
                counter.textContent = Math.floor(target);
            }
        };

        update();
    });
});
</script>
@endsection
