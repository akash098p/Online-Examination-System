@extends('layouts.admin')

@section('content')
<style>
.violation-hero-card,
.violation-student-card,
.violation-capture-card {
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
.violation-student-card::before,
.violation-capture-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), transparent 35%);
    pointer-events: none;
}
</style>

<div class="space-y-6">
    <div class="violation-hero-card p-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-200">Exam Violations</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">{{ $exam->title }}</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-300">
                Review the students flagged during this exam and inspect the stored screenshots.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <form method="GET" action="{{ route('admin.violations.exam', $exam) }}" class="min-w-[240px]">
                <input
                    type="text"
                    name="reason"
                    value="{{ $reason }}"
                    placeholder="Filter by reason"
                    class="w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white"
                >
            </form>
            <a href="{{ route('admin.violations.index') }}" class="admin-action-btn bg-slate-700 text-white">Back</a>
        </div>
        </div>
    </div>

    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-white">Students With Violations</h2>
            <span class="text-sm text-slate-300">{{ $studentSummaries->count() }} students</span>
        </div>

        @forelse($studentSummaries as $summary)
            <article class="violation-student-card p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ $summary->user->name }}</h3>
                        <p class="mt-1 text-sm text-slate-300">{{ $summary->user->email }}</p>
                        <p class="mt-1 text-xs text-slate-400">Registration No: {{ $summary->user->registration_no ?? 'N/A' }}</p>
                        <p class="mt-2 text-xs text-slate-500">
                            Last violation: {{ \Carbon\Carbon::parse($summary->latest_violation_at)->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-xs uppercase tracking-[0.18em] text-rose-200">Violations</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $summary->violations_count }}</p>
                        </div>
                        <a href="{{ route('admin.violations.student', [$exam, $summary->user->id]) }}" class="admin-action-btn bg-rose-600 text-white">
                            Open Student Timeline
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-white/12 bg-white/[0.03] p-6 text-sm text-slate-300">
                No students match this filter.
            </div>
        @endforelse
    </section>

    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-white">Recent Captures</h2>
            <span class="text-sm text-slate-300">{{ $recentViolations->count() }} screenshots</span>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @forelse($recentViolations as $violation)
                <article class="violation-capture-card p-4">
                    @php($imageUrl = route('admin.violations.image', $violation))
                    <a href="{{ $imageUrl }}" target="_blank">
                        <img
                            src="{{ $imageUrl }}"
                            alt="Violation screenshot"
                            class="h-44 w-full rounded-2xl object-cover border border-white/10"
                        >
                    </a>
                    <div class="mt-4">
                        <p class="text-sm font-semibold text-white">{{ $violation->reason }}</p>
                        <p class="mt-1 text-sm text-slate-300">{{ $violation->user?->name ?? 'Unknown student' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $violation->created_at?->format('d M Y, h:i A') }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-white/12 bg-white/[0.03] p-6 text-sm text-slate-300 md:col-span-2 xl:col-span-4">
                    No screenshots available for this exam yet.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
