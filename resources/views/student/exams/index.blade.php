<x-app-layout>
    @php
        $student = auth()->user();
    @endphp

    <div class="student-page min-h-screen py-8 sm:py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section class="student-hero overflow-hidden rounded-[28px] border border-white/10">
                <div class="px-6 py-8 sm:px-8 lg:px-10 lg:py-9">
                    <div class="space-y-5 student-reveal student-reveal-delay-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-100 shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] student-shimmer">
                            Exam Workspace
                        </div>

                        <div class="space-y-3">
                            <p class="text-lg font-medium text-amber-100/90 sm:text-xl">
                                Welcome, {{ $student->name }}
                            </p>
                            <h1 class="student-title max-w-5xl text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                                Review available exams, watch live windows and start when your schedule opens.
                            </h1>
                        </div>
                    </div>
                </div>
            </section>

            @if(session('error'))
                <div class="student-alert student-alert-danger student-reveal student-reveal-delay-2">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="student-alert student-alert-success student-reveal student-reveal-delay-2">
                    {{ session('success') }}
                </div>
            @endif

            <section class="student-panel p-5 sm:p-7 student-reveal student-reveal-delay-2">
                <div class="flex flex-col gap-3 border-b border-white/10 pb-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Assigned Exams</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">Available Exams</h2>
                        <p class="mt-2 text-sm text-slate-300">
                            Your semester: {{ $student->semester ?? 'N/A' }} | Your department: {{ $ownDepartment ?? 'N/A' }}
                        </p>
                    </div>
                    <form method="GET" action="{{ route('student.exams.index') }}" class="flex flex-col gap-2 sm:items-end">
                        <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Browse Department</label>
                        <select
                            name="department"
                            class="rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-sm text-white"
                            onchange="this.form.submit()"
                        >
                            @foreach($departments as $department)
                                <option value="{{ $department }}" {{ $selectedDepartment === $department ? 'selected' : '' }}>{{ $department }}</option>
                            @endforeach
                        </select>
                        <div class="text-sm text-slate-200">
                            {{ $exams->count() }} exam{{ $exams->count() === 1 ? '' : 's' }} found
                        </div>
                    </form>
                </div>

                <div class="mt-6 overflow-x-auto rounded-2xl border border-white/10 bg-white/[0.03]">
                    <table class="w-full text-sm text-left text-slate-200">
                        <thead class="border-b border-white/10 bg-white/[0.04] text-xs uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="px-4 py-4">Title</th>
                                <th class="px-4 py-4">Subject</th>
                                <th class="px-4 py-4 text-center">Questions</th>
                                <th class="px-4 py-4 text-center">Duration</th>
                                <th class="px-4 py-4 text-center">Start Time</th>
                                <th class="px-4 py-4 text-center">End Time</th>
                                <th class="px-4 py-4 text-center">Marks</th>
                                <th class="px-4 py-4 text-center">Status</th>
                                <th class="px-4 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($exams as $exam)
                            @php
                                $startTime = \Carbon\Carbon::parse($exam->start_time)->timezone(config('app.timezone'));
                                $endTime = \Carbon\Carbon::parse($exam->end_time)->timezone(config('app.timezone'));
                                $now = \Carbon\Carbon::now()->timezone(config('app.timezone'));

                                $result = \App\Models\Result::where('exam_id', $exam->id)
                                    ->where('user_id', auth()->id())
                                    ->first();

                                $totalQuestions = $exam->questions_count ?? $exam->questions->count();

                                if ($result) {
                                    $badge = 'Completed';
                                    $badgeClass = 'student-badge-completed';
                                } elseif ($now->lt($startTime)) {
                                    $badge = 'Upcoming';
                                    $badgeClass = 'student-badge-upcoming';
                                } elseif ($now->between($startTime, $endTime)) {
                                    $badge = 'Live';
                                    $badgeClass = 'student-badge-live';
                                } else {
                                    $badge = 'Ended';
                                    $badgeClass = 'student-badge-ended';
                                }

                                $totalMarks = $exam->questions->sum(fn($q) => (int) ($q->marks ?? 1));
                                $passPercentage = (float) ($exam->pass_percentage ?? 40);
                                $passingMarks = $totalMarks ? round(($totalMarks * $passPercentage) / 100, 2) : 'N/A';
                                $examDepartments = empty($exam->department) ? null : implode(', ', (array) $exam->department);
                                $examSemesters = empty($exam->semester) ? null : implode(', ', (array) $exam->semester);
                                $canAttempt = empty($exam->department) || in_array($ownDepartment, (array) $exam->department, true);
                            @endphp

                            <tr class="border-b border-white/10 transition hover:bg-white/[0.04] last:border-b-0">
                                <td class="px-4 py-4 font-semibold text-white" data-label="Title">{{ $exam->title }}</td>
                                <td class="px-4 py-4 text-slate-200" data-label="Subject">
                                    <div>{{ $exam->subject }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $examDepartments ?? 'All Departments' }}</div>
                                </td>
                                <td class="px-4 py-4 text-center" data-label="Questions">{{ $totalQuestions }}</td>
                                <td class="px-4 py-4 text-center" data-label="Duration">{{ $exam->duration_minutes }} mins</td>
                                <td class="px-4 py-4 text-center" data-label="Start Time">
                                    <div>{{ $startTime->format('d M Y h:i A') }}</div>
                                    <div class="countdown mt-1 text-xs text-amber-200" data-start="{{ $startTime->timestamp }}"></div>
                                </td>
                                <td class="px-4 py-4 text-center" data-label="End Time">{{ $endTime->format('d M Y h:i A') }}</td>
                                <td class="px-4 py-4 text-center" data-label="Marks">
                                    <div>{{ $totalMarks ?: 'Not Set' }} / Pass: {{ $passingMarks }}</div>
                                    <div class="mt-1 text-xs text-slate-400">
                                        (<span class="counter" data-value="{{ number_format($passPercentage, 2, '.', '') }}" data-type="percentage">0%</span>)
                                    </div>
                                    @if($exam->negative_enabled && $exam->negative_marking != 0)
                                        <div class="mt-1 text-xs text-amber-200">
                                            Negative: -{{ number_format($exam->negative_marking, 2) }} per wrong question
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center" data-label="Status">
                                    <span class="student-badge {{ $badgeClass }}">{{ $badge }}</span>
                                </td>
                                <td class="px-4 py-4 text-center" data-label="Action">
                                    @if($result)
                                        <a href="{{ route('student.exams.result', $exam->id) }}" class="student-action-btn student-action-secondary">View Result</a>
                                    @elseif($badge === 'Live' && $canAttempt)
                                        <a href="{{ route('student.exams.start', $exam->id) }}" class="student-action-btn student-action-primary">Start Exam</a>
                                    @elseif(!$canAttempt)
                                        <span class="student-action-btn student-action-disabled">View Only</span>
                                    @else
                                        <span class="student-action-btn student-action-disabled">Not Available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-slate-200">No available exams.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <style>
        .student-page { position: relative; }
        .student-hero, .student-panel { position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.18); background: rgba(8,10,34,0.66); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); box-shadow: inset 0 1px 0 rgba(255,255,255,0.2), 0 20px 50px rgba(2,6,23,0.32); border-radius: 24px; }
        .student-hero::before, .student-panel::before { content: ""; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.18), transparent 34%); pointer-events: none; }
        .student-hero::after { content: ""; position: absolute; inset: 0; background: linear-gradient(120deg, transparent 20%, rgba(255,255,255,0.16) 40%, transparent 58%); transform: translateX(-120%); animation: studentHeroSweep 8s ease-in-out infinite; pointer-events: none; }
        .student-title { text-shadow: 0 6px 24px rgba(15,23,42,0.34); }
        .student-alert { border-radius: 18px; border: 1px solid rgba(255,255,255,0.16); padding: 0.9rem 1rem; }
        .student-alert-danger { background: rgba(127,29,29,0.32); color: rgb(252 165 165); }
        .student-alert-success { background: rgba(6,95,70,0.32); color: rgb(167 243 208); }
        .student-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 88px; border-radius: 9999px; padding: 0.4rem 0.8rem; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
        .student-badge-completed { background: rgba(5,150,105,0.5); color: rgb(225 255 243); }
        .student-badge-upcoming { background: rgba(217,119,6,0.52); color: rgb(255 245 204); }
        .student-badge-live { background: rgba(220,38,38,0.5); color: rgb(255 215 215); }
        .student-badge-ended { background: rgba(51,65,85,0.56); color: rgb(238 242 248); }
        .student-action-btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 0.85rem; padding: 0.45rem 0.85rem; font-size: 0.78rem; font-weight: 600; transition: all 0.25s ease; }
        .student-action-primary { background: rgb(8 145 178); color: rgb(240 249 255); }
        .student-action-primary:hover { background: rgb(14 165 233); }
        .student-action-secondary { background: rgba(180,83,9,0.55); color: rgb(255 248 220); }
        .student-action-secondary:hover { background: rgba(217,119,6,0.65); }
        .student-action-disabled { background: rgba(71,85,105,0.55); color: rgb(226 232 240); cursor: not-allowed; }
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

    <script>
    setInterval(() => {
        document.querySelectorAll('.countdown').forEach((el) => {
            let startTime = parseInt(el.dataset.start) * 1000;
            let now = Date.now();
            let diff = startTime - now;
            if (diff > 0) {
                let hours = Math.floor(diff / (1000 * 60 * 60));
                let mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                let secs = Math.floor((diff % (1000 * 60)) / 1000);
                el.innerHTML = `Starts in ${hours}h ${mins}m ${secs}s`;
                if (diff < 1000) setTimeout(() => location.reload(), 1200);
            } else {
                el.innerHTML = '';
            }
        });
    }, 1000);
    </script>
</x-app-layout>
