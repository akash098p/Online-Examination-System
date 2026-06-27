@extends('layouts.admin')

@section('content')

<h1 class="text-2xl font-bold mb-4">Edit Question</h1>

<form method="POST" action="{{ route('admin.questions.update', $question) }}">
    @csrf
    @method('PUT')

    {{-- Question Text --}}
    <label class="font-semibold">Question Text</label>
    <textarea name="question_text"
              class="w-full p-2 border rounded mt-1 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100"
              required>{{ old('question_text', $question->question_text) }}</textarea>

    <label class="font-semibold mt-3">Topic Tag</label>
    <input type="text"
           name="topic"
           value="{{ old('topic', $question->topic) }}"
           class="w-full p-2 border rounded mt-1 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100"
           placeholder="Example: Matrices">

    <label class="font-semibold mt-3">Marks</label>
    <input type="number"
           name="marks"
           value="{{ old('marks', $question->marks ?? 1) }}"
           min="1"
           class="w-full p-2 border rounded mt-1 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100"
           required>

    <label class="font-semibold mt-3">Explanation</label>
    <textarea name="explanation"
              rows="3"
              class="w-full p-2 border rounded mt-1 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100"
              placeholder="Optional concept explanation shown in result analysis.">{{ old('explanation', $question->explanation) }}</textarea>

    {{-- Options --}}
    <h3 class="text-xl font-semibold mt-6 mb-2">Options</h3>

    <div id="options-container">
        @foreach($question->options as $index => $option)
            <div class="flex items-center gap-2 mb-2 option-row bg-white dark:bg-gray-800 dark:text-gray-100">
                <input type="text"
                       name="options[]"
                       value="{{ $option->option_text }}"
                       class="flex-1 p-2 border rounded bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100"
                       required>

                <input type="radio"
                       name="correct_option"
                       value="{{ $index }}"
                       class="control-check accent-green-500 dark:accent-green-400"
                       {{ $option->is_correct ? 'checked' : '' }}
                       required>

                <button type="button"
                        onclick="removeOption(this)"
                        class="px-2 py-1 bg-red-500 text-white rounded">
                    ✕
                </button>
            </div>
        @endforeach
    </div>

    <button type="button"
            onclick="addOption()"
            class="mt-2 px-3 py-1 bg-green-500 rounded">
        + Add Option
    </button>

    {{-- Buttons --}}
    <div class="mt-6 flex flex-wrap gap-2">

        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded">
            Update Question
        </button>

        {{-- ADD NEXT QUESTION --}}
        <a href="{{ route('admin.questions.create', $question->exam_id) }}"
           class="px-4 py-2 bg-green-600 text-white rounded">
            + Save & Add Next Question
        </a>

        {{-- Previous --}}
        @if($prev)
            <a href="{{ route('admin.questions.edit', $prev) }}"
               class="px-4 py-2 bg-yellow-400/70 text-white rounded">
                ← Previous
            </a>
        @endif

        {{-- Next --}}
        @if($next)
            <a href="{{ route('admin.questions.edit', $next) }}"
               class="px-4 py-2 bg-indigo-400/70 text-white rounded">
                Next →
            </a>
        @endif

        <a href="{{ route('admin.questions.index', $question->exam_id) }}"
           class="px-4 py-2 bg-yellow-600 rounded">
            Back to Questions
        </a>
    </div>
</form>

{{-- JS --}}
<script>
    function addOption() {
        const container = document.getElementById('options-container');
        const index = container.children.length;

        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 mb-2 option-row';

        div.innerHTML = `
            <input type="text"
                   name="options[]"
                   class="flex-1 p-2 border rounded"
                   required>

            <input type="radio"
                   name="correct_option"
                   value="${index}"
                   class="control-check"
                   required>

            <button type="button"
                    onclick="removeOption(this)"
                    class="px-2 py-1 bg-red-500 text-white rounded">
                ✕
            </button>
        `;

        container.appendChild(div);
    }

    function removeOption(btn) {
        btn.closest('.option-row').remove();

        // re-index radio values
        document.querySelectorAll('input[name="correct_option"]').forEach((r, i) => {
            r.value = i;
        });
    }
</script>

@endsection
