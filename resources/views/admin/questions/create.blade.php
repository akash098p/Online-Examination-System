@extends('layouts.admin')

@section('content')

<h1 class="text-2xl font-bold mb-4">
    Add Question — {{ $exam->title }}
</h1>

<form action="{{ route('admin.questions.store', $exam) }}" method="POST">
    @csrf

    {{-- Question Text --}}
    <label class="font-semibold">Question Text</label>
    <textarea name="question_text"
              class="w-full p-2 border rounded mt-1 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100"
              required></textarea>

    <label class="font-semibold mt-3">Topic Tag</label>
    <input type="text"
       name="topic"
       class="w-full p-2 border rounded mt-1 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100"
       placeholder="Example: Matrices">

    <label class="font-semibold mt-3">Marks</label>
    <input type="number"
       name="marks"
       value="1"
       min="1"
       class="w-full p-2 border rounded mt-1 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100"
       required>

    <label class="font-semibold mt-3">Explanation</label>
    <textarea name="explanation"
              rows="3"
              class="w-full p-2 border rounded mt-1 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100"
              placeholder="Optional concept explanation shown in result analysis."></textarea>
         

    {{-- Options --}}
    <h3 class="text-xl font-semibold mt-6 mb-2">Options</h3>

    <div id="options-container">
        {{-- Default 4 options --}}
        @for($i=0; $i<4; $i++)
            <div class="option-item p-3 border rounded mb-2 flex gap-2 items-start bg-white dark:bg-gray-800 dark:text-gray-100">
                <input type="radio"
                       name="correct_option"
                       value="{{ $i }}"
                       class="mt-2 control-check accent-green-500 dark:accent-green-400"
                       required>

                <input type="text"
                       name="options[]"
                       placeholder="Option {{ $i+1 }}"
                       class="flex-1 p-2 border rounded bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-100"
                       required>

                <button type="button"
                        onclick="removeOption(this)"
                        class="text-red-600 font-bold px-2">
                    ✕
                </button>
            </div>
        @endfor
    </div>

    <button type="button"
            onclick="addOption()"
            class="mt-2 px-3 py-1 bg-green-500 rounded">
        + Add Option
    </button>

    {{-- Buttons --}}
    <div class="mt-6 flex gap-3">
        <button type="submit"
                name="action"
                value="save_next"
                class="px-4 py-2 bg-blue-600 text-white rounded">
            Save & Add Next
        </button>

        <a href="{{ route('admin.questions.index', $exam) }}"
           class="px-4 py-2 bg-yellow-600 rounded">
            Back to Questions
        </a>
    </div>
</form>

<script>
    let optionIndex = 4;

    function addOption() {
        const container = document.getElementById('options-container');

        const div = document.createElement('div');
        div.className = "option-item p-3 border rounded mb-2 flex gap-2 items-start bg-white dark:bg-gray-800 dark:text-gray-100";

        div.innerHTML = `
            <input type="radio" name="correct_option" value="${optionIndex}" class="mt-2 control-check accent-green-500 dark:accent-green-400" required>
            <input type="text" name="options[]" class="flex-1 p-2 border rounded bg-white dark:bg-gray-700 dark:text-gray-100" placeholder="Option ${optionIndex+1}" required>
            <button type="button" onclick="removeOption(this)" class="text-red-600 font-bold px-2">✕</button>
        `;

        container.appendChild(div);
        optionIndex++;
    }

    function removeOption(btn) {
        btn.closest('.option-item').remove();
    }
</script>

@endsection
