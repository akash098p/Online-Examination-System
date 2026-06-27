@extends('layouts.admin')

@section('content')
<style>
    .exam-create-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 1.5rem;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02)),
            rgba(7, 12, 28, 0.48);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 24px 50px rgba(2, 6, 23, 0.26);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .exam-create-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), transparent 35%);
        pointer-events: none;
    }

    .exam-input-shell,
    .exam-number-shell,
    .exam-select-shell {
        position: relative;
    }

    .exam-field,
    .exam-select-input,
    .exam-datetime-input,
    .exam-number-input {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(2, 6, 23, 0.45);
        color: rgb(255, 255, 255);
        padding: 0.8rem 1rem;
        outline: none;
    }

    .exam-field::placeholder,
    .exam-number-input::placeholder {
        color: rgb(148 163 184);
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

    .exam-select-icon,
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
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.06);
        color: white;
    }

    .exam-select-icon {
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

    .exam-step-btn {
        position: absolute;
        right: 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.15rem;
        border: 0;
        background: transparent;
        color: rgb(226 232 240);
        opacity: 0.9;
        transition: color 0.2s ease, opacity 0.2s ease;
    }

    .exam-step-btn:hover {
        color: white;
        opacity: 1;
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
        background: rgba(255, 255, 255, 0.12);
        transform: translateY(-50%);
        pointer-events: none;
    }
</style>

<div class="space-y-6">
    @php
        $previousUrl = url()->previous();
        $previousPath = parse_url($previousUrl, PHP_URL_PATH) ?? '';
        $departmentSegment = null;

        if (str_contains($previousPath, '/admin/exams/department/')) {
            $departmentSegment = urldecode((string) last(array_values(array_filter(explode('/', $previousPath)))));
        }

        $departmentBackUrl = $departmentSegment
            ? route('admin.exams.department', $departmentSegment)
            : route('admin.exams.index');
    @endphp

    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Manage Exam</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">Create New Exam</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-300">
                Set up exam details, audience scope, schedule and marking rules before adding questions.
            </p>
        </div>

        <a href="{{ $departmentBackUrl }}" class="admin-action-btn bg-yellow-600 text-white">Back</a>
    </div>

    <section class="exam-create-card p-6">
        @php
            $selectedSemesters = is_array(old('semester')) ? old('semester') : (old('semester') ? [old('semester')] : []);
            $selectedDepartments = is_array(old('department')) ? old('department') : (old('department') ? [old('department')] : []);
        @endphp

        <form method="POST" action="{{ route('admin.exams.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-slate-100">Exam Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="exam-field mt-2" placeholder="Enter exam title" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-100">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="exam-field mt-2" placeholder="Subject name" required>
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
                    <textarea name="description" id="description" rows="4" class="exam-field mt-2" placeholder="Exam description (optional)">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-100">Duration (minutes)</label>
                    <div class="exam-number-shell mt-2">
                        <input type="number" name="duration_minutes" min="1" value="{{ old('duration_minutes') }}" class="exam-number-input" placeholder="e.g. 30" required>
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
                        <input type="number" name="pass_percentage" step="0.01" min="0" max="100" value="{{ old('pass_percentage', 40) }}" class="exam-number-input" placeholder="e.g. 40" required>
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
                        <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" class="exam-datetime-input" required>
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
                        <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" class="exam-datetime-input" required>
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
                        <input type="checkbox" name="negative_enabled" value="1" class="control-check accent-green-500 dark:accent-green-400" {{ old('negative_enabled') ? 'checked' : '' }}>
                        Enable negative marking
                    </label>

                    <div class="exam-number-shell w-full md:w-56">
                        <input type="number" step="0.01" min="0" name="negative_marking" value="{{ old('negative_marking') }}" class="exam-number-input" placeholder="Penalty e.g. 0.25">
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
                        <p class="mt-1 text-sm text-slate-300">Choose whether this exam should request camera and microphone access before students begin.</p>
                    </div>
                    <label class="inline-flex items-center gap-3 text-sm text-slate-200">
                        <input type="hidden" name="proctoring_enabled" value="0">
                        <input type="checkbox" name="proctoring_enabled" value="1" class="control-check accent-cyan-500" {{ old('proctoring_enabled') ? 'checked' : '' }}>
                        Enable proctoring for this exam
                    </label>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <p class="text-sm font-semibold text-slate-100">Device Access</p>
                        <div class="mt-3 grid gap-3 text-sm text-slate-200">
                            <label class="inline-flex items-center gap-3">
                                <input type="hidden" name="require_camera" value="0">
                                <input type="checkbox" name="require_camera" value="1" class="control-check accent-cyan-500" {{ old('require_camera') ? 'checked' : '' }}>
                                Require camera access
                            </label>
                            <label class="inline-flex items-center gap-3">
                                <input type="hidden" name="require_microphone" value="0">
                                <input type="checkbox" name="require_microphone" value="1" class="control-check accent-cyan-500" {{ old('require_microphone') ? 'checked' : '' }}>
                                Require microphone access
                            </label>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <p class="text-sm font-semibold text-slate-100">Violation Rules</p>
                        <div class="mt-3 grid gap-3 text-sm text-slate-200">
                            <label class="inline-flex items-center gap-3">
                                <input type="hidden" name="detect_no_face" value="0">
                                <input type="checkbox" name="detect_no_face" value="1" class="control-check accent-cyan-500" {{ old('detect_no_face', 1) ? 'checked' : '' }}>
                                Warn when no face is visible
                            </label>
                            <label class="inline-flex items-center gap-3">
                                <input type="hidden" name="detect_multiple_faces" value="0">
                                <input type="checkbox" name="detect_multiple_faces" value="1" class="control-check accent-cyan-500" {{ old('detect_multiple_faces', 1) ? 'checked' : '' }}>
                                Warn when multiple faces are detected
                            </label>
                            <label class="inline-flex items-center gap-3">
                                <input type="hidden" name="detect_talking" value="0">
                                <input type="checkbox" name="detect_talking" value="1" class="control-check accent-cyan-500" {{ old('detect_talking', 1) ? 'checked' : '' }}>
                                Warn when talking is detected
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Maximum Warnings</label>
                        <div class="exam-number-shell mt-2">
                            <input type="number" name="max_warnings" min="1" max="10" value="{{ old('max_warnings', 5) }}" class="exam-number-input" placeholder="5">
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
                            <input type="number" name="pre_exam_countdown_seconds" min="0" max="60" value="{{ old('pre_exam_countdown_seconds', 10) }}" class="exam-number-input" placeholder="10">
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
                <button type="submit" class="admin-action-btn bg-green-600 text-white">Save Exam</button>
                <a href="{{ $departmentBackUrl }}" class="admin-action-btn bg-red-600 text-white">Cancel</a>
            </div>
        </form>
    </section>
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
    });
</script>

@endsection
