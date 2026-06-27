@extends('layouts.admin')

@section('content')
<style>
.analytics-reveal {
    opacity: 0;
    transform: translateY(18px);
    animation: analyticsFadeUp .55s ease forwards;
}

.analytics-delay-1 { animation-delay: .06s; }
.analytics-delay-2 { animation-delay: .14s; }
.analytics-delay-3 { animation-delay: .22s; }

.analytics-row {
    opacity: 0;
    transform: translateY(10px);
    animation: analyticsFadeUp .45s ease forwards;
}

@keyframes analyticsFadeUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.analytics-insight-card,
.analytics-side-card {
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 1.5rem;
    background:
        linear-gradient(180deg, rgba(255,255,255,0.15), rgba(255,255,255,0.04)),
        rgba(8, 10, 34, 0.58);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.analytics-insight-card::before,
.analytics-side-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.18), transparent 36%);
    pointer-events: none;
}

.analytics-summary-layout {
    display: grid;
    gap: 1rem;
}

.analytics-summary-block {
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 1.35rem;
    background: rgba(255,255,255,0.08);
}

.analytics-summary-grid {
    display: grid;
    gap: 0.85rem;
}

.analytics-top-hidden {
    display: none;
}

.analytics-leaderboard-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    border: 1px solid rgba(255,255,255,0.16);
    border-radius: 1.25rem;
    background:
        linear-gradient(180deg, rgba(255,255,255,0.18), rgba(255,255,255,0.06)),
        rgba(15, 23, 42, 0.42);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.18);
}

.analytics-leaderboard-rank {
    width: 2rem;
    font-size: 1.05rem;
    font-weight: 700;
}

.analytics-leaderboard-rank.is-gold { color: rgb(253 224 71); }
.analytics-leaderboard-rank.is-silver { color: rgb(209 213 219); }
.analytics-leaderboard-rank.is-bronze { color: rgb(253 186 116); }

.analytics-leaderboard-score {
    text-align: right;
}

.analytics-toggle-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.8rem;
    padding-inline: 0.85rem;
}

.analytics-toggle-btn svg {
    transition: transform 0.2s ease;
}

.analytics-toggle-btn.is-collapsed svg {
    transform: rotate(180deg);
}

.analytics-summary-hidden {
    display: none;
}

.analytics-kpi-strip {
    display: grid;
    gap: 0.85rem;
}

.analytics-kpi-card {
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 1.1rem;
    background: rgba(255,255,255,0.08);
}

@media (min-width: 1280px) {
    .analytics-summary-layout {
        grid-template-columns: minmax(0, 1fr);
        align-items: start;
    }

    .analytics-kpi-strip {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 1024px) {
    .analytics-insight-card,
    .analytics-side-card,
    .analytics-summary-block,
    .analytics-kpi-card {
        min-width: 0;
    }

    .analytics-insight-card {
        padding: 1.25rem;
    }

    .analytics-insight-card > .flex,
    .analytics-insight-card > .flex > .flex {
        flex-direction: column !important;
        align-items: stretch !important;
    }

    .analytics-summary-layout {
        gap: 1rem;
    }

    .analytics-kpi-strip {
        grid-template-columns: 1fr;
    }

    .analytics-summary-grid {
        gap: 0.85rem;
    }

    .analytics-summary-block {
        padding: 1rem;
        word-break: break-word;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .analytics-insight-card h2,
    .analytics-insight-card p {
        max-width: 100%;
        word-break: break-word;
        white-space: normal;
    }
}

@media (max-width: 640px) {
    .analytics-insight-card {
        padding: 1rem;
    }

    .analytics-side-card {
        padding: 1rem;
    }

    .analytics-leaderboard-card {
        flex-direction: row;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 0.9rem !important;
        border-radius: 1rem;
    }

    .analytics-leaderboard-card .flex {
        flex-direction: row;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .analytics-leaderboard-rank {
        width: 1.6rem;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .analytics-leaderboard-card img {
        width: 2.25rem;
        height: 2.25rem;
        flex-shrink: 0;
    }

    .analytics-leaderboard-name {
        font-size: 0.95rem;
        line-height: 1.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .analytics-leaderboard-subtext,
    .analytics-leaderboard-view {
        display: none !important;
    }

    .analytics-leaderboard-score {
        font-size: 0.98rem;
        text-align: right;
        flex-shrink: 0;
    }
}
</style>

<h1 class="text-2xl font-bold mb-6 text-white">Analytics Dashboard</h1>

@php
    $selectedDepartments = $selectedDepartments ?? array_values(array_filter((array) request('department', []), fn ($value) => $value !== ''));
    $selectedSemesters = $selectedSemesters ?? array_values(array_filter((array) request('semester', []), fn ($value) => $value !== ''));
    $selectedDepartmentLabel = count($selectedDepartments) ? implode(', ', $selectedDepartments) : 'All';
    $selectedSemesterLabel = count($selectedSemesters) ? implode(', ', $selectedSemesters) : 'All';
@endphp

<form method="GET" action="{{ route('admin.analytics') }}" class="mb-6 flex flex-wrap items-center gap-3 custom-dropdown-group" style="overflow: visible; position: relative; z-index: 1;">
    <div class="w-full sm:w-auto">
        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Department</label>
        <details class="custom-multi-select compact" style="overflow: visible; z-index: 999;">
            <summary>
                <span class="truncate">{{ $selectedDepartmentLabel }}</span>
                <span class="dropdown-toggle">▾</span>
            </summary>
            <div class="multi-select-panel">
                <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                    <input type="checkbox" name="department[]" value="" {{ count($selectedDepartments) === 0 ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                    <span>All Departments</span>
                </label>
                @foreach(config('academix.departments', []) as $department)
                    <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                        <input type="checkbox" name="department[]" value="{{ $department }}" {{ in_array($department, $selectedDepartments, true) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                        <span>{{ $department }}</span>
                    </label>
                @endforeach
            </div>
        </details>
    </div>

    <div class="w-full sm:w-auto">
        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Semester for Exam Chart & Leaderboard</label>
        <details class="custom-multi-select compact" style="overflow: visible; z-index: 999;">
            <summary>
                <span class="truncate">{{ $selectedSemesterLabel }}</span>
                <span class="dropdown-toggle">▾</span>
            </summary>
            <div class="multi-select-panel">
                <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                    <input type="checkbox" name="semester[]" value="" {{ count($selectedSemesters) === 0 ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                    <span>All Semesters</span>
                </label>
                @foreach(config('academix.semesters', []) as $semester)
                    <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                        <input type="checkbox" name="semester[]" value="{{ $semester }}" {{ in_array($semester, $selectedSemesters, true) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                        <span>{{ $semester }}</span>
                    </label>
                @endforeach
            </div>
        </details>
    </div>

    <div class="flex flex-wrap items-center gap-3 justify-center sm:justify-start w-full sm:w-auto">
        <button type="submit" class="admin-action-btn bg-blue-600 text-white">Apply</button>
        <a href="{{ route('admin.analytics') }}" class="admin-action-btn bg-slate-700 text-white">Reset</a>
    </div>
</form>

<div class="grid grid-cols-1 gap-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="bg-gray-800 p-5 rounded shadow analytics-reveal analytics-delay-1">
        <h2 class="text-xl font-semibold mb-3 text-white">Exam Summary</h2>
        <p class="mb-3 text-xs uppercase tracking-[0.18em] text-slate-400">
            {{ $selectedDepartmentLabel }} | {{ $selectedSemesterLabel }}
        </p>
        <canvas id="examPerformanceChart" height="150"></canvas>
    </div>

    <div class="bg-gray-800 p-5 rounded shadow analytics-reveal analytics-delay-2">
        <h2 class="text-xl font-semibold mb-3 text-white">Daily Attempts</h2>
        <p class="mb-3 text-xs uppercase tracking-[0.18em] text-slate-400">
            {{ $selectedDepartmentLabel }} | {{ $selectedSemesterLabel }}
        </p>
        <canvas id="dailyAttemptsChart" height="150"></canvas>
    </div>

    <section class="analytics-insight-card p-5 lg:col-span-2 analytics-reveal analytics-delay-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Leaderboard</p>
                <h2 class="mt-2 text-2xl font-semibold text-white">Top Performing Students</h2>
                <p class="mt-2 text-sm text-slate-400">
                    {{ $selectedDepartmentLabel }} | {{ $selectedSemesterLabel }}
                </p>
            </div>
            <button
                type="button"
                id="toggleTopStudentsBtn"
                class="admin-action-btn bg-slate-700/80 text-white analytics-toggle-btn"
                aria-expanded="true"
                aria-label="Hide top performing students"
                title="Hide top performing students"
            >
                <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path d="M5 12.5L10 7.5L15 12.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
            </button>
        </div>

        <div id="topStudentsBody" class="mt-6 space-y-4">
            @foreach ($topStudents as $index => $row)
                <div class="analytics-leaderboard-card px-4 py-4 analytics-row" style="animation-delay: {{ 0.06 + ($loop->index * 0.04) }}s;">
                    <div class="flex items-center gap-3">
                        <div class="analytics-leaderboard-rank {{ $loop->first ? 'is-gold' : ($loop->iteration === 2 ? 'is-silver' : ($loop->iteration === 3 ? 'is-bronze' : '')) }}">
                            @if($loop->first)
                                #1
                            @elseif($loop->iteration === 2)
                                #2
                            @elseif($loop->iteration === 3)
                                #3
                            @else
                                #{{ $index + 1 }}
                            @endif
                        </div>
                        <img src="{{ $row->user->profilePhotoUrl() }}" alt="{{ $row->user->name }}" class="h-10 w-10 rounded-full object-cover border border-white/20">
                        <div>
                            <p class="analytics-leaderboard-name font-semibold text-white">{{ $row->user->name }}</p>
                            <p class="analytics-leaderboard-subtext text-sm text-slate-400">Average score across results</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="analytics-leaderboard-score">
                            <p class="text-lg font-bold text-emerald-300">{{ number_format($row->avg_percentage, 2) }}%</p>
                        </div>
                        <a href="{{ route('admin.students.show', $row->user->id) }}" class="analytics-leaderboard-view admin-action-btn bg-indigo-600 text-white hover:bg-indigo-700 transition">
                            View
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    </div>

    <section class="analytics-insight-card p-6 analytics-reveal analytics-delay-1">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">AI Summary Block</p>
                <h2 class="mt-2 max-w-4xl text-2xl font-semibold leading-tight text-white">{{ $aiInsights['headline'] ?? 'AI Insights' }}</h2>
                <p class="mt-3 max-w-5xl text-sm leading-6 text-slate-300">
                    {{ $aiInsights['summary'] ?? 'Analytics insights will appear here once enough exam data is available.' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <div class="rounded-2xl border border-cyan-300/20 bg-cyan-400/10 px-4 py-3 text-sm text-cyan-100 lg:max-w-xs">
                    Data-backed guidance for admin decisions
                </div>
                <button
                    type="button"
                    id="toggleAnalyticsSummaryBtn"
                    class="admin-action-btn bg-slate-700/80 text-white"
                    aria-expanded="true"
                    aria-label="Hide AI summary"
                    title="Hide AI summary"
                >
                    <svg id="toggleAnalyticsSummaryIcon" viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                        <path d="M5 12.5L10 7.5L15 12.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="analyticsSummaryBody" class="mt-6 analytics-summary-layout">
            <div class="analytics-kpi-strip">
                <div class="analytics-kpi-card px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Key Focus</p>
                    <p class="mt-2 text-sm font-semibold leading-6 text-white">{{ collect($aiInsights['insights'] ?? [])->first() ?? 'Awaiting stronger patterns from exam activity.' }}</p>
                </div>
                <div class="analytics-kpi-card px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Priority Action</p>
                    <p class="mt-2 text-sm font-semibold leading-6 text-emerald-100">{{ collect($aiInsights['actions'] ?? [])->first() ?? 'No urgent intervention suggested yet.' }}</p>
                </div>
                <div class="analytics-kpi-card px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Signal Strength</p>
                    <p class="mt-2 text-sm font-semibold leading-6 text-cyan-100">{{ count($aiInsights['insights'] ?? []) ? 'Focused summary generated from current trends.' : 'More exam data will sharpen future recommendations.' }}</p>
                </div>
            </div>

            <div class="analytics-side-card p-5">
                <h3 class="text-lg font-semibold text-white">Top findings</h3>
                <div class="mt-4 analytics-summary-grid">
                    @foreach(collect($aiInsights['insights'] ?? [])->take(3) as $insight)
                        <div class="analytics-summary-block px-4 py-3 text-sm leading-6 text-slate-200">
                            {{ $insight }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="analytics-side-card p-5">
                <h3 class="text-lg font-semibold text-white">Priority actions</h3>
                <div class="mt-4 space-y-3">
                    @foreach(collect($aiInsights['actions'] ?? [])->take(3) as $action)
                        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm leading-6 text-emerald-100">
                            {{ $action }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="analytics-side-card p-5 analytics-reveal analytics-delay-2">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Weak Topic Detection</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white">Lowest Accuracy Topics</h2>
                </div>
            </div>
            <div class="mt-4">
                <canvas id="weakTopicsChart" height="200"></canvas>
            </div>
        </section>

        <section class="analytics-side-card p-5 analytics-reveal analytics-delay-3">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Hardest Questions</p>
            <h2 class="mt-2 text-2xl font-semibold text-white">Most Missed Questions</h2>

            <div class="mt-5 space-y-3">
                @forelse($hardestQuestions as $item)
                    <article class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $item->question_text }}</p>
                                <p class="mt-2 text-xs uppercase tracking-[0.18em] text-slate-400">{{ $item->exam_title }} • {{ $item->topic }}</p>
                            </div>
                            <div class="rounded-full border border-rose-300/25 bg-rose-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-rose-100">
                                {{ number_format($item->wrong_rate, 2) }}% wrong
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-slate-300">{{ $item->attempts }} student attempts recorded</p>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/12 bg-white/[0.03] px-5 py-6 text-sm text-slate-300">
                        Hardest-question analytics will appear once students complete more exams.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

<div id="dailyAttemptsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-2xl bg-slate-900/90 border border-white/20 rounded-xl shadow-2xl">
        <div class="flex items-center justify-between p-4 border-b border-white/15">
            <h3 id="dailyAttemptsModalTitle" class="text-lg font-semibold text-white">Daily Attempts</h3>
            <button id="dailyAttemptsModalClose" type="button" class="text-gray-300 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <div id="dailyAttemptsModalBody" class="max-h-[65vh] overflow-y-auto p-4 space-y-3"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const examLabels = {!! json_encode($examLabels) !!};
    const examScores = {!! json_encode($examScores) !!};
    const examBaseColor = 'rgba(230, 226, 2, 0.92)';
    const examDimColor = 'rgba(230, 226, 2, 0.20)';
    const examFocusBorder = 'rgba(255, 255, 255, 0.9)';

    function applyExamBarFocus(chart, focusedIndex) {
        const total = chart.data.labels.length;
        const bg = [];
        const border = [];
        const borderWidth = [];

        for (let i = 0; i < total; i++) {
            const isFocused = focusedIndex === null || i === focusedIndex;
            bg.push(isFocused ? examBaseColor : examDimColor);
            border.push(isFocused ? examFocusBorder : 'rgba(255, 255, 255, 0.12)');
            borderWidth.push(isFocused ? 1.4 : 0.6);
        }

        chart.data.datasets[0].backgroundColor = bg;
        chart.data.datasets[0].borderColor = border;
        chart.data.datasets[0].borderWidth = borderWidth;
        chart.update('none');
    }

    let examChart;
    let examChartRevealTimer;

    const dailyLabels = {!! json_encode($dailyLabels) !!};
    const dailyCounts = {!! json_encode($dailyCounts) !!};
    const dailyAttemptDetails = @json($dailyAttemptDetails->toArray());
    const weakTopicLabels = @json(collect($weakTopicsData)->pluck('topic'));
    const weakTopicAccuracy = @json(collect($weakTopicsData)->pluck('accuracy'));

    const modal = document.getElementById('dailyAttemptsModal');
    const modalBody = document.getElementById('dailyAttemptsModalBody');
    const modalTitle = document.getElementById('dailyAttemptsModalTitle');
    const closeModalBtn = document.getElementById('dailyAttemptsModalClose');
    const defaultStudentPhoto = @json(asset('images/default-male.png'));

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function openDailyAttemptsModal(day) {
        const attempts = Array.isArray(dailyAttemptDetails?.[day])
            ? dailyAttemptDetails[day]
            : Object.values(dailyAttemptDetails?.[day] || {});
        modalTitle.textContent = `Attempts on ${day}`;

        if (!attempts.length) {
            modalBody.innerHTML = '<p class="text-gray-300">No attempts found for this day.</p>';
        } else {
            modalBody.innerHTML = attempts.map((item) => {
                const studentName = escapeHtml(item?.student_name || 'Unknown');
                const studentReg = escapeHtml(item?.student_reg || 'N/A');
                const studentDepartment = escapeHtml(item?.student_department || 'N/A');
                const studentSemester = escapeHtml(item?.student_semester || 'N/A');
                const examTitle = escapeHtml(item?.exam_title || 'Exam');
                const status = escapeHtml(item?.status || 'Unknown');
                const time = escapeHtml(item?.time || 'Time unavailable');
                const percentage = Number(item?.percentage ?? 0).toFixed(2);
                const studentPhoto = escapeHtml(item?.student_photo || defaultStudentPhoto);

                return `
                <div class="rounded-lg border border-white/15 bg-white/5 p-3">
                    <div class="flex items-start gap-3">
                        <img src="${studentPhoto}" alt="${studentName}" class="w-10 h-10 rounded-full object-cover border border-white/20" />
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-white font-semibold">${studentName}</p>
                                <span class="text-xs px-2 py-1 rounded ${status === 'Pass' ? 'bg-green-600/80' : 'bg-red-600/80'} text-white">${status}</span>
                            </div>
                            <p class="text-xs text-gray-300">Reg No: ${studentReg}</p>
                            <p class="text-xs text-gray-300">${studentDepartment} | ${studentSemester}</p>
                            <p class="text-sm text-cyan-300 mt-1">${examTitle}</p>
                            <div class="flex items-center justify-between mt-1 text-xs text-gray-300">
                                <span>${time}</span>
                                <span>${percentage}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDailyAttemptsModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    closeModalBtn.addEventListener('click', closeDailyAttemptsModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeDailyAttemptsModal();
    });

    let dailyAttemptsChart;
    let weakTopicsChart;
    const analyticsSummaryBody = document.getElementById('analyticsSummaryBody');
    const toggleAnalyticsSummaryBtn = document.getElementById('toggleAnalyticsSummaryBtn');
    const topStudentsBody = document.getElementById('topStudentsBody');
    const toggleTopStudentsBtn = document.getElementById('toggleTopStudentsBtn');
    let isAnalyticsSummaryCollapsed = false;
    let isTopStudentsCollapsed = false;

    function syncAnalyticsSummaryVisibility() {
        analyticsSummaryBody?.classList.toggle('analytics-summary-hidden', isAnalyticsSummaryCollapsed);

        if (toggleAnalyticsSummaryBtn) {
            toggleAnalyticsSummaryBtn.setAttribute('aria-expanded', String(!isAnalyticsSummaryCollapsed));
            toggleAnalyticsSummaryBtn.setAttribute('aria-label', isAnalyticsSummaryCollapsed ? 'Show AI summary' : 'Hide AI summary');
            toggleAnalyticsSummaryBtn.title = isAnalyticsSummaryCollapsed ? 'Show AI summary' : 'Hide AI summary';
            toggleAnalyticsSummaryBtn.querySelector('svg')?.classList.toggle('rotate-180', isAnalyticsSummaryCollapsed);
        }
    }

    function syncTopStudentsVisibility() {
        topStudentsBody?.classList.toggle('analytics-top-hidden', isTopStudentsCollapsed);

        if (toggleTopStudentsBtn) {
            toggleTopStudentsBtn.classList.toggle('is-collapsed', isTopStudentsCollapsed);
            toggleTopStudentsBtn.setAttribute('aria-expanded', String(!isTopStudentsCollapsed));
            toggleTopStudentsBtn.setAttribute('aria-label', isTopStudentsCollapsed ? 'Show top performing students' : 'Hide top performing students');
            toggleTopStudentsBtn.title = isTopStudentsCollapsed ? 'Show top performing students' : 'Hide top performing students';
        }
    }

    function renderAnalyticsCharts() {
        window.clearTimeout(examChartRevealTimer);
        examChart?.destroy();
        dailyAttemptsChart?.destroy();
        weakTopicsChart?.destroy();

        examChart = new Chart(document.getElementById('examPerformanceChart'), {
            type: 'bar',
            data: {
                labels: examLabels,
                datasets: [{
                    label: 'Average %',
                    data: examScores,
                    backgroundColor: examBaseColor,
                    borderRadius: 0,
                    borderSkipped: false,
                    hidden: true
                }]
            },
            options: {
                animation: {
                    duration: 1400,
                    easing: 'easeOutQuart',
                    delay(context) {
                        return context.type === 'data' ? context.dataIndex * 110 : 0;
                    }
                },
                interaction: {
                    mode: 'nearest',
                    intersect: true
                },
                onHover: (event, activeElements, chart) => {
                    const focusedIndex = activeElements.length ? activeElements[0].index : null;
                    applyExamBarFocus(chart, focusedIndex);
                }
            }
        });

        examChartRevealTimer = window.setTimeout(() => {
            examChart.data.datasets[0].hidden = false;
            examChart.update();
            applyExamBarFocus(examChart, null);
        }, 180);

        dailyAttemptsChart = new Chart(document.getElementById('dailyAttemptsChart'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Attempts',
                    data: dailyCounts,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.16)',
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#dcfce7',
                    pointBorderWidth: 1.5,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                animation: {
                    duration: 1600,
                    easing: 'easeOutQuart'
                },
                animations: {
                    x: {
                        type: 'number',
                        easing: 'easeOutCubic',
                        duration: 900,
                        from: 0,
                        delay(context) {
                            return context.type === 'data' ? context.dataIndex * 80 : 0;
                        }
                    },
                    y: {
                        type: 'number',
                        easing: 'easeOutCubic',
                        duration: 1100,
                        from(context) {
                            const chartArea = context.chart.chartArea;
                            return chartArea ? chartArea.bottom : 0;
                        },
                        delay(context) {
                            return context.type === 'data' ? context.dataIndex * 80 : 0;
                        }
                    }
                },
                onClick: (event, elements, chart) => {
                    if (!elements.length) return;
                    const index = elements[0].index;
                    const day = chart.data.labels[index];
                    openDailyAttemptsModal(day);
                }
            }
        });

        weakTopicsChart = new Chart(document.getElementById('weakTopicsChart'), {
            type: 'bar',
            data: {
                labels: weakTopicLabels,
                datasets: [{
                    label: 'Accuracy %',
                    data: weakTopicAccuracy,
                    backgroundColor: [
                        'rgba(248, 113, 113, 0.82)',
                        'rgba(251, 146, 60, 0.82)',
                        'rgba(250, 204, 21, 0.82)',
                        'rgba(34, 197, 94, 0.82)',
                        'rgba(59, 130, 246, 0.82)',
                        'rgba(14, 165, 233, 0.82)'
                    ],
                    borderRadius: 10,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                scales: {
                    x: {
                        suggestedMax: 100
                    }
                }
            }
        });
    }

    toggleAnalyticsSummaryBtn?.addEventListener('click', () => {
        isAnalyticsSummaryCollapsed = !isAnalyticsSummaryCollapsed;
        syncAnalyticsSummaryVisibility();
    });

    toggleTopStudentsBtn?.addEventListener('click', () => {
        isTopStudentsCollapsed = !isTopStudentsCollapsed;
        syncTopStudentsVisibility();
    });

    syncAnalyticsSummaryVisibility();
    syncTopStudentsVisibility();
    renderAnalyticsCharts();
    window.addEventListener('pageshow', renderAnalyticsCharts);
</script>

@endsection
