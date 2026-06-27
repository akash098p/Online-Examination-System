
@extends('layouts.admin')   

@section('content')
<style>
    h1.text-3xl.font-bold.mb-6 {
        display: none;
    }

    .student-create-card {
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

    .student-create-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), transparent 35%);
        pointer-events: none;
    }

    .student-create-control {
        width: 100%;
        min-height: 3.2rem;
        border-radius: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(2, 6, 23, 0.45);
        color: #fff;
        padding: 0.8rem 1rem;
        outline: none;
    }

    .student-create-select-wrap {
        position: relative;
    }

    .student-create-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: none !important;
        padding-right: 3.5rem;
    }

    .student-create-select-icon {
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
        pointer-events: none;
    }

    .student-create-upload-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        width: 100%;
        border-radius: 1.25rem;
        border: 1px dashed rgba(103, 232, 249, 0.24);
        background:
            linear-gradient(180deg, rgba(34, 211, 238, 0.08), rgba(255, 255, 255, 0.03)),
            rgba(2, 6, 23, 0.34);
        padding: 1rem;
        cursor: pointer;
    }

    .student-create-upload-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        background: rgba(34, 211, 238, 0.12);
        border: 1px solid rgba(103, 232, 249, 0.2);
        color: rgb(165 243 252);
        flex-shrink: 0;
    }

    .student-create-file-input {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>

<div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Student Management</p>
        <h1 class="mt-2 text-3xl font-semibold text-white">Register New Student</h1>
        <p class="mt-2 max-w-3xl text-sm text-slate-300">Create a student account with academic details and login credentials.</p>
    </div>

    <a href="{{ route('admin.students.index') }}" class="admin-action-btn bg-slate-700 text-white">Back</a>
</div>

<h1 class="text-3xl font-bold mb-6">➕ Register New Student</h1>

<div class="student-create-card p-5 sm:p-8">

@if ($errors->any())
<div class="mb-6 rounded-2xl border border-rose-400/30 bg-rose-500/10 p-4 text-rose-100">
<ul class="list-disc list-inside text-sm">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.students.store') }}" class="space-y-8" enctype="multipart/form-data">
@csrf

<div class="grid gap-5 md:grid-cols-2">

<div>
<label class="block text-sm font-semibold text-slate-100">Full Name</label>
<input type="text" name="name" value="{{ old('name') }}"
class="student-create-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Email</label>
<input type="email" name="email" value="{{ old('email') }}"
class="student-create-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">College Name</label>
<input type="text" name="college_name" value="{{ old('college_name') }}"
class="student-create-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Registration Number</label>
<input type="text" name="registration_no" value="{{ old('registration_no') }}"
class="student-create-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Department</label>
<div class="student-create-select-wrap mt-2">
<select name="department"
class="student-create-control student-create-select" required>
<option value="">Select Department</option>
@foreach(config('academix.departments', []) as $department)
<option value="{{ $department }}" {{ old('department') === $department ? 'selected' : '' }}>{{ $department }}</option>
@endforeach
</select>
<span class="student-create-select-icon" aria-hidden="true">
    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4">
        <path d="M6 8L10 12L14 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</span>
</div>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Semester</label>
<div class="student-create-select-wrap mt-2">
<select name="semester"
class="student-create-control student-create-select" required>
<option value="">Select Semester</option>
@foreach(config('academix.semesters', []) as $semester)
<option value="{{ $semester }}" {{ old('semester') === $semester ? 'selected' : '' }}>{{ $semester }}</option>
@endforeach
</select>
<span class="student-create-select-icon" aria-hidden="true">
    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4">
        <path d="M6 8L10 12L14 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</span>
</div>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Contact Number</label>
<input type="text" name="phone" value="{{ old('phone') }}"
class="student-create-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Gender</label>
<div class="student-create-select-wrap mt-2">
<select name="sex"
class="student-create-control student-create-select" required>
<option value="">Select Gender</option>
<option value="male" {{ old('sex')=='male'?'selected':'' }}>Male</option>
<option value="female" {{ old('sex')=='female'?'selected':'' }}>Female</option>
</select>
<span class="student-create-select-icon" aria-hidden="true">
    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4">
        <path d="M6 8L10 12L14 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</span>
</div>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Date of Birth (Optional)</label>
<input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
class="student-create-control mt-2">
</div>

<div class="md:col-span-2">
<label class="block text-sm font-semibold text-slate-100">Profile Photo (Optional)</label>
<label for="profile_photo" class="student-create-upload-box mt-2">
    <span class="student-create-upload-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
            <path d="M12 16V8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
            <path d="M8.5 11.5 12 8l3.5 3.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
            <path d="M5 16.5v.5A2 2 0 0 0 7 19h10a2 2 0 0 0 2-2v-.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
        </svg>
    </span>
    <span class="min-w-0">
        <span class="block text-sm font-semibold text-white">Choose profile photo</span>
        <span id="newStudentPhotoName" class="block truncate text-sm text-slate-300">No file chosen</span>
    </span>
</label>
<input type="file" id="profile_photo" name="profile_photo" accept=".jpg,.jpeg,.png" class="student-create-file-input">
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Password</label>
<input type="password" name="password"
class="student-create-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Confirm Password</label>
<input type="password" name="password_confirmation"
class="student-create-control mt-2" required>
</div>

</div>

<div class="flex flex-wrap gap-3 pt-4">
<button type="submit"
class="admin-action-btn bg-green-600 text-white">
Register Student
</button>

<a href="{{ route('admin.students.index') }}"
class="admin-action-btn bg-slate-700 text-white">
Cancel
</a>
</div>

</form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const uploadInput = document.getElementById('profile_photo');
        const uploadLabel = document.getElementById('newStudentPhotoName');

        uploadInput?.addEventListener('change', () => {
            if (uploadLabel) {
                uploadLabel.textContent = uploadInput.files?.[0]?.name || 'No file chosen';
            }
        });
    });
</script>

@endsection
