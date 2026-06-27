@extends('layouts.admin')

@section('content')
<style>
    .manage-exams-page {
        position: relative;
    }

    .manage-exams-header,
    .department-summary-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 1.75rem;
        background:
            linear-gradient(135deg, rgba(10, 16, 30, 0.78), rgba(15, 23, 42, 0.58)),
            linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.04));
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.18),
            inset 0 -1px 0 rgba(255, 255, 255, 0.06),
            0 28px 70px rgba(2, 6, 23, 0.42);
        backdrop-filter: blur(12px) saturate(125%);
        -webkit-backdrop-filter: blur(12px) saturate(125%);
    }

    .manage-exams-header::before,
    .department-summary-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), transparent 34%);
        pointer-events: none;
    }

    .manage-exams-header::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent 20%, rgba(255, 255, 255, 0.16) 40%, transparent 58%);
        transform: translateX(-120%);
        animation: manageExamsHeroSweep 8s ease-in-out infinite;
        pointer-events: none;
    }

    .department-summary-grid {
        display: grid;
        gap: 1.5rem;
    }

    .department-summary-card {
        display: block;
        padding: 1rem 1.15rem;
        transition: transform 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease;
    }

    .department-summary-card:hover {
        transform: translateY(-4px);
        border-color: rgba(255, 255, 255, 0.22);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.18),
            0 24px 55px rgba(2, 6, 23, 0.38);
    }

    .department-summary-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.9rem;
        margin-top: 1.5rem;
    }

    .department-summary-pill {
        border-radius: 1.15rem;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0.05)),
            rgba(15, 23, 42, 0.4);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
        padding: 1rem;
    }

    .department-summary-pill p {
        position: relative;
        z-index: 1;
    }

    .department-summary-pill::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.14), transparent 42%);
        pointer-events: none;
    }

    @keyframes manageExamsHeroSweep {
        0%,
        100% {
            transform: translateX(-120%);
        }

        50% {
            transform: translateX(120%);
        }
    }

    @media (min-width: 768px) {
        .department-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .department-summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

</style>

<div class="manage-exams-page space-y-6">
    <div class="manage-exams-header p-6">
        <div class="flex justify-between items-center gap-4 flex-wrap">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.26em] text-amber-200">Manage Exams</p>
                <h1 class="mt-2 text-2xl font-bold text-white">Manage Exams for All Departments</h1>
                <p class="mt-2 text-sm text-slate-300">Select a department to open its semester-wise exams.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <section class="department-summary-grid">
        @foreach($departmentSummaries as $summary)
            <a
                href="{{ route('admin.exams.department', $summary['name']) }}"
                class="department-summary-card"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-200">Department</p>
                        <h2 class="mt-2 text-xl leading-tight font-semibold text-white">{{ $summary['name'] }}</h2>
                    </div>
                    <span class="rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-amber-100">
                        View
                    </span>
                </div>

                <div class="department-summary-stats">
                    <div class="department-summary-pill">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-slate-400">Total Exams</p>
                        <p class="mt-2 text-xl font-semibold text-white">{{ $summary['total_exams'] }}</p>
                    </div>
                    <div class="department-summary-pill">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-emerald-300">Active Exams</p>
                        <p class="mt-2 text-xl font-semibold text-emerald-200">{{ $summary['active_exams'] }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </section>
</div>
@endsection
