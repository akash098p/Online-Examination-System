@extends('layouts.admin')

@section('content')
@php
    $aiDrafts = $exam->aiGeneratedQuestions->map(function ($question) {
        return [
            'id' => $question->id,
            'subject' => $question->subject,
            'difficulty' => $question->difficulty,
            'topic' => $question->topic,
            'question' => $question->question,
            'options' => $question->options,
            'correct_answer' => $question->correct_answer,
            'explanation' => $question->explanation,
            'status' => $question->status,
            'question_id' => $question->question_id,
            'approved_url' => $question->question_id ? route('admin.questions.edit', $question->question_id) : null,
        ];
    })->values();
    $examDetailsRoute = route('admin.exams.show', $exam);
@endphp

<style>
    .exam-edit-shell {
        display: grid;
        gap: 1.5rem;
    }

    .exam-edit-card,
    .ai-workbench-card,
    .ai-draft-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 1.5rem;
        background:
            linear-gradient(180deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)),
            rgba(7, 12, 28, 0.48);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.12), 0 24px 50px rgba(2, 6, 23, 0.26);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .exam-edit-card::before,
    .ai-workbench-card::before,
    .ai-draft-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.12), transparent 35%);
        pointer-events: none;
    }

    .ai-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.18);
        padding: 0.4rem 0.8rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #e2e8f0;
        background: rgba(255,255,255,0.12);
    }

    .ai-draft-card[data-status="approved"] { border-color: rgba(34, 197, 94, 0.34); }
    .ai-draft-card[data-status="rejected"] { border-color: rgba(248, 113, 113, 0.34); opacity: 0.92; }
    .ai-status-approved { color: rgb(134 239 172); }
    .ai-status-pending { color: rgb(253 224 71); }
    .ai-status-rejected { color: rgb(252 165 165); }
    .ai-draft-grid { display: grid; gap: 1rem; }
    .queue-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; border: 1px solid rgba(255,255,255,0.14); border-radius: 1.25rem; background: rgba(255,255,255,0.12); padding: 0.85rem 1rem; }
    .queue-toolbar.hidden { display: none; }
    .queue-counter { display: inline-flex; align-items: center; gap: 0.65rem; color: rgb(226 232 240); font-size: 0.86rem; }
    .queue-counter strong { color: white; font-size: 1rem; }
    .queue-nav { display: inline-flex; align-items: center; gap: 0.75rem; }
    .queue-nav-btn { display: inline-flex; align-items: center; justify-content: center; width: 2.9rem; height: 2.9rem; border-radius: 999px; border: 1px solid rgba(255,255,255,0.14); background: rgba(8, 14, 30, 0.78); color: white; transition: transform 0.2s ease, background-color 0.2s ease, opacity 0.2s ease; }
    .queue-nav-btn:hover:not(:disabled) { transform: translateY(-1px); background: rgba(14, 165, 233, 0.28); }
    .queue-nav-btn:disabled { opacity: 0.38; cursor: not-allowed; }
    .queue-progress { display: inline-flex; align-items: center; gap: 0.45rem; flex-wrap: wrap; }
    .queue-progress-dot { width: 0.65rem; height: 0.65rem; border-radius: 999px; background: rgba(255,255,255,0.28); cursor: pointer; transition: transform 0.2s ease, background-color 0.2s ease; }
    .queue-progress-dot.is-active { background: rgb(34 211 238); transform: scale(1.15); }
    .queue-progress-dot.is-approved { background: rgb(34 197 94); }
    .queue-progress-dot.is-rejected { background: rgb(248 113 113); }
    .queue-card-header { display: flex; flex-wrap: wrap; align-items: start; justify-content: space-between; gap: 1rem; }
    .queue-card-meta { display: flex; flex-wrap: wrap; gap: 0.6rem; }
    .queue-option-list { display: grid; gap: 0.9rem; }
    .queue-option { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 0.9rem; border: 1px solid rgba(255,255,255,0.1); border-radius: 1.2rem; background: rgba(10, 16, 34, 0.56); padding: 0.8rem 0.95rem; }
    .queue-option input[type="text"] { min-width: 0; }
    .queue-field-grid { display: grid; gap: 1rem; }
    .queue-footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
    .queue-helper { color: rgb(148 163 184); font-size: 0.8rem; }
    .queue-review-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 3rem; height: 3rem; border-radius: 1rem; border: 1px solid rgba(34,211,238,0.28); background: rgba(14,165,233,0.22); color: rgb(207 250 254); font-size: 0.88rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; }
    .queue-card-top { display: flex; align-items: start; gap: 1rem; }
    .queue-card-top-content { flex: 1; min-width: 0; }
    .exam-input-shell {
        position: relative;
    }
    .exam-number-shell {
        position: relative;
    }
    .exam-select-shell {
        position: relative;
    }
    .exam-select-input {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: none !important;
        padding-right: 3.6rem;
    }
    .exam-select-input::-ms-expand {
        display: none;
    }
    .exam-select-icon {
        position: absolute;
        top: 50%;
        right: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        transform: translateY(-50%);
        border-radius: 0.75rem;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.06);
        color: white;
        pointer-events: none;
    }
    .exam-datetime-input {
        color-scheme: dark;
        padding-right: 3.6rem;
    }
    .exam-datetime-input::-webkit-calendar-picker-indicator {
        opacity: 0;
        position: absolute;
        right: 0.85rem;
        width: 2rem;
        height: 2rem;
        cursor: pointer;
    }
    .exam-datetime-input::-webkit-datetime-edit,
    .exam-datetime-input::-webkit-datetime-edit-text,
    .exam-datetime-input::-webkit-datetime-edit-month-field,
    .exam-datetime-input::-webkit-datetime-edit-day-field,
    .exam-datetime-input::-webkit-datetime-edit-year-field,
    .exam-datetime-input::-webkit-datetime-edit-hour-field,
    .exam-datetime-input::-webkit-datetime-edit-minute-field,
    .exam-datetime-input::-webkit-datetime-edit-ampm-field {
        color: rgb(241 245 249);
    }
    .exam-number-input {
        -moz-appearance: textfield;
        padding-right: 3rem;
    }
    .exam-number-input::-webkit-outer-spin-button,
    .exam-number-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .exam-input-icon-btn {
        position: absolute;
        top: 50%;
        right: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        transform: translateY(-50%);
        border-radius: 0.75rem;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.06);
        color: white;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    .exam-input-icon-btn:hover {
        background: rgba(255,255,255,0.12);
        border-color: rgba(255,255,255,0.2);
    }
    .exam-step-btn {
        position: absolute;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        right: 0.75rem;
        width: 1.5rem;
        height: 1.15rem;
        border: 0;
        background: transparent;
        color: rgb(226 232 240);
        line-height: 1;
        transition: color 0.2s ease, opacity 0.2s ease;
        opacity: 0.9;
    }
    .exam-step-btn:hover {
        color: white;
        opacity: 1;
    }
    .exam-step-btn-left {
        display: none;
    }
    .exam-step-btn-right {
        display: none;
    }
    .exam-step-btn-up {
        top: 0.58rem;
    }
    .exam-step-btn-down {
        bottom: 0.58rem;
    }
    .exam-step-divider {
        position: absolute;
        top: 50%;
        right: 0.75rem;
        width: 1.5rem;
        height: 1px;
        background: rgba(255,255,255,0.12);
        transform: translateY(-50%);
        pointer-events: none;
    }

    @media (min-width: 768px) {
        .queue-field-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .exam-edit-shell {
            grid-template-columns: minmax(0, 1fr);
            align-items: start;
        }
    }
</style>

<div class="space-y-6">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Manage Exam</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">Edit {{ $exam->title }}</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-300">
                Update exam settings, generate AI-assisted questions and review drafts before they go live in the question bank.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ $examDetailsRoute }}" class="admin-action-btn bg-red-600 text-white">Cancel</a>
            <a href="{{ route('admin.questions.index', $exam) }}" class="admin-action-btn bg-indigo-600 text-white">Manage Questions</a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="exam-edit-shell">
        <section class="exam-edit-card p-6">
            @php
                $selectedSemesters = is_array(old('semester', $exam->semester)) ? old('semester', $exam->semester) : (old('semester', $exam->semester) ? [old('semester', $exam->semester)] : []);
                $selectedDepartments = is_array(old('department', $exam->department)) ? old('department', $exam->department) : (old('department', $exam->department) ? [old('department', $exam->department)] : []);
            @endphp
            <form method="POST" action="{{ route('admin.exams.update', $exam) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Exam Title</label>
                        <input type="text" name="title" value="{{ old('title', $exam->title) }}" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Subject</label>
                        <input type="text" name="subject" value="{{ old('subject', $exam->subject) }}" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Select Semester</label>
                        <details class="custom-multi-select mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 text-white">
                            <summary class="flex items-center justify-between gap-3 p-4 cursor-pointer">
                                <span class="truncate">{{ count($selectedSemesters) ? implode(', ', $selectedSemesters) : 'All semesters' }}</span>
                                <span class="text-slate-300">▾</span>
                            </summary>
                            <div class="multi-select-panel rounded-b-2xl border-t border-white/10 bg-slate-950/90 p-3">
                                @foreach(config('academix.semesters', []) as $semester)
                                    <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                                        <input type="checkbox" name="semester[]" value="{{ $semester }}" {{ in_array($semester, $selectedSemesters, true) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-cyan-500">
                                        <span>{{ $semester }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </details>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Select Department</label>
                        <details class="custom-multi-select mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 text-white">
                            <summary class="flex items-center justify-between gap-3 p-4 cursor-pointer">
                                <span class="truncate">{{ count($selectedDepartments) ? implode(', ', $selectedDepartments) : 'All departments' }}</span>
                                <span class="text-slate-300">▾</span>
                            </summary>
                            <div class="multi-select-panel rounded-b-2xl border-t border-white/10 bg-slate-950/90 p-3">
                                @foreach(config('academix.departments', []) as $department)
                                    <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                                        <input type="checkbox" name="department[]" value="{{ $department }}" {{ in_array($department, $selectedDepartments, true) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-amber-400">
                                        <span>{{ $department }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </details>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-100">Description</label>
                        <textarea name="description" rows="4" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">{{ old('description', $exam->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Duration (minutes)</label>
                        <div class="exam-number-shell mt-2">
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" class="exam-number-input w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white" required>
                            <button type="button" class="exam-step-btn exam-step-btn-up" data-step-target="duration_minutes" data-step-direction="up" aria-label="Increase duration">
                                <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                    <path d="M2.25 7.5 6 4.25 9.75 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                </svg>
                            </button>
                            <span class="exam-step-divider" aria-hidden="true"></span>
                            <button type="button" class="exam-step-btn exam-step-btn-down" data-step-target="duration_minutes" data-step-direction="down" aria-label="Decrease duration">
                                <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                    <path d="M2.25 4.5 6 7.75 9.75 4.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Pass Percentage</label>
                        <div class="exam-number-shell mt-2">
                            <input type="number" name="pass_percentage" step="0.01" min="0" max="100" value="{{ old('pass_percentage', $exam->pass_percentage ?? 40) }}" class="exam-number-input w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white" required>
                            <button type="button" class="exam-step-btn exam-step-btn-up" data-step-target="pass_percentage" data-step-direction="up" aria-label="Increase pass percentage">
                                <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                    <path d="M2.25 7.5 6 4.25 9.75 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                </svg>
                            </button>
                            <span class="exam-step-divider" aria-hidden="true"></span>
                            <button type="button" class="exam-step-btn exam-step-btn-down" data-step-target="pass_percentage" data-step-direction="down" aria-label="Decrease pass percentage">
                                <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                    <path d="M2.25 4.5 6 7.75 9.75 4.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Start Time</label>
                        <div class="exam-input-shell mt-2">
                            <input type="datetime-local" name="start_time" value="{{ old('start_time', $exam->start_time?->format('Y-m-d\TH:i')) }}" class="exam-datetime-input w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">
                            <button type="button" class="exam-input-icon-btn" onclick="this.previousElementSibling.showPicker ? this.previousElementSibling.showPicker() : this.previousElementSibling.focus()" aria-label="Open start time picker">
                                <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                    <path d="M6.5 2.5V5" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
                                    <path d="M13.5 2.5V5" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
                                    <rect x="3" y="4.5" width="14" height="12.5" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M3 8.5H17" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">End Time</label>
                        <div class="exam-input-shell mt-2">
                            <input type="datetime-local" name="end_time" value="{{ old('end_time', $exam->end_time?->format('Y-m-d\TH:i')) }}" class="exam-datetime-input w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">
                            <button type="button" class="exam-input-icon-btn" onclick="this.previousElementSibling.showPicker ? this.previousElementSibling.showPicker() : this.previousElementSibling.focus()" aria-label="Open end time picker">
                                <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                    <path d="M6.5 2.5V5" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
                                    <path d="M13.5 2.5V5" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
                                    <rect x="3" y="4.5" width="14" height="12.5" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M3 8.5H17" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                    <label class="block text-sm font-semibold text-slate-100">Negative Marking</label>
                    <input type="hidden" name="negative_enabled" value="0">
                    <div class="mt-3 flex flex-col gap-4 md:flex-row md:items-center">
                        <label class="inline-flex items-center gap-3 text-sm text-slate-200">
                            <input type="checkbox" name="negative_enabled" value="1" class="control-check accent-green-500 dark:accent-green-400" {{ old('negative_enabled', $exam->negative_enabled) ? 'checked' : '' }}>
                            Enable negative marking
                        </label>
                        <div class="exam-number-shell w-full md:w-56">
                            <input type="number" step="0.01" name="negative_marking" value="{{ old('negative_marking', $exam->negative_marking) }}" class="exam-number-input w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white" placeholder="Penalty per wrong answer">
                            <button type="button" class="exam-step-btn exam-step-btn-up" data-step-target="negative_marking" data-step-direction="up" aria-label="Increase negative marking">
                                <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                    <path d="M2.25 7.5 6 4.25 9.75 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                </svg>
                            </button>
                            <span class="exam-step-divider" aria-hidden="true"></span>
                            <button type="button" class="exam-step-btn exam-step-btn-down" data-step-target="negative_marking" data-step-direction="down" aria-label="Decrease negative marking">
                                <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                    <path d="M2.25 4.5 6 7.75 9.75 4.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-cyan-400/20 bg-cyan-500/5 p-4">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>
                            <label class="block text-sm font-semibold text-slate-100">AI Proctoring</label>
                            <p class="mt-1 text-sm text-slate-300">Control whether this exam asks students for camera and microphone access before the timer begins.</p>
                        </div>
                        <label class="inline-flex items-center gap-3 text-sm text-slate-200">
                            <input type="hidden" name="proctoring_enabled" value="0">
                            <input type="checkbox" name="proctoring_enabled" value="1" class="control-check accent-cyan-500" {{ old('proctoring_enabled', $exam->proctoring_enabled) ? 'checked' : '' }}>
                            Enable proctoring for this exam
                        </label>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <p class="text-sm font-semibold text-slate-100">Device Access</p>
                            <div class="mt-3 grid gap-3 text-sm text-slate-200">
                                <label class="inline-flex items-center gap-3">
                                    <input type="hidden" name="require_camera" value="0">
                                    <input type="checkbox" name="require_camera" value="1" class="control-check accent-cyan-500" {{ old('require_camera', $exam->require_camera) ? 'checked' : '' }}>
                                    Require camera access
                                </label>
                                <label class="inline-flex items-center gap-3">
                                    <input type="hidden" name="require_microphone" value="0">
                                    <input type="checkbox" name="require_microphone" value="1" class="control-check accent-cyan-500" {{ old('require_microphone', $exam->require_microphone) ? 'checked' : '' }}>
                                    Require microphone access
                                </label>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <p class="text-sm font-semibold text-slate-100">Violation Rules</p>
                            <div class="mt-3 grid gap-3 text-sm text-slate-200">
                                <label class="inline-flex items-center gap-3">
                                    <input type="hidden" name="detect_no_face" value="0">
                                    <input type="checkbox" name="detect_no_face" value="1" class="control-check accent-cyan-500" {{ old('detect_no_face', $exam->detect_no_face) ? 'checked' : '' }}>
                                    Warn when no face is visible
                                </label>
                                <label class="inline-flex items-center gap-3">
                                    <input type="hidden" name="detect_multiple_faces" value="0">
                                    <input type="checkbox" name="detect_multiple_faces" value="1" class="control-check accent-cyan-500" {{ old('detect_multiple_faces', $exam->detect_multiple_faces) ? 'checked' : '' }}>
                                    Warn when multiple faces are detected
                                </label>
                                <label class="inline-flex items-center gap-3">
                                    <input type="hidden" name="detect_talking" value="0">
                                    <input type="checkbox" name="detect_talking" value="1" class="control-check accent-cyan-500" {{ old('detect_talking', $exam->detect_talking) ? 'checked' : '' }}>
                                    Warn when talking is detected
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-100">Maximum Warnings</label>
                            <div class="exam-number-shell mt-2">
                                <input type="number" name="max_warnings" min="1" max="10" value="{{ old('max_warnings', $exam->max_warnings ?? 5) }}" class="exam-number-input w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white" placeholder="5">
                                <button type="button" class="exam-step-btn exam-step-btn-up" data-step-target="max_warnings" data-step-direction="up" aria-label="Increase maximum warnings">
                                    <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                        <path d="M2.25 7.5 6 4.25 9.75 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                    </svg>
                                </button>
                                <span class="exam-step-divider" aria-hidden="true"></span>
                                <button type="button" class="exam-step-btn exam-step-btn-down" data-step-target="max_warnings" data-step-direction="down" aria-label="Decrease maximum warnings">
                                    <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                        <path d="M2.25 4.5 6 7.75 9.75 4.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-100">Pre-Exam Countdown (seconds)</label>
                            <div class="exam-number-shell mt-2">
                                <input type="number" name="pre_exam_countdown_seconds" min="0" max="60" value="{{ old('pre_exam_countdown_seconds', $exam->pre_exam_countdown_seconds ?? 10) }}" class="exam-number-input w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white" placeholder="10">
                                <button type="button" class="exam-step-btn exam-step-btn-up" data-step-target="pre_exam_countdown_seconds" data-step-direction="up" aria-label="Increase pre-exam countdown">
                                    <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                        <path d="M2.25 7.5 6 4.25 9.75 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                    </svg>
                                </button>
                                <span class="exam-step-divider" aria-hidden="true"></span>
                                <button type="button" class="exam-step-btn exam-step-btn-down" data-step-target="pre_exam_countdown_seconds" data-step-direction="down" aria-label="Decrease pre-exam countdown">
                                    <svg viewBox="0 0 12 12" fill="none" class="h-3 w-3" aria-hidden="true">
                                        <path d="M2.25 4.5 6 7.75 9.75 4.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button class="admin-action-btn bg-green-600 text-white">Update Exam</button>
                    <a href="{{ $examDetailsRoute }}" class="admin-action-btn bg-yellow-600 text-white">Back</a>
                </div>
            </form>
        </section>

        <section class="space-y-6">
            <div class="ai-workbench-card p-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">AI Question Generator</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">Build Draft Questions</h2>
                        <p class="mt-2 text-sm text-slate-300">
                            Generate multi-subject MCQs, review them, then approve only the questions you trust.
                        </p>
                    </div>
                    <span class="ai-pill">Rate limited to {{ config('services.gemini.rate_limit_per_hour', 10) }}/hour</span>
                </div>

                <form id="aiGeneratorForm" class="mt-6 grid gap-4 xl:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Subjects</label>
                        <textarea name="subjects" rows="3" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white" placeholder="Example: Algebra, Calculus, Probability">{{ $exam->subject }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Topics</label>
                        <textarea name="topics" rows="3" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white" placeholder="Example: Matrices, Determinants, Eigenvalues"></textarea>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 xl:col-span-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-100">Difficulty</label>
                            <div class="exam-select-shell mt-2">
                                <select name="difficulty" class="exam-select-input w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">
                                    <option value="mixed">Mixed</option>
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                                <span class="exam-select-icon" aria-hidden="true">
                                    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4">
                                        <path d="M6 8L10 12L14 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-100">Questions</label>
                            <input type="number" name="question_count" min="1" max="15" value="5" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 xl:col-span-2">
                        <button type="submit" id="generateAiBtn" class="admin-action-btn bg-blue-600 text-white">Generate with AI</button>
                        <span id="aiGeneratorState" class="text-sm text-slate-300">Ready to create reviewable drafts.</span>
                    </div>
                </form>
            </div>

            <div class="ai-workbench-card p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Quality Control</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">AI Draft Queue</h2>
                    </div>
                    <span class="ai-pill" id="aiDraftCount">{{ $aiDrafts->count() }} drafts</span>
                </div>

                <div id="aiDraftList" class="mt-6"></div>
                <div id="aiDraftNavigator" class="queue-toolbar mt-6 {{ $aiDrafts->count() ? '' : 'hidden' }}">
                    <div class="queue-counter">
                        <strong id="aiDraftPosition">Draft 1 of {{ max($aiDrafts->count(), 1) }}</strong>
                        <span id="aiDraftShortMeta">Focused review mode</span>
                    </div>
                    <div class="queue-nav">
                        <button type="button" id="aiDraftPrev" class="queue-nav-btn" aria-label="Previous draft">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                            </svg>
                        </button>
                        <div id="aiDraftProgress" class="queue-progress"></div>
                        <button type="button" id="aiDraftNext" class="queue-nav-btn" aria-label="Next draft">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="aiDraftEmpty" class="rounded-2xl border border-dashed border-white/12 bg-white/[0.03] px-5 py-6 text-sm text-slate-300 {{ $aiDrafts->count() ? 'hidden' : '' }}">
                    No AI drafts yet. Generate a batch to start review.
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-step-target]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.querySelector(`[name="${button.dataset.stepTarget}"]`);
                if (!input) return;

                const step = Number(input.step) || 1;
                const min = input.min === '' ? null : Number(input.min);
                const max = input.max === '' ? null : Number(input.max);
                const current = input.value === '' ? 0 : Number(input.value);
                const delta = button.dataset.stepDirection === 'down' ? -step : step;
                let next = current + delta;

                if (min !== null) {
                    next = Math.max(min, next);
                }

                if (max !== null) {
                    next = Math.min(max, next);
                }

                input.value = Number.isInteger(step) ? String(next) : next.toFixed(String(step).split('.')[1]?.length || 2);
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const examId = @json($exam->id);
        const draftList = document.getElementById('aiDraftList');
        const draftEmpty = document.getElementById('aiDraftEmpty');
        const draftCount = document.getElementById('aiDraftCount');
        const draftNavigator = document.getElementById('aiDraftNavigator');
        const draftPosition = document.getElementById('aiDraftPosition');
        const draftShortMeta = document.getElementById('aiDraftShortMeta');
        const draftPrev = document.getElementById('aiDraftPrev');
        const draftNext = document.getElementById('aiDraftNext');
        const draftProgress = document.getElementById('aiDraftProgress');
        const stateText = document.getElementById('aiGeneratorState');
        const generatorForm = document.getElementById('aiGeneratorForm');
        const generateButton = document.getElementById('generateAiBtn');
        let drafts = @json($aiDrafts);
        let currentDraftIndex = 0;

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function statusClass(status) {
            if (status === 'approved') return 'ai-status-approved';
            if (status === 'rejected') return 'ai-status-rejected';
            return 'ai-status-pending';
        }

        function updateDraftMeta() {
            draftCount.textContent = `${drafts.length} drafts`;
            draftEmpty.classList.toggle('hidden', drafts.length > 0);
            draftNavigator.classList.toggle('hidden', drafts.length === 0);

            if (!drafts.length) {
                draftPosition.textContent = 'Draft 0 of 0';
                draftShortMeta.textContent = 'Generate a fresh batch to begin review.';
                draftProgress.innerHTML = '';
                return;
            }

            currentDraftIndex = Math.min(currentDraftIndex, drafts.length - 1);
            const current = drafts[currentDraftIndex];
            draftPosition.textContent = `Draft ${currentDraftIndex + 1} of ${drafts.length}`;
            draftShortMeta.textContent = `${current.subject} • ${current.difficulty} • ${current.topic || 'General'}`;
            draftPrev.disabled = currentDraftIndex === 0;
            draftNext.disabled = currentDraftIndex === drafts.length - 1;
            draftProgress.innerHTML = drafts.map((item, index) => `
                <button
                    type="button"
                    class="queue-progress-dot ${index === currentDraftIndex ? 'is-active' : ''} ${item.status === 'approved' ? 'is-approved' : ''} ${item.status === 'rejected' ? 'is-rejected' : ''}"
                    data-progress-index="${index}"
                    aria-label="Go to draft ${index + 1}"
                ></button>
            `).join('');
        }

        function optionMarkup(question, option, index) {
            const checked = option === question.correct_answer ? 'checked' : '';

            return `
                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-black/20 px-3 py-3">
                    <input type="radio" name="correct-${question.id}" value="${index}" ${checked} class="control-check accent-cyan-400">
                    <input type="text" data-option-index="${index}" value="${escapeHtml(option)}" class="w-full rounded-xl border border-white/10 bg-slate-950/45 px-3 py-2 text-sm text-white">
                </label>
            `;
        }

        function renderDrafts() {
            if (!drafts.length) {
                draftList.innerHTML = '';
                updateDraftMeta();
                return;
            }

            const question = drafts[currentDraftIndex];
            draftList.innerHTML = `
                <article class="ai-draft-card p-5 sm:p-6" data-id="${question.id}" data-status="${escapeHtml(question.status)}">
                    <div class="queue-card-top">
                        <div class="queue-review-badge">Q${currentDraftIndex + 1}</div>
                        <div class="queue-card-top-content">
                            <div class="queue-card-header">
                                <div class="queue-card-meta">
                                    <span class="ai-pill">${escapeHtml(question.subject)}</span>
                                    <span class="ai-pill">${escapeHtml(question.difficulty)}</span>
                                    <span class="ai-pill">${escapeHtml(question.topic || 'General')}</span>
                                </div>
                                <span class="text-xs font-semibold uppercase tracking-[0.18em] ${statusClass(question.status)}">${escapeHtml(question.status)}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-5">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-100">Question</span>
                            <textarea data-field="question" rows="4" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-4 text-white">${escapeHtml(question.question)}</textarea>
                        </label>

                        <div class="queue-field-grid">
                            <label class="block">
                                <span class="text-sm font-semibold text-slate-100">Subject</span>
                                <input data-field="subject" type="text" value="${escapeHtml(question.subject)}" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">
                            </label>
                            <label class="block">
                                <span class="text-sm font-semibold text-slate-100">Difficulty</span>
                                <select data-field="difficulty" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">
                                    ${['easy', 'medium', 'hard', 'mixed'].map((item) => `<option value="${item}" ${item === question.difficulty ? 'selected' : ''}>${item.charAt(0).toUpperCase() + item.slice(1)}</option>`).join('')}
                                </select>
                            </label>
                            <label class="block">
                                <span class="text-sm font-semibold text-slate-100">Topic</span>
                                <input data-field="topic" type="text" value="${escapeHtml(question.topic || '')}" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">
                            </label>
                        </div>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-100">Explanation</span>
                            <textarea data-field="explanation" rows="4" class="mt-2 w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white">${escapeHtml(question.explanation || '')}</textarea>
                        </label>

                        <div>
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold text-slate-100">Options</span>
                                <span class="queue-helper">Select the correct answer before approving</span>
                            </div>
                            <div class="queue-option-list">
                                ${question.options.map((option, index) => `
                                    <label class="queue-option">
                                        <input type="text" data-option-index="${index}" value="${escapeHtml(option)}" class="w-full rounded-xl border border-white/10 bg-slate-950/45 px-4 py-3 text-sm text-white">
                                        <input type="radio" name="correct-${question.id}" value="${index}" ${option === question.correct_answer ? 'checked' : ''} class="control-check accent-cyan-400">
                                    </label>
                                `).join('')}
                            </div>
                        </div>
                    </div>

                    <div class="queue-footer mt-5">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" data-action="save" class="admin-action-btn bg-blue-600 text-white">Save Draft</button>
                            <button type="button" data-action="approve" class="admin-action-btn bg-green-600 text-white">Approve</button>
                            <button type="button" data-action="reject" class="admin-action-btn bg-red-600 text-white">Reject</button>
                            ${question.approved_url ? `<a href="${question.approved_url}" class="admin-action-btn bg-indigo-600 text-white">Open Question</a>` : ''}
                        </div>
                        <span class="queue-helper">Use the navigator below to move through the draft queue quickly.</span>
                    </div>
                </article>
            `;

            updateDraftMeta();
        }

        function collectDraftPayload(card, question) {
            const options = Array.from(card.querySelectorAll('[data-option-index]')).map((input) => input.value.trim());
            const checked = card.querySelector(`input[name="correct-${question.id}"]:checked`);
            const correctIndex = checked ? Number(checked.value) : -1;

            return {
                subject: card.querySelector('[data-field="subject"]').value.trim(),
                difficulty: card.querySelector('[data-field="difficulty"]').value,
                topic: card.querySelector('[data-field="topic"]').value.trim(),
                question: card.querySelector('[data-field="question"]').value.trim(),
                options,
                correct_answer: correctIndex >= 0 ? options[correctIndex] : '',
                explanation: card.querySelector('[data-field="explanation"]').value.trim(),
            };
        }

        async function request(url, method, body) {
            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(body),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.message || 'Request failed.');
            }

            return data;
        }

        generatorForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            generateButton.disabled = true;
            stateText.textContent = 'Generating questions... please wait.';

            const formData = new FormData(generatorForm);
            const payload = {
                subjects: formData.get('subjects'),
                topics: formData.get('topics'),
                difficulty: formData.get('difficulty'),
                question_count: Number(formData.get('question_count')),
            };

            try {
                const data = await request(@json(route('admin.exams.ai_questions.generate', $exam)), 'POST', payload);
                drafts = [...data.questions, ...drafts];
                currentDraftIndex = 0;
                renderDrafts();
                stateText.textContent = data.message;
            } catch (error) {
                stateText.textContent = error.message;
            } finally {
                generateButton.disabled = false;
            }
        });

        draftList.addEventListener('click', async (event) => {
            const button = event.target.closest('[data-action]');
            if (!button) return;

            const card = event.target.closest('[data-id]');
            const id = Number(card?.dataset.id);
            const draft = drafts.find((item) => item.id === id);
            if (!draft) return;

            const payload = collectDraftPayload(card, draft);

            try {
                if (button.dataset.action === 'save') {
                    const data = await request(`/admin/exams/${examId}/ai-questions/${id}`, 'PATCH', payload);
                    drafts = drafts.map((item) => item.id === id ? data.question : item);
                    stateText.textContent = data.message;
                }

                if (button.dataset.action === 'approve') {
                    await request(`/admin/exams/${examId}/ai-questions/${id}`, 'PATCH', payload);
                    const data = await request(`/admin/exams/${examId}/ai-questions/${id}/approve`, 'POST', {});
                    drafts = drafts.map((item) => item.id === id ? data.question : item);
                    stateText.textContent = data.message;
                }

                if (button.dataset.action === 'reject') {
                    const data = await request(`/admin/exams/${examId}/ai-questions/${id}/reject`, 'POST', {});
                    drafts = drafts.map((item) => item.id === id ? data.question : item);
                    stateText.textContent = data.message;
                }

                renderDrafts();
            } catch (error) {
                stateText.textContent = error.message;
            }
        });

        draftPrev.addEventListener('click', () => {
            if (currentDraftIndex === 0) return;
            currentDraftIndex -= 1;
            renderDrafts();
        });

        draftNext.addEventListener('click', () => {
            if (currentDraftIndex >= drafts.length - 1) return;
            currentDraftIndex += 1;
            renderDrafts();
        });

        draftProgress.addEventListener('click', (event) => {
            const target = event.target.closest('[data-progress-index]');
            if (!target) return;
            currentDraftIndex = Number(target.dataset.progressIndex);
            renderDrafts();
        });

        renderDrafts();
    });
</script>
@endsection
