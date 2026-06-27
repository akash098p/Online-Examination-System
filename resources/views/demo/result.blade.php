<x-app-layout>
@php
    $test = $result['test'];
    $summary = $result['summary'];
@endphp

<style>
@media (max-width: 640px) {
    .demo-result-shell {
        max-width: none;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-top: 1.25rem;
        padding-bottom: 1.5rem;
    }

    .demo-result-summary {
        gap: 0.75rem;
    }

    .demo-result-summary > div {
        padding: 0.85rem;
    }
}
</style>

<div class="demo-result-shell max-w-6xl mx-auto px-4 py-8">
    <div class="rounded-xl border border-white/20 bg-slate-900/70 p-6 mb-6">
        <h1 class="text-2xl font-bold text-white">{{ $test['title'] }} - Result</h1>
    </div>

    <div class="demo-result-summary grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="rounded-lg bg-black/30 border border-white/10 p-4 text-center">
            <p class="text-gray-400 text-sm">Total</p>
            <p class="text-xl font-bold text-white">{{ $summary['total_questions'] }}</p>
        </div>
        <div class="rounded-lg bg-black/30 border border-white/10 p-4 text-center">
            <p class="text-gray-400 text-sm">Correct</p>
            <p class="text-xl font-bold text-green-400">{{ $summary['correct'] }}</p>
        </div>
        <div class="rounded-lg bg-black/30 border border-white/10 p-4 text-center">
            <p class="text-gray-400 text-sm">Wrong</p>
            <p class="text-xl font-bold text-red-400">{{ $summary['wrong'] }}</p>
        </div>
        <div class="rounded-lg bg-black/30 border border-white/10 p-4 text-center">
            <p class="text-gray-400 text-sm">Not Attempted</p>
            <p class="text-xl font-bold text-yellow-300">{{ $summary['not_attempted'] }}</p>
        </div>
        <div class="rounded-lg bg-black/30 border border-white/10 p-4 text-center">
            <p class="text-gray-400 text-sm">Score</p>
            <p class="text-xl font-bold text-cyan-300">{{ $summary['score'] }} / {{ $summary['total_questions'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ number_format($summary['percentage'], 2) }}%</p>
        </div>
    </div>

    <div class="rounded-xl border border-white/15 bg-slate-950/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-black/40 text-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Question</th>
                        <th class="px-4 py-3 text-left">Your Answer</th>
                        <th class="px-4 py-3 text-left">Correct Answer</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result['rows'] as $i => $row)
                        <tr class="border-t border-white/10">
                            <td class="px-4 py-3 text-gray-300">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 text-gray-200">{{ $row['question_text'] }}</td>
                            <td class="px-4 py-3 text-gray-200">
                                {{ $row['selected_text'] ?? 'Not Attempted' }}
                            </td>
                            <td class="px-4 py-3 text-cyan-300">{{ $row['correct_text'] }}</td>
                            <td class="px-4 py-3">
                                @if(!$row['is_attempted'])
                                    <span class="text-yellow-300 font-semibold">Not Attempted</span>
                                @elseif($row['is_correct'])
                                    <span class="text-green-400 font-semibold">Correct</span>
                                @else
                                    <span class="text-red-400 font-semibold">Wrong</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('demo.start', $test['slug']) }}"
           class="rounded-md bg-blue-600 px-5 py-2 font-semibold text-white hover:bg-blue-500 transition">
            Retake This Exam
        </a>
        <a href="{{ route('demo.index') }}"
           class="rounded-md bg-gray-700 px-5 py-2 font-semibold text-white hover:bg-gray-600 transition">
            Try Other Exams
        </a>
        <a href="{{ route('home') }}"
           class="rounded-md bg-emerald-700 px-5 py-2 font-semibold text-white hover:bg-emerald-600 transition">
            Back to Home
        </a>
    </div>
</div>
</x-app-layout>
