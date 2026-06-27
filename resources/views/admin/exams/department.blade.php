@extends('layouts.admin')

@section('content')
<style>
    .exam-page-card,
    .exam-filters-card,
    .exam-table-card {
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

    .exam-page-card::before,
    .exam-filters-card::before,
    .exam-table-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), transparent 35%);
        pointer-events: none;
    }

    .exam-filter-field {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(15, 23, 42, 0.6);
        color: rgb(241 245 249);
        padding: 0.9rem 1rem;
    }

    .exam-filter-field::placeholder {
        color: rgb(148 163 184);
    }

    .exam-filters-form {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 0.75rem;
    }

    .exam-search-wrap {
        flex: 1 1 auto;
        min-width: 0;
    }

    .exam-status-wrap {
        flex: 0 0 13rem;
    }

    .exam-filter-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 0 0 auto;
    }

    .semester-toggle-btn {
        appearance: none;
        -webkit-appearance: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        padding: 0;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.14);
        background: rgba(255,255,255,0.06);
        color: white;
        outline: none;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .semester-toggle-btn::-moz-focus-inner {
        border: 0;
    }

    .semester-toggle-btn:hover {
        background: rgba(255,255,255,0.12);
    }

    .semester-toggle-btn svg {
        display: none;
    }

    .semester-toggle-btn-text {
        font-size: 1.5rem;
        line-height: 1;
        transition: transform 0.2s ease;
    }

    .semester-toggle-btn.is-collapsed .semester-toggle-btn-text {
        transform: rotate(180deg);
    }

    .semester-section-content.hidden {
        display: none;
    }

    @media (max-width: 767px) {
        .exam-page-card,
        .exam-filters-card,
        .exam-table-card {
            border-radius: 1.25rem;
        }

        .exam-page-card {
            padding: 1.1rem;
        }

        .exam-filters-card {
            padding: 1rem;
        }

        .exam-filters-form {
            flex-wrap: wrap;
            align-items: stretch;
            gap: 0.85rem;
        }

        .exam-search-wrap,
        .exam-status-wrap {
            width: 100%;
            flex: 1 1 100%;
        }

        .exam-filter-actions {
            width: 100%;
            gap: 0.75rem;
        }

        .exam-filter-actions > * {
            flex: 1 1 0;
            justify-content: center;
            text-align: center;
        }
    }
</style>

<div class="space-y-6">
    <div class="exam-page-card p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-200">Department Exams</p>
                <h1 class="mt-2 text-3xl font-semibold text-white">{{ $department }}</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-300">
                    Manage exams for this department and semester from 6th to 1st.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.exams.index') }}" class="admin-action-btn bg-slate-700 text-white">Back to Departments</a>
                <a href="{{ route('admin.exams.create') }}" class="admin-action-btn bg-sky-600 text-white">+ Create Exam</a>
            </div>
        </div>
    </div>
    
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="exam-filters-card p-5">
        <form method="GET" action="{{ route('admin.exams.department', $department) }}" class="exam-filters-form">
            <div class="exam-search-wrap">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Search exam by title or subject"
                    class="exam-filter-field"
                >
            </div>

            <div class="exam-status-wrap">
                <select name="status" class="exam-filter-field">
                    <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="draft" {{ ($status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ ($status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <div class="exam-filter-actions">
                <button type="submit" class="admin-action-btn bg-sky-600 text-white">
                    Apply
                </button>

                <a href="{{ route('admin.exams.department', $department) }}" class="admin-action-btn bg-slate-600 text-white">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @foreach($semesterSections as $section)
        <div class="exam-table-card overflow-hidden">
            <div class="border-b border-white/10 px-5 py-4 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-white">{{ $section['name'] }} Semester</h2>
                    <p class="mt-1 text-sm text-slate-300">Showing {{ $department }} exams available for {{ $section['name'] }} semester.</p>
                </div>

                <button
                    type="button"
                    class="semester-toggle-btn"
                    data-semester-toggle="{{ $section['name'] }}"
                    aria-expanded="true"
                    aria-label="Hide {{ $section['name'] }} semester"
                >
                    <span class="semester-toggle-btn-text" aria-hidden="true">^</span>
                </button>
            </div>

            <div class="semester-section-content" data-semester-content="{{ $section['name'] }}">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/10 text-gray-200">
                        <tr>
                            <th class="p-3 text-left">Title</th>
                            <th class="p-3 text-center">Semester</th>
                            <th class="p-3 text-center">Questions</th>
                            <th class="p-3 text-center">Duration</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($section['exams'] as $exam)
                            @php $examSemesters = empty($exam->semester) ? 'All' : implode(', ', (array) $exam->semester); @endphp
                            <tr class="border-t border-white/10 hover:bg-white/[0.04]">
                                <td class="p-3">
                                    <div class="font-semibold text-white">{{ $exam->title }}</div>
                                    <div class="text-sm text-gray-400">{{ $exam->subject }}</div>
                                </td>

                                <td class="p-3 text-center text-slate-200">
                                    {{ $examSemesters }}
                                </td>

                                <td class="p-3 text-center text-slate-200">
                                    {{ $exam->questions_count ?? $exam->questions->count() }}
                                </td>

                                <td class="p-3 text-center text-slate-200">
                                    {{ $exam->duration_minutes }} mins
                                </td>

                                <td class="p-3 text-center">
                                    <span class="px-2 py-1 rounded text-white text-xs {{ $exam->status === 'published' ? 'bg-green-600' : 'bg-yellow-600' }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </td>

                                <td class="p-3">
                                    <div class="flex justify-center gap-2 flex-wrap">
                                        <a href="{{ route('admin.exams.show', $exam) }}" class="admin-action-btn bg-gray-600 hover:bg-gray-500 text-white">
                                            View
                                        </a>

                                        <a href="{{ route('admin.exams.edit', $exam) }}#aiGeneratorForm" class="admin-action-btn bg-blue-600 hover:bg-blue-700 text-white">
                                            AI Builder
                                        </a>

                                        <form action="{{ route('admin.exams.toggle_publish', $exam) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="event.preventDefault(); appConfirm('{{ $exam->status === 'published' ? 'Are you sure you want to unpublish this exam?' : 'Are you sure you want to publish this exam?' }}', { title: '{{ $exam->status === 'published' ? 'Unpublish Exam' : 'Publish Exam' }}', confirmText: '{{ $exam->status === 'published' ? 'Unpublish' : 'Publish' }}' }).then(confirmed => { if (confirmed) this.submit(); });">
                                            @csrf
                                            <button class="admin-action-btn text-white {{ $exam->status === 'published' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }}">
                                                {{ $exam->status === 'published' ? 'Unpublish' : 'Publish' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.exams.destroy', $exam) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="event.preventDefault(); appConfirm('Are you sure you want to delete this exam?', { title: 'Delete Exam', confirmText: 'Delete' }).then(confirmed => { if (confirmed) this.submit(); });">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-action-btn bg-red-600 hover:bg-red-700 text-white">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-400">
                                    No exams found for {{ $section['name'] }} semester.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-semester-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const semester = button.getAttribute('data-semester-toggle');
            const content = document.querySelector(`[data-semester-content="${semester}"]`);
            if (!content) return;

            const isCollapsed = content.classList.toggle('hidden');
            button.classList.toggle('is-collapsed', isCollapsed);
            button.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
            button.setAttribute('aria-label', `${isCollapsed ? 'Show' : 'Hide'} ${semester} semester`);
        });
    });
});
</script>
@endsection
