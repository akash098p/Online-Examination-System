@extends('layouts.admin')

@section('content')
<style>
.violation-hero-card,
.violation-stat-card,
.violation-section-card,
.violation-feed-card {
    position: relative;
    overflow: hidden;
    border-radius: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.14);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02)),
        rgba(7, 12, 28, 0.48);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.12),
        0 24px 50px rgba(2, 6, 23, 0.26);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.violation-hero-card::before,
.violation-stat-card::before,
.violation-section-card::before,
.violation-feed-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), transparent 35%);
    pointer-events: none;
}

.violation-recent-list {
    display: grid;
    gap: 0.7rem;
    max-height: 640px;
    overflow-y: auto;
    padding-right: 0.2rem;
}

.violation-feed-card {
    padding: 0.8rem;
}

.violation-feed-row {
    display: grid;
    grid-template-columns: 72px minmax(0, 1fr) auto;
    gap: 0.75rem;
    align-items: center;
}

.violation-feed-thumb {
    width: 72px;
    height: 58px;
    border-radius: 0.85rem;
    object-fit: cover;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.violation-feed-copy {
    min-width: 0;
}

.violation-feed-copy p,
.violation-feed-copy span {
    display: block;
}

.violation-feed-title {
    color: white;
    font-size: 0.92rem;
    font-weight: 700;
    line-height: 1.2;
}

.violation-feed-meta {
    margin-top: 0.14rem;
    color: #cbd5e1;
    font-size: 0.84rem;
}

.violation-feed-sub {
    margin-top: 0.08rem;
    color: #94a3b8;
    font-size: 0.78rem;
    line-height: 1.3;
}

.violation-feed-time {
    color: #94a3b8;
    font-size: 0.76rem;
    text-align: right;
    white-space: nowrap;
}

.violation-summary-grid {
    display: grid;
    gap: 1rem;
}

.violation-highlight-grid {
    display: grid;
    gap: 0.85rem;
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.violation-highlight-card {
    border-radius: 1.15rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(15, 23, 42, 0.42);
    padding: 0.9rem 1rem;
}

.violation-highlight-label {
    color: #cbd5e1;
    font-size: 0.8rem;
    line-height: 1.3;
}

.violation-highlight-total {
    margin-top: 0.35rem;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

@media (max-width: 1280px) {
    .violation-recent-list {
        max-height: none;
    }
}

@media (max-width: 1024px) {
    .violation-highlight-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .violation-feed-row {
        grid-template-columns: 72px minmax(0, 1fr);
    }

    .violation-feed-thumb {
        width: 72px;
        height: 64px;
    }

    .violation-feed-time {
        grid-column: 2;
        text-align: left;
        margin-top: 0.3rem;
    }

    .violation-highlight-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="space-y-6">
    <div class="violation-hero-card p-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-200">Proctoring Review</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">Violation Dashboard</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-300">
                Review captured screenshots, grouped by exam, and drill into the students who triggered warnings.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.violations.index') }}" class="w-full max-w-md">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by exam, student, email, registration no, or reason"
                class="w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white"
            >
        </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="violation-stat-card p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Total Violations</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ $stats['total_violations'] }}</p>
        </div>
        <div class="violation-stat-card p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Affected Exams</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ $stats['affected_exams'] }}</p>
        </div>
        <div class="violation-stat-card p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Affected Students</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ $stats['affected_students'] }}</p>
        </div>
    </div>

    @if($reasonHighlights->isNotEmpty())
        <div class="violation-section-card p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">Top Violation Reasons</h2>
                    <p class="mt-1 text-sm text-slate-300">A quick summary of the most frequent triggers.</p>
                </div>
                <span class="text-sm text-slate-300">Top {{ $reasonHighlights->count() }}</span>
            </div>

            <div class="violation-highlight-grid mt-4">
                @foreach($reasonHighlights as $highlight)
                    <div class="violation-highlight-card">
                        <p class="violation-highlight-label">{{ $highlight->reason }}</p>
                        <p class="violation-highlight-total">{{ $highlight->total }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.8fr)]">
        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-white">Exam Summaries</h2>
                <span class="text-sm text-slate-300">{{ $examSummaries->count() }} exams with recorded violations</span>
            </div>

            <div class="violation-summary-grid">
                @forelse($examSummaries as $exam)
                    <article class="violation-section-card p-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="text-lg font-semibold text-white">{{ $exam->title }}</h3>
                                        <p class="mt-1 text-sm text-slate-300">{{ $exam->subject ?: 'No subject' }}</p>
                                    </div>
                                    <div class="text-left lg:text-right">
                                        <p class="text-xs uppercase tracking-[0.18em] text-rose-200">Violations</p>
                                        <p class="mt-1 text-2xl font-semibold text-white">{{ $exam->violations_count }}</p>
                                    </div>
                                </div>

                                <p class="mt-3 text-sm text-slate-400">
                                    Last violation:
                                    <span class="text-slate-200">{{ optional($exam->latest_violation_at)->format('d M Y, h:i A') ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('admin.violations.exam', $exam) }}" class="admin-action-btn bg-rose-600 text-white">
                                Review Exam Violations
                            </a>
                            <a href="{{ route('admin.exams.show', $exam) }}" class="admin-action-btn bg-slate-700 text-white">
                                Open Exam Details
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-white/12 bg-white/[0.03] p-6 text-sm text-slate-300">
                        No violations found yet.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-white">Recent Violations</h2>
                <span class="text-sm text-slate-300">Latest {{ $recentViolations->count() }}</span>
            </div>

            <div class="violation-recent-list">
                @forelse($recentViolations as $violation)
                    <article class="violation-feed-card p-4">
                        @php($imageUrl = route('admin.violations.image', $violation))
                        <div class="violation-feed-row">
                            <a href="{{ $imageUrl }}" target="_blank" class="shrink-0">
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="Violation capture"
                                    class="violation-feed-thumb"
                                >
                            </a>
                            <div class="violation-feed-copy">
                                <p class="violation-feed-title">{{ $violation->reason }}</p>
                                <span class="violation-feed-meta">
                                    {{ $violation->user?->name ?? 'Unknown student' }}
                                    @if($violation->user?->registration_no || $violation->user?->email)
                                        • {{ $violation->user?->registration_no ?? $violation->user?->email }}
                                    @endif
                                </span>
                                <span class="violation-feed-sub">{{ $violation->exam?->title ?? 'Unknown exam' }}</span>
                            </div>
                            <div class="violation-feed-time">{{ $violation->created_at?->format('d M, h:i A') }}</div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-white/12 bg-white/[0.03] p-6 text-sm text-slate-300">
                        No recent violations to show.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
