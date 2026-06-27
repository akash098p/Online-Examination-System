@extends('layouts.admin')

@section('content')

<h1 class="text-2xl font-bold mb-4">Exam Details</h1>

@php
    use Carbon\Carbon;

    $examDepartments = empty($exam->department) ? 'All departments' : implode(', ', (array) $exam->department);
    $examSemesters = empty($exam->semester) ? 'All semesters' : implode(', ', (array) $exam->semester);
    $backDepartment = collect((array) $exam->department)->filter()->first();
    $backRoute = $backDepartment
        ? route('admin.exams.department', $backDepartment)
        : route('admin.exams.index');
@endphp

<style>
.label {
    color: #93c5fd;
    font-weight: 600;
    min-width: 170px;
    display: inline-block;
}

.value {
    color: #f9fafb;
    margin-left: 6px;
}

/* 🎯 Priority colors */
.value-important {
    color: #facc15;
    font-weight: 600;
}

.value-time {
    color: #f87171; /* 🔴 RED */
    font-weight: 700;
}

.value-success {
    color: #4ade80;
    font-weight: 600;
}

.value-danger {
    color: #f87171;
    font-weight: 600;
}
</style>

<div class="bg-white dark:bg-gray-800 dark:text-gray-100 p-6 rounded shadow">

    {{-- HEADER --}}
    <div class="flex justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold">{{ $exam->title }}</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ $exam->subject }}</p>
        </div>

        <span class="px-3 py-1 text-white rounded
            {{ $exam->status == 'published' ? 'bg-green-600' : 'bg-yellow-600' }}">
            {{ ucfirst($exam->status) }}
        </span>
    </div>

    {{-- DETAILS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <p>
                <span class="label">Duration:</span>
                <span class="value value-important">{{ $exam->duration_minutes }} minutes</span>
            </p>

            <p>
                <span class="label">Total Questions:</span>
                <span class="value value-important">{{ $exam->questions->count() }}</span>
            </p>

            <p>
                <span class="label">Departments:</span>
                <span class="value">{{ $examDepartments }}</span>
            </p>

            <p>
                <span class="label">Semesters:</span>
                <span class="value">{{ $examSemesters }}</span>
            </p>

            <p class="mt-2">
                <span class="label">Negative Marking:</span>
                <span class="value {{ $exam->negative_enabled ? 'value-danger' : '' }}">
                    @if($exam->negative_enabled)
                        -{{ $exam->negative_marking }} per wrong answer
                    @else
                        No
                    @endif
                </span>
            </p>

            <p class="mt-2">
                <span class="label">AI Proctoring:</span>
                <span class="value {{ $exam->proctoring_enabled ? 'value-success' : '' }}">
                    {{ $exam->proctoring_enabled ? 'Enabled' : 'Disabled' }}
                </span>
            </p>

            @if($exam->proctoring_enabled)
                <p>
                    <span class="label">Camera:</span>
                    <span class="value {{ $exam->require_camera ? 'value-success' : '' }}">
                        {{ $exam->require_camera ? 'Required' : 'Not required' }}
                    </span>
                </p>

                <p>
                    <span class="label">Microphone:</span>
                    <span class="value {{ $exam->require_microphone ? 'value-success' : '' }}">
                        {{ $exam->require_microphone ? 'Required' : 'Not required' }}
                    </span>
                </p>

                <p>
                    <span class="label">Max Warnings:</span>
                    <span class="value value-important">{{ $exam->max_warnings }}</span>
                </p>

                <p>
                    <span class="label">Pre-Exam Countdown:</span>
                    <span class="value value-important">{{ $exam->pre_exam_countdown_seconds }} seconds</span>
                </p>
            @endif
        </div>

        <div>
            <p>
                <span class="label">Start Time:</span>
                <span class="value value-time">
                    {{ Carbon::parse($exam->start_time)->format('d M Y • h:i A') }}
                </span>
            </p>

            <p>
                <span class="label">End Time:</span>
                <span class="value value-time">
                    {{ Carbon::parse($exam->end_time)->format('d M Y • h:i A') }}
                </span>
            </p>

            @if($exam->proctoring_enabled)
                <div class="mt-2">
                    <span class="label">Violation Checks:</span>
                    <ul class="ml-5 mt-1 list-disc text-gray-200">
                        @if($exam->detect_no_face)
                            <li>No face detected</li>
                        @endif
                        @if($exam->detect_multiple_faces)
                            <li>Multiple faces detected</li>
                        @endif
                        @if($exam->detect_talking)
                            <li>Talking detected</li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>

        <div class="md:col-span-2 mt-3">
            <span class="label">Description:</span>
            <div class="prose dark:prose-invert mt-2">
                {!! $exam->description !!}
            </div>
        </div>

    </div>

    {{-- ACTION BUTTONS --}}
    <div class="mt-6 flex gap-3 flex-wrap">

        <a href="{{ route('admin.exams.edit', $exam) }}"
           class="admin-action-btn bg-blue-600 text-white">
            Edit Exam
        </a>

        <a href="{{ route('admin.exams.edit', $exam) }}#aiGeneratorForm"
           class="admin-action-btn bg-cyan-600 text-white">
            AI Question Generator
        </a>

        <a href="{{ route('admin.questions.index', $exam) }}"
           class="admin-action-btn bg-indigo-600 text-white">
            Manage Questions
        </a>

        @if($exam->violations()->exists())
        <a href="{{ route('admin.violations.exam', $exam) }}"
           class="admin-action-btn bg-rose-600 text-white">
            Review Violations
        </a>
        @endif

        <form method="POST" action="{{ route('admin.exams.toggle_publish', $exam) }}">
            @csrf
            <button class="admin-action-btn text-white rounded
                {{ $exam->status == 'published' ? 'bg-yellow-600' : 'bg-green-600' }}">
                {{ $exam->status == 'published' ? 'Unpublish' : 'Publish' }}
            </button>
        </form>

        <a href="{{ $backRoute }}"
           class="admin-action-btn bg-yellow-600 text-white">
            Back
        </a>
    </div>

</div>

@endsection