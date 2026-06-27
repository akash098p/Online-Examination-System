<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $faviconVersion = file_exists(public_path('App-logo.png')) ? filemtime(public_path('App-logo.png')) : time();
        $faviconUrl = asset('App-logo.png') . '?v=' . $faviconVersion;
    @endphp
    <title>{{ config('app.name', 'Online Exam System') }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-800">
    @php
        $responses = \App\Models\Response::where('exam_id', $exam->id)
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('question_id');

        $totalQuestions = $exam->questions->count();
        $attempted = $responses->whereNotNull('option_id')->count();
        $correct = $responses->where('is_correct', 1)->count();
        $wrong = $responses->where('is_correct', 0)->whereNotNull('option_id')->count();
        $notAttempted = $totalQuestions - $attempted;
        $passPercentage = (float) ($exam->pass_percentage ?? 40);
        $passed = $percentage >= $passPercentage;
        $resultBadgeClass = $passed ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700';
        $percentageBadgeClass = $passed ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700';
        $attemptBadgeClass = $isAttempted ?? false;
    @endphp

    <div class="min-h-screen bg-stone-50 py-8 sm:py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-2xl border border-stone-200 bg-stone-100 shadow-sm">
                <div class="border-b border-slate-200 px-6 py-6 sm:px-8">
                    <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">{{ $exam->title }}</h1>
                            <p class="mt-2 text-sm text-slate-600">A simple summary of your exam performance.</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold {{ $resultBadgeClass }}">
                            {{ $passed ? 'Passed' : 'Failed' }}
                        </span>
                    </div>
                </div>

                <div class="px-6 py-6 sm:px-8">
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                        <div class="rounded-xl border border-stone-200 bg-stone-50 p-3 sm:p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Total Questions</p>
                            <p class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $totalQuestions }}</p>
                        </div>
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 sm:p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Correct</p>
                            <p class="mt-3 text-2xl font-bold text-emerald-800 sm:text-3xl">{{ $correct }}</p>
                        </div>
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 sm:p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">Not Attempted</p>
                            <p class="mt-3 text-2xl font-bold text-amber-800 sm:text-3xl">{{ $notAttempted }}</p>
                        </div>
                        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 sm:p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-rose-700">Wrong</p>
                            <p class="mt-3 text-2xl font-bold text-rose-800 sm:text-3xl">{{ $wrong }}</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 lg:grid-cols-2">
                        <div class="rounded-xl border border-stone-200 bg-stone-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Marks Obtained</p>
                            <div class="mt-3 flex items-end gap-2">
                                <p class="text-3xl font-bold text-slate-900">{{ number_format($obtainedMarks, 2) }}</p>
                                <p class="pb-1 text-sm font-medium text-slate-500">/ {{ number_format($totalMarks, 2) }}</p>
                            </div>
                            <div class="mt-5 rounded-lg border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-slate-600">
                                Score Progress: <span class="font-semibold text-slate-900">{{ number_format($percentage, 2) }}%</span>
                            </div>
                        </div>

                        <div class="rounded-xl border border-stone-200 bg-stone-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Percentage</p>
                            <div class="mt-3 flex items-center justify-between gap-4">
                                <p class="text-3xl font-bold text-slate-900 counter" data-value="{{ number_format($percentage, 2, '.', '') }}" data-type="percentage">0%</p>
                                <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold {{ $percentageBadgeClass }}">
                                    {{ $passed ? 'Pass' : 'Fail' }}
                                </span>
                            </div>
                            <p class="mt-5 text-sm text-slate-600">
                                Pass threshold:
                                <span class="font-semibold text-slate-800 counter" data-value="{{ number_format($passPercentage, 2, '.', '') }}" data-type="percentage">0%</span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-6 rounded-2xl border border-stone-200 bg-stone-100 shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5 sm:px-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Question Review</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-900">Question-wise Performance</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-stone-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                <th class="px-6 py-4">Question</th>
                                <th class="px-6 py-4">Attempt</th>
                                <th class="px-6 py-4">Result</th>
                                <th class="px-6 py-4">Marks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-stone-50 text-sm text-slate-700">
                            @foreach($exam->questions as $index => $question)
                                @php
                                    $resp = $responses[$question->id] ?? null;
                                    $isAttempted = $resp && $resp->option_id !== null;
                                    $isCorrect = $resp && $resp->is_correct == 1;
                                    $marksDisplay = 0;

                                    if ($isCorrect) {
                                        $marksDisplay = $resp->marks_obtained;
                                    } elseif ($isAttempted && $exam->negative_enabled) {
                                        $marksDisplay = -$exam->negative_marking;
                                    }
                                @endphp
                                <tr class="hover:bg-stone-100">
                                    <td class="px-6 py-4 font-semibold text-slate-900">Q{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $isAttempted ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $isAttempted ? 'Attempted' : 'Not Attempted' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(!$isAttempted)
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Not Attempted</span>
                                        @elseif($isCorrect)
                                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Correct</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Wrong</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-semibold">
                                        @if($isCorrect)
                                            <span class="text-emerald-700">+{{ number_format($marksDisplay, 2) }}</span>
                                        @elseif($isAttempted && $exam->negative_enabled)
                                            <span class="text-rose-700">{{ number_format($marksDisplay, 2) }}</span>
                                        @else
                                            <span class="text-slate-500">0.00</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-5 sm:px-8">
                    <a href="{{ route('student.results.index') }}" class="inline-flex items-center rounded-lg border border-black bg-black px-4 py-2 text-sm font-semibold text-white transition duration-200 hover:bg-slate-800 hover:border-slate-800">
                        Back to My Results
                    </a>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll(".counter");

            counters.forEach((counter) => {
                const target = parseFloat(counter.getAttribute("data-value"));
                const isPercentage = counter.getAttribute("data-type") === "percentage";

                let count = 0;
                const speed = Math.max(20, 1000 / (target || 1));

                const update = () => {
                    if (count < target) {
                        count += target / 60;

                        counter.innerText = isPercentage
                            ? count.toFixed(2) + "%"
                            : Math.ceil(count);

                        setTimeout(update, speed);
                    } else {
                        counter.innerText = isPercentage
                            ? target.toFixed(2) + "%"
                            : target;
                    }
                };

                update();
            });
        });
    </script>
</body>
</html>
