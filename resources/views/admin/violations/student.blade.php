@extends('layouts.admin')

@section('content')
<style>
.violation-hero-card,
.violation-stat-card,
.violation-breakdown-card,
.violation-evidence-card {
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
.violation-breakdown-card::before,
.violation-evidence-card::before {
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
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-200">Student Violation Timeline</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">{{ $student->name }}</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-300">
                Exam: {{ $exam->title }}. Review each recorded screenshot and the reason attached to it.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.violations.exam', $exam) }}" class="admin-action-btn bg-slate-700 text-white">Back to Exam</a>
            <a href="{{ route('admin.students.show', $student->id) }}" class="admin-action-btn bg-indigo-600 text-white">Open Student</a>
        </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="violation-stat-card p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Registration No</p>
            <p class="mt-3 text-lg font-semibold text-white">{{ $student->registration_no ?? 'N/A' }}</p>
        </div>
        <div class="violation-stat-card p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Email</p>
            <p class="mt-3 text-lg font-semibold text-white break-all">{{ $student->email }}</p>
        </div>
        <div class="violation-stat-card p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Total Violations</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ $violations->count() }}</p>
        </div>
    </div>

    <section class="space-y-4">
        <h2 class="text-xl font-semibold text-white">Reason Breakdown</h2>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($reasonBreakdown as $reason => $count)
                <div class="violation-breakdown-card p-4">
                    <p class="text-sm font-semibold text-white">{{ $reason }}</p>
                    <p class="mt-2 text-2xl font-semibold text-rose-200">{{ $count }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="text-xl font-semibold text-white">Captured Evidence</h2>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($violations as $violation)
                <article class="violation-evidence-card p-4">
                    @php($imageUrl = route('admin.violations.image', $violation))
                    <a href="{{ $imageUrl }}" target="_blank">
                        <img
                            src="{{ $imageUrl }}"
                            alt="Violation evidence"
                            class="h-56 w-full rounded-2xl object-cover border border-white/10"
                        >
                    </a>
                    <div class="mt-4 space-y-2">
                        <p class="text-sm font-semibold text-white">{{ $violation->reason }}</p>
                        <p class="text-xs text-slate-400">{{ $violation->created_at?->format('d M Y, h:i:s A') }}</p>
                        <p class="text-xs text-slate-500 break-all">Stored as: {{ $violation->image_path }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
