@extends('layouts.admin')

@section('content')

<h2 class="text-2xl font-bold mb-4">
    {{ $exam->title }} - Student Attempts
</h2>

<form method="GET" action="{{ route('admin.results.show', $exam->id) }}" class="mb-4 flex flex-wrap items-center gap-3">
    <label for="filter" class="text-sm font-semibold text-gray-200">Filter:</label>
    <select id="filter" name="filter"
            class="px-3 py-2 rounded bg-gray-900/80 border border-gray-600 text-gray-100"
            onchange="this.form.submit()">
        <option value="all" {{ ($filter ?? 'all') === 'all' ? 'selected' : '' }}>All Attempts</option>
        <option value="highest" {{ ($filter ?? '') === 'highest' ? 'selected' : '' }}>Highest Marks</option>
        <option value="lowest" {{ ($filter ?? '') === 'lowest' ? 'selected' : '' }}>Lowest Marks</option>
        <option value="pass" {{ ($filter ?? '') === 'pass' ? 'selected' : '' }}>Passed Students</option>
        <option value="fail" {{ ($filter ?? '') === 'fail' ? 'selected' : '' }}>Failed Students</option>
    </select>

    <select name="department"
            class="px-3 py-2 rounded bg-gray-900/80 border border-gray-600 text-gray-100"
            onchange="this.form.submit()">
        <option value="">All Departments</option>
        @foreach(config('academix.departments', []) as $dept)
            <option value="{{ $dept }}" {{ ($department ?? '') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
        @endforeach
    </select>

    <select name="semester"
            class="px-3 py-2 rounded bg-gray-900/80 border border-gray-600 text-gray-100"
            onchange="this.form.submit()">
        <option value="">All Semesters</option>
        @foreach(config('academix.semesters', []) as $sem)
            <option value="{{ $sem }}" {{ ($semester ?? '') === $sem ? 'selected' : '' }}>{{ $sem }}</option>
        @endforeach
    </select>
</form>

<table class="table-auto w-full border border-gray-700 rounded-lg overflow-hidden">
    <thead class="bg-gray-800">
        <tr>
            <th class="p-3">Student</th>
            <th class="p-3">Reg No</th>
            <th class="p-3">Department</th>
            <th class="p-3">Semester</th>
            <th class="p-3">Marks</th>
            <th class="p-3">%</th>
            <th class="p-3">Status</th>
            <th class="p-3">Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($results as $r)
        <tr class="border-t border-gray-700 hover:bg-gray-800">
            <td class="p-3">
                <div class="flex items-center gap-2">
                    <img src="{{ $r->user->profilePhotoUrl() }}" alt="{{ $r->user->name }}" class="w-8 h-8 rounded-full object-cover border border-white/20">
                    <span>{{ $r->user->name }}</span>
                </div>
            </td>
            <td class="p-3">{{ $r->user->registration_no }}</td>
            <td class="p-3">{{ $r->user->department ?? 'N/A' }}</td>
            <td class="p-3">{{ $r->user->semester ?? 'N/A' }}</td>
            <td class="p-3">{{ $r->obtained_marks }} / {{ $r->total_marks }}</td>
            <td class="p-3">{{ number_format($r->percentage, 2) }}%</td>

            <td class="p-3">
                <span class="px-2 py-1 rounded text-sm
                    {{ $r->status === 'Pass' ? 'bg-green-600' : 'bg-red-600' }}">
                    {{ $r->status }}
                </span>
            </td>

            <td class="p-3">
                <a href="{{ route('admin.results.sheet', $r->id) }}"
                   target="_blank"
                   class="px-3 py-1 bg-indigo-600 rounded hover:bg-indigo-700">
                    View Sheet
                </a>
            </td>
        </tr>
        @empty
        <tr class="border-t border-gray-700">
            <td colspan="8" class="p-4 text-center text-gray-300">No student attempts found for selected filter.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection
