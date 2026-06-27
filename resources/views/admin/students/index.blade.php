
@extends('layouts.admin')                       

@section('content')

<h1 class="text-3xl font-bold mb-6">🎓 Student Management</h1>

<!-- Top Bar -->
@php
    $search = $search ?? request('search');
    $selectedDepartments = $selectedDepartments ?? array_values(array_filter((array) request('department', []), fn ($value) => $value !== ''));
    $selectedSemesters = $selectedSemesters ?? array_values(array_filter((array) request('semester', []), fn ($value) => $value !== ''));
    $departmentLabel = count($selectedDepartments) ? implode(', ', $selectedDepartments) : 'All';
    $semesterLabel = count($selectedSemesters) ? implode(', ', $selectedSemesters) : 'All';
@endphp
<div class="custom-dropdown-group flex flex-wrap items-center gap-3 mb-4" style="overflow: visible; position: relative; z-index: 1;">

    <!-- Filter Form -->
    <form method="GET" class="custom-dropdown-form flex flex-wrap items-center gap-3" style="overflow: visible; position: relative;">
        <input type="text" name="search" placeholder="Search name, email, reg no"
        value="{{ $search }}"
        class="p-2 rounded bg-gray-800 border border-gray-700 text-gray-200">

        <div class="w-full sm:w-auto" style="overflow: visible; position: relative; z-index: 1;">
            <details class="custom-multi-select compact" style="overflow: visible; z-index: 999;">
                <summary>
                    <span class="truncate">{{ $departmentLabel }}</span>
                    <span class="dropdown-toggle">▾</span>
                </summary>
                <div class="multi-select-panel" style="max-height: 16rem; width: 18rem;">
                    <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                        <input type="checkbox" name="department[]" value="" {{ count($selectedDepartments) === 0 ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                        <span>All departments</span>
                    </label>
                    @foreach(config('academix.departments', []) as $option)
                        <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                            <input type="checkbox" name="department[]" value="{{ $option }}" {{ in_array($option, $selectedDepartments, true) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                            <span>{{ $option }}</span>
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
                <div class="multi-select-panel" style="max-height: 16rem; width: 15rem;">
                    <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                        <input type="checkbox" name="semester[]" value="" {{ count($selectedSemesters) === 0 ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                        <span>All semesters</span>
                    </label>
                    @foreach(config('academix.semesters', []) as $option)
                        <label class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-white/5">
                            <input type="checkbox" name="semester[]" value="{{ $option }}" {{ in_array($option, $selectedSemesters, true) ? 'checked' : '' }} class="h-4 w-4 rounded-full accent-indigo-500">
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
            </details>
        </div>

        <button class="px-4 py-2 bg-indigo-600 rounded text-white">
            Filter
        </button>
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 rounded bg-gray-700 text-white hover:bg-gray-600">
            Reset
        </a>
    </form>

    <!-- Add Student Button -->
    <a href="{{ route('admin.students.create') }}"
       class="px-4 py-2 bg-green-600 rounded text-white ml-auto">
        + Add Student
    </a>

</div>

<div class="overflow-x-auto bg-gray-800 rounded-xl shadow">
<table class="w-full text-sm text-gray-300">
<thead class="bg-gray-900 text-gray-200">
<tr>
<th class="p-3 text-left">Name</th>
<th class="p-3 text-left">Reg No</th>
<th class="p-3 text-left">Email</th>
<th class="p-3 text-left">Department</th>
<th class="p-3 text-left">Semester</th>
<th class="p-3 text-left">Status</th>
<th class="p-3 text-left">Action</th>
</tr>
</thead>

<tbody>
@foreach($students as $s)
<tr class="border-b border-gray-700 hover:bg-gray-700/40 transition">
<td class="p-3">
    <div class="flex items-center gap-2">
        <img src="{{ $s->profilePhotoUrl() }}" alt="{{ $s->name }}" class="w-8 h-8 rounded-full object-cover border border-white/20">
        <span>{{ $s->name }}</span>
    </div>
</td>
<td class="p-3">{{ $s->registration_no }}</td>
<td class="p-3">{{ $s->email }}</td>
<td class="p-3">{{ $s->department ?? 'N/A' }}</td>
<td class="p-3">{{ $s->semester }}</td>

<td class="p-3">
@if($s->trashed())
<span class="px-2 py-1 text-xs bg-gray-600 rounded">Deleted From App</span>
@elseif($s->is_blocked)
<span class="px-2 py-1 text-xs bg-red-600 rounded">Blocked</span>
@else
<span class="px-2 py-1 text-xs bg-green-600 rounded">Active</span>
@endif
</td>

<td class="p-3 flex flex-wrap gap-2">

<!-- View -->
<a href="{{ route('admin.students.show',$s->id) }}"
class="admin-action-btn bg-blue-600 text-white">
View
</a>

<!-- Block / Unblock -->
<form method="POST" action="{{ route('admin.students.toggle',$s->id) }}"
    x-data
    @submit.prevent="appConfirm('Are you sure you want to change student status?', { title: 'Change Student Status', confirmText: 'Yes, Continue' }).then(confirmed => { if (confirmed) $el.submit(); });">
    @csrf
    <button type="submit"
        {{ $s->trashed() ? 'disabled' : '' }}
        class="admin-action-btn bg-yellow-600 text-white hover:bg-yellow-700 transition">
        Block/Unblock
    </button>
</form>

</td>
</tr>
@endforeach
</tbody>
</table>
</div>

<div class="mt-4">
{{ $students->links() }}
</div>

@endsection
