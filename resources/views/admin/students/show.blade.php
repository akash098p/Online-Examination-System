@extends('layouts.admin')

@section('content')
<style>
.glass-card {
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

.glass-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), transparent 35%);
    pointer-events: none;
}

.student-profile-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.student-profile-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 0.75rem;
}

.student-profile-photo-card {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.student-profile-photo {
    width: 5rem;
    height: 5rem;
    border-radius: 1.5rem;
    object-fit: cover;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 12px 24px rgba(2, 6, 23, 0.24);
}

.student-profile-meta-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    color: rgb(165 243 252);
}

.student-profile-meta-value {
    margin-top: 0.6rem;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.5;
    color: white;
    word-break: break-word;
}

@media (max-width: 767px) {
    .student-profile-mobile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .student-profile-mobile-span-2 {
        grid-column: span 2;
    }
}
</style>

<div class="space-y-5">
    <div class="student-profile-header">
        <h1 class="text-3xl font-bold text-white">Student Profile</h1>

        <div class="student-profile-actions">
            <a href="{{ route('admin.students.edit', $student->id) }}" class="admin-action-btn bg-indigo-600 text-white">
                Edit Student
            </a>

            @if($student->violations()->exists())
                <a href="{{ route('admin.violations.student_overview', $student->id) }}" class="admin-action-btn bg-rose-600 text-white">
                    View Violations
                </a>
            @endif

            <form method="POST" action="{{ route('admin.students.toggle', $student->id) }}"
                x-data
                @submit.prevent="appConfirm('Are you sure you want to change student status?', { title: 'Change Student Status', confirmText: 'Yes, Continue' }).then(confirmed => { if (confirmed) $el.submit(); });">
                @csrf
                <button type="submit"
                    {{ $student->trashed() ? 'disabled' : '' }}
                    class="admin-action-btn bg-yellow-600 text-white hover:bg-yellow-700 transition">
                    Block/Unblock
                </button>
            </form>

            <form method="POST" action="{{ route('admin.students.destroy', $student->id) }}"
                x-data
                @submit.prevent="appConfirm('{{ $student->trashed() ? 'Restore this student account to app access?' : 'Remove this student from app access? Their database records will stay.' }}', { title: '{{ $student->trashed() ? 'Restore Student' : 'Remove Student' }}', confirmText: '{{ $student->trashed() ? 'Restore' : 'Remove' }}' }).then(confirmed => { if (confirmed) $el.submit(); });">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="admin-action-btn bg-red-600 text-white hover:bg-red-700 transition">
                    {{ $student->trashed() ? 'Restore' : 'Delete' }}
                </button>
            </form>
        </div>
    </div>

    <div class="student-profile-mobile-grid md:grid md:grid-cols-4 gap-4">
        <!-- First row: Student, Department, D.O.B, College Name -->
        <div class="glass-card p-4 student-profile-photo-card">
            <img src="{{ $student->profilePhotoUrl() }}" alt="{{ $student->name }}" class="student-profile-photo">
            <div>
                <span class="student-profile-meta-label">Student</span>
                <p class="student-profile-meta-value">{{ $student->name }}</p>
            </div>
        </div>

        <div class="glass-card p-4">
            <span class="student-profile-meta-label">Department</span>
            <p class="student-profile-meta-value">{{ $student->department ?? 'N/A' }}</p>
        </div>

        <div class="glass-card p-4">
            <span class="student-profile-meta-label">D.O.B</span>
            <p class="student-profile-meta-value">{{ optional($student->date_of_birth)->format('d M Y') ?? 'N/A' }}</p>
        </div>

        <div class="glass-card p-4">
            <span class="student-profile-meta-label">College Name</span>
            <p class="student-profile-meta-value">{{ $student->college_name ?? 'N/A' }}</p>
        </div>

        <!-- Second row: Reg No, Semester, Joined, Contact No -->
        <div class="glass-card p-4">
            <span class="student-profile-meta-label">Reg No</span>
            <p class="student-profile-meta-value">{{ $student->registration_no ?? 'N/A' }}</p>
        </div>

        <div class="glass-card p-4">
            <span class="student-profile-meta-label">Semester</span>
            <p class="student-profile-meta-value">{{ $student->semester ?? 'N/A' }}</p>
        </div>

        <div class="glass-card p-4">
            <span class="student-profile-meta-label">Joined</span>
            <p class="student-profile-meta-value">{{ $student->created_at->format('d M Y') }}</p>
        </div>

        <div class="glass-card p-4">
            <span class="student-profile-meta-label">Contact No</span>
            <p class="student-profile-meta-value">{{ $student->phone ?? 'N/A' }}</p>
        </div>
    </div>

    <h2 class="text-xl font-semibold text-white">Exam History</h2>

    @forelse($results as $r)
        <div class="glass-card p-4 flex justify-between items-center">
            <div>
                <p class="font-semibold text-white">{{ $r->exam->title ?? 'Exam' }}</p>
                <p class="text-sm text-gray-300">
                    {{ optional($r->submitted_at ?? $r->created_at)->format('d M Y, h:i A') }}
                </p>
            </div>

            <div class="text-right">
                <p class="text-green-400 font-semibold">Score: {{ $r->obtained_marks }} / {{ $r->total_marks }}</p>
                <a href="{{ route('admin.results.sheet', $r->id) }}"
                   class="inline-block mt-2 px-3 py-1 text-sm bg-indigo-600/80 rounded hover:bg-indigo-700 transition">
                    View Sheet
                </a>
            </div>
        </div>
    @empty
        <p class="text-gray-300">No exams attempted.</p>
    @endforelse
</div>

@endsection
