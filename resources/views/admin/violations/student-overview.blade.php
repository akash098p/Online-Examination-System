@extends('layouts.admin')

@section('content')
<style>
.violation-hero-card,
.violation-summary-card,
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
.violation-summary-card::before,
.violation-feed-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), transparent 35%);
    pointer-events: none;
}
</style>

<div class="space-y-6">
    <div class="violation-hero-card p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-200">Student Violation Overview</p>
                <h1 class="mt-2 text-3xl font-semibold text-white">{{ $student->name }}</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-300">
                    Review all exams where this student triggered proctoring violations and jump into the specific timeline for each exam.
                </p>
                <div class="mt-4 flex flex-wrap gap-3 text-sm text-slate-300">
                    <span>Email: {{ $student->email }}</span>
                    <span>Registration No: {{ $student->registration_no ?? 'N/A' }}</span>
                    <span>Semester: {{ $student->semester ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.students.show', $student->id) }}" class="admin-action-btn bg-indigo-600 text-white">Back to Student</a>
                <a href="{{ route('admin.violations.index') }}" class="admin-action-btn bg-slate-700 text-white">Violation Dashboard</a>
            </div>
        </div>
    </div>

    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-white">Exam Breakdown</h2>
            <span class="text-sm text-slate-300">{{ $examSummaries->count() }} exams</span>
        </div>

        @forelse($examSummaries as $summary)
            <article class="violation-summary-card p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ $summary->exam->title }}</h3>
                        <p class="mt-1 text-sm text-slate-300">{{ $summary->exam->subject ?: 'No subject' }}</p>
                        <p class="mt-2 text-xs text-slate-500">
                            Last violation: {{ \Carbon\Carbon::parse($summary->latest_violation_at)->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-xs uppercase tracking-[0.18em] text-rose-200">Violations</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $summary->violations_count }}</p>
                        </div>
                        <a href="{{ route('admin.violations.student', [$summary->exam, $student->id]) }}" class="admin-action-btn bg-rose-600 text-white">
                            Open Exam Timeline
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-white/12 bg-white/[0.03] p-6 text-sm text-slate-300">
                No violations found for this student.
            </div>
        @endforelse
    </section>

    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-white">Recent Captures</h2>
            <span class="text-sm text-slate-300">{{ $recentViolations->count() }} items</span>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($recentViolations as $violation)
                @php($imageUrl = route('admin.violations.image', $violation))
                <article class="violation-feed-card p-4">
                    <a href="{{ $imageUrl }}" target="_blank">
                        <img src="{{ $imageUrl }}" alt="Violation evidence" class="h-56 w-full rounded-2xl object-cover border border-white/10">
                    </a>
                    <div class="mt-4">
                        <p class="text-sm font-semibold text-white">{{ $violation->reason }}</p>
                        <p class="mt-1 text-sm text-slate-300">{{ $violation->exam?->title ?? 'Unknown exam' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $violation->created_at?->format('d M Y, h:i:s A') }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
