@extends('layouts.admin')

@section('content')

<style>
    .question-order-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 999px;
        background: rgba(34, 211, 238, 0.14);
        border: 1px solid rgba(34, 211, 238, 0.24);
        color: rgb(207 250 254);
        font-weight: 700;
        font-size: 0.9rem;
    }
</style>

<div class="mb-4 flex items-center justify-between gap-3">
    <h1 class="text-2xl font-bold">
        Questions for: {{ $exam->title }}
    </h1>

    <div class="flex items-center gap-3 whitespace-nowrap">
        <a href="{{ route('admin.exams.show', $exam) }}" class="admin-action-btn bg-yellow-600 text-white">
            Back
        </a>

        <a href="{{ route('admin.questions.create', $exam) }}"
           class="admin-action-btn bg-blue-600 text-white">
            + Add Question
        </a>
    </div>
</div>

<table class="w-full bg-white dark:bg-gray-800 rounded shadow">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="p-3 text-left w-16">#</th>
            <th class="p-3 text-left">Question</th>
            <th class="p-3">Options</th>
            <th class="p-3">Correct Answer</th>
            <th class="p-3">Actions</th>
        </tr>
    </thead>

    <tbody>
        @forelse($questions as $q)
        <tr class="border-t">
            <td class="p-3 text-gray-900 dark:text-gray-100">
                <span class="question-order-badge">{{ $loop->iteration }}</span>
            </td>

            <td class="p-3 text-gray-900 dark:text-gray-100">{{ $q->question_text }}</td>

            <td class="p-3 text-gray-900 dark:text-gray-100">
                <div class="space-y-1">
                    @foreach($q->options as $index => $op)
                        <div class="text-sm {{ $op->is_correct ? 'text-green-400 font-semibold' : 'text-gray-200' }}">
                            {{ chr(65 + $index) }}) {{ $op->option_text }}
                        </div>
                    @endforeach
                </div>
            </td>

            <td class="p-3">
                @foreach($q->options as $op)
                    @if($op->is_correct)
                        <span class="text-green-600 font-bold">{{ $op->option_text }}</span>
                    @endif
                @endforeach
            </td>

            <td class="p-3 flex gap-2 text-gray-900 dark:text-gray-100">
                <a href="{{ route('admin.questions.edit', $q) }}"
                   class="admin-action-btn bg-blue-600 text-white">
                    Edit
                </a>

                <form method="POST"
                      action="{{ route('admin.questions.destroy', $q) }}"
                      onsubmit="event.preventDefault(); appConfirm('Delete question?', { title: 'Delete Question', confirmText: 'Delete' }).then(confirmed => { if (confirmed) this.submit(); });"
                      class="inline-block">
                    @csrf @method('DELETE')
                    <button class="admin-action-btn bg-red-600 text-white">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="p-3 text-gray-500 text-center">
                No questions added yet.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection
