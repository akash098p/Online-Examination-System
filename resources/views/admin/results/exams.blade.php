@extends('layouts.admin')

@section('content')

@php
    $departmentLabel = count($selectedDepartments ?? []) ? implode(', ', $selectedDepartments) : 'All';
    $semesterLabel = count($selectedSemesters ?? []) ? implode(', ', $selectedSemesters) : 'All';
@endphp

<h2 class="text-2xl font-bold mb-6">Completed Exams</h2>

<form method="GET" action="{{ route('admin.results.index') }}" class="mb-4 flex flex-wrap items-center gap-3 custom-dropdown-group" style="overflow: visible; position: relative; z-index: 1;">
    <input
        type="text"
        name="search"
        value="{{ $search ?? '' }}"
        placeholder="Search exam by title or subject"
        class="w-full md:w-80 px-3 py-2 rounded bg-gray-900/80 border border-gray-600 text-gray-100 placeholder:text-gray-400"
    >
    <button type="submit" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
        Search
    </button>

    <div class="w-full sm:w-auto" style="overflow: visible; position: relative; z-index: 1;">
        <details class="custom-multi-select compact" style="overflow: visible; z-index: 999;">
            <summary>
                <span class="truncate">{{ $departmentLabel }}</span>
                <span class="dropdown-toggle">▾</span>
            </summary>
            <div class="multi-select-panel">
                <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                    <input type="checkbox" name="department[]" value="" {{ empty($selectedDepartments ?? []) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                    <span>All Departments</span>
                </label>
                @foreach(config('academix.departments', []) as $dept)
                    <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                        <input type="checkbox" name="department[]" value="{{ $dept }}" {{ in_array($dept, $selectedDepartments ?? [], true) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                        <span>{{ $dept }}</span>
                    </label>
                @endforeach
            </div>
        </details>
    </div>

    <div class="w-full sm:w-auto" style="overflow: visible; position: relative; z-index: 1;">
        <details class="custom-multi-select compact" style="overflow: visible; z-index: 999;">
            <summary>
                <span class="truncate">{{ $semesterLabel }}</span>
                <span class="dropdown-toggle">▾</span>
            </summary>
            <div class="multi-select-panel">
                <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                    <input type="checkbox" name="semester[]" value="" {{ empty($selectedSemesters ?? []) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                    <span>All Semesters</span>
                </label>
                @foreach(config('academix.semesters', []) as $sem)
                    <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                        <input type="checkbox" name="semester[]" value="{{ $sem }}" {{ in_array($sem, $selectedSemesters ?? [], true) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                        <span>{{ $sem }}</span>
                    </label>
                @endforeach
            </div>
        </details>
    </div>
    <button type="submit" class="px-4 py-2 rounded bg-slate-700 hover:bg-slate-600 text-white">Apply</button>
    <div class="flex flex-wrap items-center gap-3 justify-center sm:justify-start w-full sm:w-auto">
        <a href="{{ route('admin.results.index') }}" class="px-4 py-2 rounded bg-gray-700 hover:bg-gray-600 text-white">
            Reset
        </a>
    </div>
</form>

<table class="table-auto w-full border border-gray-700 rounded-lg overflow-hidden">
    <thead class="bg-gray-800">
        <tr>
            <th class="p-3 text-left">Exam</th>
            <th class="p-3 text-center">Department</th>
            <th class="p-3 text-center">Semester</th>
            <th class="p-3">Attempts</th>
            <th class="p-3">Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($exams as $exam)
        @php
            $examDepartments = empty($exam->department) ? 'All' : implode(', ', (array) $exam->department);
            $examSemesters = empty($exam->semester) ? 'All' : implode(', ', (array) $exam->semester);
        @endphp
        <tr class="border-t border-gray-700 hover:bg-gray-800">
            <td class="p-3">{{ $exam->title }}</td>
            <td class="p-3 text-center">{{ $examDepartments }}</td>
            <td class="p-3 text-center">{{ $examSemesters }}</td>
            <td class="p-3 text-center">{{ $exam->results_count }}</td>
            <td class="p-3 text-center">
                <a href="{{ route('admin.results.show', ['exam' => $exam->id, 'department' => $department ?? '', 'semester' => $semester ?? '']) }}"
                   class="px-4 py-1 bg-indigo-600 rounded hover:bg-indigo-700">
                    View Students
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="p-4 text-center text-gray-400">
                No completed exams yet.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection
