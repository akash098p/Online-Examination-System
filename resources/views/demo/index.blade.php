<x-app-layout>
<style>
.demo-page-shell {
    width: 100%;
}

.demo-card {
    position: relative;
    overflow: hidden;
    transform: translateY(22px) scale(0.985);
    opacity: 0;
    animation: demoCardIn 0.7s ease forwards;
    transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
}

.demo-card::before {
    content: "";
    position: absolute;
    inset: -2px;
    background: linear-gradient(120deg, rgba(59,130,246,0.18), rgba(16,185,129,0.14), rgba(59,130,246,0.18));
    opacity: 0;
    transition: opacity 0.35s ease;
    pointer-events: none;
}

.demo-card:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 18px 40px rgba(2, 132, 199, 0.25);
    border-color: rgba(56, 189, 248, 0.45);
}

.demo-card:hover::before {
    opacity: 1;
}

@keyframes demoCardIn {
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 640px) {
    .demo-page-shell {
        max-width: none;
        padding-left: 0.9rem;
        padding-right: 0.9rem;
        padding-top: 1.25rem;
        padding-bottom: 1.5rem;
    }

    .demo-card {
        padding: 1.1rem;
    }
}
</style>

<div class="demo-page-shell max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Try Demo Test</h1>
        <p class="text-gray-300 mt-2">Choose any demo test below. You can take each test multiple times.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
        @foreach($tests as $index => $test)
            <div class="demo-card rounded-xl border border-white/20 bg-slate-900/70 p-6 shadow-lg"
                 style="animation-delay: {{ 120 * $index }}ms;">
                <h2 class="text-xl font-semibold text-white">{{ $test['title'] }}</h2>
                <p class="text-gray-300 mt-2">{{ $test['description'] }}</p>
                <p class="text-sm text-cyan-300 mt-3">Duration: {{ $test['duration_minutes'] }} minutes</p>

                <a href="{{ route('demo.start', $test['slug']) }}"
                   class="inline-block mt-5 rounded-md bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-500 transition">
                    Start Exam
                </a>
            </div>
        @endforeach
    </div>
</div>
</x-app-layout>
