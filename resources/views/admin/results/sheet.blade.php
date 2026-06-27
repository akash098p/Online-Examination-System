<link rel="icon" type="image/png" href="{{ asset('App-logo.png') }}">
@extends('layouts.admin')

@section('content')

<style>
.glass-card {
    background: rgba(8, 10, 34, 0.38);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(220, 220, 220, 0.22);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    border-radius: 0.9rem;
}
.question-card {
    background: rgba(8, 10, 34, 0.46);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(220, 220, 220, 0.18);
    border-radius: 0.9rem;
}

@media (max-width: 767px) {
    .admin-sheet-mobile-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.85rem;
    }

    .admin-sheet-mobile-card {
        min-height: 100%;
    }

    .admin-sheet-mobile-label {
        margin-bottom: 0.45rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgba(226, 232, 240, 0.78);
    }

    .admin-sheet-mobile-value {
        font-size: 1.03rem;
        font-weight: 600;
        line-height: 1.45;
        color: #fff;
        word-break: break-word;
    }

    .admin-sheet-mobile-span-3 {
        grid-column: span 3;
    }

    .admin-sheet-mobile-span-2 {
        grid-column: span 2;
    }

    .admin-sheet-mobile-status-pass {
        color: rgb(74 222 128);
    }

    .admin-sheet-mobile-status-fail {
        color: rgb(248 113 113);
    }
}
</style>

<div class="space-y-5">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <h1 class="text-3xl font-bold text-white">Student Answer Sheet</h1>

        <div class="flex flex-wrap gap-2">
            @if($result->user && $result->user->violations()->where('exam_id', $exam->id)->exists())
                <a href="{{ route('admin.violations.student', [$exam, $result->user->id]) }}" class="admin-action-btn bg-rose-600 text-white">
                    View Violations
                </a>
            @endif
            <a href="{{ route('admin.students.show', $result->user->id) }}" class="admin-action-btn bg-indigo-600 text-white">
                Student Profile
            </a>
        </div>
    </div>

    <div class="admin-sheet-mobile-grid md:hidden">
        <div class="glass-card admin-sheet-mobile-card admin-sheet-mobile-span-3 p-4">
            <p class="admin-sheet-mobile-label">Exam</p>
            <p class="admin-sheet-mobile-value">{{ $exam->title }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card admin-sheet-mobile-span-2 p-4">
            <p class="admin-sheet-mobile-label">Student</p>
            <p class="admin-sheet-mobile-value">{{ $result->user->name }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Semester</p>
            <p class="admin-sheet-mobile-value">{{ $result->user->semester ?? 'N/A' }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card admin-sheet-mobile-span-3 p-4">
            <p class="admin-sheet-mobile-label">Registration No</p>
            <p class="admin-sheet-mobile-value">{{ $result->user->registration_no ?? 'N/A' }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Total Questions</p>
            <p class="admin-sheet-mobile-value">{{ $totalQuestions }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Answered</p>
            <p class="admin-sheet-mobile-value">{{ $attempted }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Not Answered</p>
            <p class="admin-sheet-mobile-value">{{ $notAnswered }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Correct</p>
            <p class="admin-sheet-mobile-value">{{ $result->correct ?? 0 }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Wrong</p>
            <p class="admin-sheet-mobile-value">{{ $result->wrong ?? 0 }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Pass Mark</p>
            @php $passPercentage = (float) ($exam->pass_percentage ?? 40); @endphp
            <p class="admin-sheet-mobile-value">{{ number_format($passPercentage, 2) }}%</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Obtained</p>
            <p class="admin-sheet-mobile-value">{{ $result->obtained_marks }} / {{ $result->total_marks }}</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Percentage</p>
            <p class="admin-sheet-mobile-value">{{ number_format((float) $result->percentage, 2) }}%</p>
        </div>
        <div class="glass-card admin-sheet-mobile-card p-4">
            <p class="admin-sheet-mobile-label">Status</p>
            <p class="admin-sheet-mobile-value {{ ($result->status ?? '') === 'Pass' ? 'admin-sheet-mobile-status-pass' : 'admin-sheet-mobile-status-fail' }}">
                {{ $result->status }}
            </p>
        </div>
        <div class="glass-card admin-sheet-mobile-card admin-sheet-mobile-span-3 p-4">
            <p class="admin-sheet-mobile-label">Submitted</p>
            <p class="admin-sheet-mobile-value">{{ optional($result->submitted_at ?? $result->created_at)->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <div class="hidden gap-4 md:grid md:grid-cols-4">
        <div class="glass-card p-4">Student: {{ $result->user->name }}</div>
        <div class="glass-card p-4">Reg No: {{ $result->user->registration_no ?? 'N/A' }}</div>
        <div class="glass-card p-4">Exam: {{ $exam->title }}</div>
        <div class="glass-card p-4">
            Submitted: {{ optional($result->submitted_at ?? $result->created_at)->format('d M Y, h:i A') }}
        </div>
    </div>

    <div class="hidden gap-4 md:grid md:grid-cols-4">
        <div class="glass-card p-4">Total Questions: {{ $totalQuestions }}</div>
        <div class="glass-card p-4">Answered: {{ $attempted }}</div>
        <div class="glass-card p-4">Not Answered: {{ $notAnswered }}</div>
        <div class="glass-card p-4">
            Correct: {{ $result->correct ?? 0 }} | Wrong: {{ $result->wrong ?? 0 }}
        </div>
    </div>

    <div class="hidden gap-4 md:grid md:grid-cols-4">
        <div class="glass-card p-4">Obtained: {{ $result->obtained_marks }} / {{ $result->total_marks }}</div>
        <div class="glass-card p-4">Percentage: {{ number_format((float) $result->percentage, 2) }}%</div>
        <div class="glass-card p-4">
            Status:
            <span class="{{ ($result->status ?? '') === 'Pass' ? 'text-green-400' : 'text-red-400' }}">
                {{ $result->status }}
            </span>
        </div>
        <div class="glass-card p-4">
            Pass Mark:
            @php $passPercentage = (float) ($exam->pass_percentage ?? 40); @endphp
            {{ number_format($passPercentage, 2) }}%
        </div>
    </div>

    @foreach($questions as $index => $q)
        @php
            $response = $responses->get($q->id);
            $selectedOption = $response?->option;
            $correctOption = $q->options->firstWhere('is_correct', 1);
            $isAnswered = !is_null($response?->option_id);
            $isCorrect = (bool) ($response?->is_correct);
            $status = !$isAnswered ? 'Not Answered' : ($isCorrect ? 'Correct' : 'Wrong');
        @endphp

        <div class="question-card p-4 mb-4">
            <h3 class="font-semibold mb-2 text-white">Q{{ $index + 1 }}. {{ $q->question_text }}</h3>

            <div class="grid md:grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-gray-300">Selected Option:</p>
                    <p class="{{ $isAnswered ? ($isCorrect ? 'text-green-400' : 'text-red-400') : 'text-yellow-400' }}">
                        {{ $selectedOption?->option_text ?? 'Not Answered' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-300">Correct Option:</p>
                    <p class="text-green-400">{{ $correctOption?->option_text ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-300">Status:</p>
                    <p class="{{ $status === 'Correct' ? 'text-green-400' : ($status === 'Wrong' ? 'text-red-400' : 'text-yellow-400') }}">
                        {{ $status }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-300">Marks Obtained:</p>
                    <p class="text-white">{{ (int) ($response->marks_obtained ?? 0) }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

@endsection
