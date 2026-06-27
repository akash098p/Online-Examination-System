<x-app-layout>
    <div class="student-page min-h-screen py-8 sm:py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section class="student-hero overflow-hidden rounded-[28px] border border-white/10">
                <div class="px-6 py-8 sm:px-8 lg:px-10 lg:py-9">
                    <div class="space-y-5 student-reveal student-reveal-delay-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-100 shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] student-shimmer">
                            My Results
                        </div>

                        <div class="space-y-3">
                            <h1 class="student-title max-w-5xl text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                                Track your completed exams, review performance and reopen any result in one place.
                            </h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="student-panel p-5 sm:p-7 student-reveal student-reveal-delay-2">
                <div class="flex flex-col gap-4 border-b border-white/10 pb-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Result History</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">Exam History</h2>
                    </div>

                    <form method="GET" action="{{ route('student.results.index') }}" class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search exam by title or subject" class="w-full rounded-xl border border-white/12 bg-slate-950/70 px-4 py-2.5 text-sm text-gray-100 placeholder:text-gray-400 sm:w-80">
                        <button type="submit" class="student-action-btn student-action-primary">Search</button>
                        <a href="{{ route('student.results.index') }}" class="student-action-btn student-action-muted">Reset</a>
                    </form>
                </div>

                @if($results->count() === 0)
                    <div class="mt-6 rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-6 text-sm text-slate-200">
                        You have not attempted any exams yet.
                    </div>
                @else
                    <div class="mt-6 overflow-x-auto rounded-2xl border border-white/10 bg-white/[0.03]">
                        <table class="w-full text-sm text-left text-slate-200">
                            <thead class="border-b border-white/10 bg-white/[0.04] text-xs uppercase tracking-[0.2em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-4">Exam</th>
                                    <th class="px-4 py-4">Submitted At</th>
                                    <th class="px-4 py-4">Correct</th>
                                    <th class="px-4 py-4">Wrong</th>
                                    <th class="px-4 py-4">Not Answered</th>
                                    <th class="px-4 py-4">Percentage</th>
                                    <th class="px-4 py-4">Status</th>
                                    <th class="px-4 py-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($results as $result)
                                <tr class="border-b border-white/10 transition hover:bg-white/[0.04] last:border-b-0">
                                    <td class="px-4 py-4 font-semibold text-white" data-label="Exam">
                                        {{ $result->exam->title ?? 'Exam unavailable' }}
                                    </td>
                                    <td class="px-4 py-4" data-label="Submitted At">{{ $result->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="px-4 py-4 font-semibold text-emerald-300" data-label="Correct">{{ $result->correct ?? 0 }}</td>
                                    <td class="px-4 py-4 font-semibold text-rose-300" data-label="Wrong">{{ $result->wrong ?? 0 }}</td>
                                    <td class="px-4 py-4 font-semibold text-amber-200" data-label="Not Answered">{{ $result->not_attempted ?? 0 }}</td>
                                    <td class="px-4 py-4" data-label="Percentage">
                                        <span class="counter" data-value="{{ number_format($result->percentage, 2, '.', '') }}" data-type="percentage">0%</span>
                                    </td>
                                    <td class="px-4 py-4" data-label="Status"><span class="student-badge {{ $result->status === 'Pass' ? 'student-badge-completed' : 'student-badge-ended' }}">{{ $result->status }}</span></td>
                                    <td class="px-4 py-4" data-label="Action">
                                        @if($result->exam)
                                            <a href="{{ route('student.results.show', $result->id) }}" class="student-action-btn student-action-secondary">View Result</a>
                                        @else
                                            <span class="student-action-btn student-action-muted opacity-70 cursor-not-allowed">Unavailable</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>

    <style>
        .student-page { position: relative; }
        .student-hero, .student-panel { position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.18); background: rgba(8,10,34,0.66); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); box-shadow: inset 0 1px 0 rgba(255,255,255,0.2), 0 20px 50px rgba(2,6,23,0.32); border-radius: 24px; }
        .student-hero::before, .student-panel::before { content: ""; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.18), transparent 34%); pointer-events: none; }
        .student-hero::after { content: ""; position: absolute; inset: 0; background: linear-gradient(120deg, transparent 20%, rgba(255,255,255,0.16) 40%, transparent 58%); transform: translateX(-120%); animation: studentHeroSweep 8s ease-in-out infinite; pointer-events: none; }
        .student-title { text-shadow: 0 6px 24px rgba(15,23,42,0.34); }
        .student-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 88px; border-radius: 9999px; padding: 0.4rem 0.8rem; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
        .student-badge-completed { background: rgba(5,150,105,0.5); color: rgb(225 255 243); }
        .student-badge-ended { background: rgba(220,38,38,0.5); color: rgb(255 215 215); }
        .student-action-btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 0.85rem; padding: 0.6rem 0.95rem; font-size: 0.8rem; font-weight: 600; transition: all 0.25s ease; }
        .student-action-primary { background: rgb(8 145 178); color: rgb(240 249 255); }
        .student-action-primary:hover { background: rgb(14 165 233); }
        .student-action-secondary { background: rgba(180,83,9,0.55); color: rgb(255 248 220); }
        .student-action-secondary:hover { background: rgba(217,119,6,0.65); }
        .student-action-muted { background: rgba(51,65,85,0.56); color: rgb(226 232 240); }
        .student-action-muted:hover { background: rgba(71,85,105,0.7); }
        .student-reveal { opacity: 0; transform: translateY(22px); animation: studentReveal 0.75s cubic-bezier(0.22,1,0.36,1) forwards; will-change: transform, opacity; }
        .student-reveal-delay-1 { animation-delay: 0.06s; }
        .student-reveal-delay-2 { animation-delay: 0.14s; }
        .student-shimmer { position: relative; overflow: hidden; }
        .student-shimmer::after { content: ""; position: absolute; inset: 0; background: linear-gradient(110deg, transparent 20%, rgba(255,255,255,0.22) 45%, transparent 70%); transform: translateX(-135%); animation: studentBadgeShimmer 6.5s ease-in-out infinite; }
        @keyframes studentReveal { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes studentHeroSweep { 0%,100% { transform: translateX(-120%); } 45%,55% { transform: translateX(120%); } }
        @keyframes studentBadgeShimmer { 0%,100% { transform: translateX(-135%); } 48%,60% { transform: translateX(135%); } }
        @media (prefers-reduced-motion: reduce) { .student-hero::after, .student-shimmer::after, .student-reveal { animation: none !important; opacity: 1; transform: none; } }

        @media (max-width: 900px) {
            .student-panel { padding: 1.25rem; }
            .student-panel table { border: 0; }
            .student-panel table thead { display: none; }
            .student-panel tbody tr { display: block; margin: 0 0 1rem; border: 1px solid rgba(255,255,255,0.12); border-radius: 1.4rem; background: rgba(15,23,42,0.72); }
            .student-panel tbody tr td { display: flex; justify-content: space-between; gap: 0.75rem; padding: 0.9rem 1rem; text-align: left; white-space: normal; }
            .student-panel tbody tr td::before { content: attr(data-label); flex: 0 0 35%; color: rgba(148,163,184,0.95); font-size: 0.73rem; text-transform: uppercase; letter-spacing: 0.12em; }
            .student-panel tbody tr td:last-child { padding-top: 0.75rem; }
            .student-panel tbody tr td .student-action-btn { width: 100%; }
            .student-panel tbody tr td .student-badge { width: auto; }
        }
    </style>

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
</x-app-layout>
