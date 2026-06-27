
@extends('layouts.admin')

@section('content')
<style>
    h1.text-3xl.font-bold.mb-6 {
        display: none;
    }
    .student-edit-card {
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

    .student-edit-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), transparent 35%);
        pointer-events: none;
    }

    .student-edit-control {
        width: 100%;
        min-height: 3.2rem;
        border-radius: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(2, 6, 23, 0.45);
        color: #fff;
        padding: 0.8rem 1rem;
        outline: none;
    }

    .student-edit-control::placeholder {
        color: rgb(148 163 184);
    }

    .student-edit-select-wrap {
        position: relative;
    }

    .student-edit-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: none !important;
        padding-right: 3.5rem;
    }

    .student-edit-select-icon {
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

    .student-photo-upload {
        display: grid;
        gap: 1rem;
    }

    .student-photo-preview {
        width: 5rem;
        height: 5rem;
        border-radius: 1.5rem;
        object-fit: cover;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .student-photo-dropzone {
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
        color: rgb(226 232 240);
        cursor: pointer;
    }

    .student-photo-icon {
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

    .student-photo-input {
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

    @media (min-width: 768px) {
        .student-photo-upload {
            grid-template-columns: auto minmax(0, 1fr);
            align-items: center;
        }
    }
</style>

<div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Student Management</p>
        <h1 class="mt-2 text-3xl font-semibold text-white">Edit Student Profile</h1>
        <p class="mt-2 max-w-3xl text-sm text-slate-300">Update student identity, academic details, profile photo and password.</p>
    </div>

    <a href="{{ route('admin.students.show', $student->id) }}" class="admin-action-btn bg-slate-700 text-white">Back to Profile</a>
</div>

<h1 class="text-3xl font-bold mb-6">✏ Edit Student Profile</h1>

<div class="student-edit-card p-5 sm:p-8">

@if ($errors->any())
<div class="mb-6 rounded-2xl border border-rose-400/30 bg-rose-500/10 p-4 text-rose-100">
<ul class="list-disc list-inside space-y-1 text-sm">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="space-y-8" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="grid gap-5 md:grid-cols-2">

<div>
<label class="block text-sm font-semibold text-slate-100">Full Name</label>
<input type="text" name="name"
value="{{ old('name', $student->name) }}"
class="student-edit-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Email</label>
<input type="email" name="email"
value="{{ old('email', $student->email) }}"
class="student-edit-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">College Name</label>
<input type="text" name="college_name"
value="{{ old('college_name', $student->college_name) }}"
class="student-edit-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Registration Number</label>
<input type="text" name="registration_no"
value="{{ old('registration_no', $student->registration_no) }}"
class="student-edit-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Department</label>
<div class="student-edit-select-wrap mt-2">
<select name="department"
class="student-edit-control student-edit-select" required>
@foreach(config('academix.departments', []) as $department)
<option value="{{ $department }}" {{ old('department', $student->department) === $department ? 'selected' : '' }}>{{ $department }}</option>
@endforeach
</select>
<span class="student-edit-select-icon" aria-hidden="true">
    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4">
        <path d="M6 8L10 12L14 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</span>
</div>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Semester</label>
<div class="student-edit-select-wrap mt-2">
<select name="semester"
class="student-edit-control student-edit-select" required>
@foreach(config('academix.semesters', []) as $semester)
<option value="{{ $semester }}" {{ old('semester', $student->semester) === $semester ? 'selected' : '' }}>{{ $semester }}</option>
@endforeach
</select>
<span class="student-edit-select-icon" aria-hidden="true">
    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4">
        <path d="M6 8L10 12L14 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</span>
</div>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Contact Number</label>
<input type="text" name="phone"
value="{{ old('phone', $student->phone) }}"
class="student-edit-control mt-2" required>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Gender</label>
<div class="student-edit-select-wrap mt-2">
<select name="sex"
class="student-edit-control student-edit-select" required>
<option value="male" {{ strtolower((string) old('sex', $student->sex))=='male'?'selected':'' }}>Male</option>
<option value="female" {{ strtolower((string) old('sex', $student->sex))=='female'?'selected':'' }}>Female</option>
</select>
<span class="student-edit-select-icon" aria-hidden="true">
    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4">
        <path d="M6 8L10 12L14 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</span>
</div>
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">Date of Birth</label>
<input type="date" name="date_of_birth"
value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
class="student-edit-control mt-2">
</div>

<div class="md:col-span-2">
<label class="block text-sm font-semibold text-slate-100">Profile Photo</label>
<div class="student-photo-upload mt-3">
    <img src="{{ $student->profilePhotoUrl() }}" alt="{{ $student->name }}"
         class="student-photo-preview">
    <div class="min-w-0">
        <label for="profile_photo" class="student-photo-dropzone">
            <span class="student-photo-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                    <path d="M12 16V8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                    <path d="M8.5 11.5 12 8l3.5 3.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                    <path d="M5 16.5v.5A2 2 0 0 0 7 19h10a2 2 0 0 0 2-2v-.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                </svg>
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-white">Choose a new photo</span>
                <span id="studentPhotoFilename" class="block truncate text-sm text-slate-300">No file chosen</span>
            </span>
        </label>
        <input type="file" id="profile_photo" name="profile_photo" accept=".jpg,.jpeg,.png"
               class="student-photo-input">
        @if($student->profile_photo)
            <label class="mt-3 inline-flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="remove_profile_photo" value="1" class="control-check">
                Remove current photo
            </label>
        @endif
    </div>
</div>
</div>

<!-- 🔐 PASSWORD RESET SECTION -->

<div class="md:col-span-2 rounded-3xl border border-white/10 bg-black/20 p-5">
<p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Password Reset</p>
<h2 class="mt-2 text-xl font-semibold text-white">Reset Password</h2>

<div class="mt-5 grid gap-5 md:grid-cols-2">

<div>
<label class="block text-sm font-semibold text-slate-100">
New Password
</label>
<input type="password" name="password"
class="student-edit-control mt-2"
placeholder="Leave blank if no change">
</div>

<div>
<label class="block text-sm font-semibold text-slate-100">
Confirm New Password
</label>
<input type="password" name="password_confirmation"
class="student-edit-control mt-2"
placeholder="Re-enter new password">
</div>

</div>
</div>

</div>

<div class="flex flex-wrap gap-3 pt-4">
<button type="submit"
class="admin-action-btn bg-indigo-600 text-white">
Update Student
</button>

<a href="{{ route('admin.students.show', $student->id) }}"
class="admin-action-btn bg-slate-700 text-white">
Cancel
</a>
</div>

</form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const photoInput = document.getElementById('profile_photo');
        const fileNameLabel = document.getElementById('studentPhotoFilename');

        photoInput?.addEventListener('change', () => {
            if (!fileNameLabel) {
                return;
            }

            fileNameLabel.textContent = photoInput.files?.[0]?.name || 'No file chosen';
        });
    });
</script>

@endsection
