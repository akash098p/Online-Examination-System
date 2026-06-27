<x-app-layout>
    @php
        $existingAnalysis = $result->aiAnalysis?->analysis;
        $chatMessages = $result->aiChatMessages->map(fn ($message) => [
            'id' => $message->id,
            'role' => $message->role,
            'message' => $message->message,
        ])->values();
    @endphp
    <div class="student-page min-h-screen py-8 sm:py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section class="student-hero overflow-hidden rounded-[28px] border border-white/10">
                <div class="px-6 py-8 sm:px-8 lg:px-10 lg:py-9">
                    <div class="space-y-5 student-reveal student-reveal-delay-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-100 shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] student-shimmer">
                            Result Detail
                        </div>

                        <div class="space-y-3">
                            <h1 class="student-title max-w-5xl text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                                Review your performance, compare responses and understand every question clearly.
                            </h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="student-panel p-5 sm:p-7 student-reveal student-reveal-delay-2">
                <div class="result-summary-grid grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="result-summary-card result-summary-card-exam">
                        <p class="result-summary-label">Exam</p>
                        <p class="result-summary-value">{{ $exam->title }}</p>
                    </div>
                    <div class="result-summary-card">
                        <p class="result-summary-label">Total Questions</p>
                        <p class="result-summary-value">{{ $totalQuestions }}</p>
                    </div>
                    <div class="result-summary-card">
                        <p class="result-summary-label">Obtained Marks</p>
                        <p class="result-summary-value">{{ $result->obtained_marks }} / {{ $result->total_marks }}</p>
                    </div>
                    <div class="result-summary-card">
                        <p class="result-summary-label">Percentage</p>
                        <p class="result-summary-value counter" data-value="{{ number_format($result->percentage, 2, '.', '') }}" data-type="percentage">0%</p>
                    </div>
                </div>

                <div class="result-performance-grid mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="result-performance-card result-performance-attempted result-performance-card-attempted">
                        <p class="result-performance-label">Attempted</p>
                        <p class="result-performance-value">{{ $attempted }}</p>
                    </div>
                    <div class="result-performance-card result-performance-correct result-performance-card-correct">
                        <p class="result-performance-label">Correct</p>
                        <p class="result-performance-value">{{ $correct }}</p>
                    </div>
                    <div class="result-performance-card result-performance-wrong result-performance-card-wrong">
                        <p class="result-performance-label">Wrong</p>
                        <p class="result-performance-value">{{ $wrong }}</p>
                    </div>
                    <div class="result-performance-card result-performance-empty result-performance-card-empty">
                        <p class="result-performance-label">Not Answered</p>
                        <p class="result-performance-value">{{ $notAnswered }}</p>
                    </div>
                </div>
            </section>

            <div class="grid gap-6">
                <section class="student-panel p-5 sm:p-7 student-reveal student-reveal-delay-2">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">AI Result Analysis</p>
                            <h2 class="mt-2 text-2xl font-semibold text-white">Strengths, weak spots, and next steps</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="generateAnalysisBtn" class="student-action-btn student-action-primary">
                                {{ $existingAnalysis ? 'Refresh AI Analysis' : 'Generate AI Analysis' }}
                            </button>
                            <button
                                type="button"
                                id="toggleAnalysisOverviewBtn"
                                class="student-action-btn student-action-muted analysis-toggle-btn {{ $existingAnalysis ? '' : 'hidden' }}"
                                aria-expanded="true"
                                aria-label="Hide overview"
                                title="Hide overview"
                            >
                                <svg id="toggleAnalysisOverviewIcon" viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                    <path d="M5 12.5L10 7.5L15 12.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <p id="analysisStatus" class="mt-3 text-sm text-slate-300">
                        {{ $existingAnalysis ? 'Cached AI analysis loaded for this result.' : 'Generate a detailed analysis for this exam result.' }}
                    </p>

                    <div id="analysisContent" class="mt-6 space-y-4"></div>
                    <div id="analysisEmpty" class="rounded-2xl border border-dashed border-white/12 bg-white/[0.03] px-5 py-6 text-sm text-slate-300 {{ $existingAnalysis ? 'hidden' : '' }}">
                        No AI analysis yet for this result.
                    </div>
                </section>

                <section class="student-panel p-5 sm:p-7 student-reveal student-reveal-delay-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">AI Doubt Chat</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">Ask why an answer was wrong</h2>
                        <p class="mt-2 text-sm text-slate-300">
                            The AI uses your question, your answer, and the correct answer as context before replying.
                        </p>
                    </div>

                    <div class="mt-6 analysis-chat-shell">
                        <div id="aiChatMessages" class="flex max-h-[24rem] flex-col gap-3 overflow-y-auto pr-1"></div>
                        <div id="aiChatEmpty" class="analysis-chat-empty rounded-2xl border border-dashed border-white/12 bg-white/[0.03] px-5 py-6 text-sm text-slate-300 {{ $chatMessages->count() ? 'hidden' : '' }}">
                            Start with a question like “Why was question 3 wrong?” or “Explain the matrix concept used here.”
                        </div>
                    </div>

                    <form id="aiChatForm" class="mt-4 space-y-3">
                        <textarea id="aiChatInput" rows="4" class="w-full rounded-2xl border border-white/12 bg-slate-950/45 px-4 py-3 text-white" placeholder="Ask about a question, concept, or answer..."></textarea>
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="submit" class="student-action-btn student-action-primary">Send to AI</button>
                            <span id="chatStatus" class="text-sm text-slate-300">AI is ready.</span>
                        </div>
                    </form>
                </section>
            </div>

            <section class="student-panel p-5 sm:p-7 student-reveal student-reveal-delay-2">
                <div class="border-b border-white/10 pb-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Question Review</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white">Answer Breakdown</h2>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach($exam->questions as $index => $question)
                        @php
                            $response = $responses[$question->id] ?? null;
                            $selectedOptionId = $response?->option_id;
                            $correctOptionId = $question->options->where('is_correct', 1)->first()?->id;
                        @endphp

                        @php
                            $questionState = !$selectedOptionId
                                ? 'not_answered'
                                : ($selectedOptionId === $correctOptionId ? 'correct' : 'wrong');
                        @endphp

                        <article class="result-question-card {{ $questionState === 'correct' ? 'result-question-card-correct' : ($questionState === 'wrong' ? 'result-question-card-wrong' : 'result-question-card-empty') }}">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <h3 class="text-lg font-semibold leading-7 text-white">Q{{ $index + 1 }}. {{ $question->question_text }}</h3>
                                <span class="result-review-pill {{ $questionState === 'correct' ? 'result-review-pill-correct' : ($questionState === 'wrong' ? 'result-review-pill-wrong' : 'result-review-pill-empty') }}">
                                    {{ $questionState === 'correct' ? 'Correct' : ($questionState === 'wrong' ? 'Wrong' : 'Not Answered') }}
                                </span>
                            </div>

                            <ul class="mt-4 space-y-2">
                                @foreach($question->options as $option)
                                    @php
                                        $isSelected = $selectedOptionId === $option->id;
                                        $isCorrect = $option->id === $correctOptionId;
                                    @endphp

                                    <li class="result-option {{ $isCorrect ? 'result-option-correct' : '' }} {{ $isSelected && !$isCorrect ? 'result-option-wrong' : '' }} {{ $isSelected ? 'result-option-selected' : '' }}">
                                        <span class="result-option-text">{{ $option->option_text }}</span>
                                        <span class="flex shrink-0 flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em]">
                                            @if($isCorrect)
                                                <span class="result-chip result-chip-correct">Correct Answer</span>
                                            @endif
                                            @if($isSelected)
                                                <span class="result-chip result-chip-selected">Your Answer</span>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>

                <a href="{{ route('student.results.index') }}" class="student-action-btn student-action-muted mt-6 inline-flex">
                    Back to Exam History
                </a>
            </section>
        </div>
    </div>

    <style>
        .student-page { position: relative; }
        .student-hero, .student-panel { position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.18); background: rgba(8,10,34,0.66); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); box-shadow: inset 0 1px 0 rgba(255,255,255,0.2), 0 20px 50px rgba(2,6,23,0.32); border-radius: 24px; }
        .student-hero::before, .student-panel::before { content: ""; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.18), transparent 34%); pointer-events: none; }
        .student-hero::after { content: ""; position: absolute; inset: 0; background: linear-gradient(120deg, transparent 20%, rgba(255,255,255,0.16) 40%, transparent 58%); transform: translateX(-120%); animation: studentHeroSweep 8s ease-in-out infinite; pointer-events: none; }
        .student-title { text-shadow: 0 6px 24px rgba(15,23,42,0.34); }
        .result-summary-card, .result-performance-card, .result-question-card, .result-option { border: 1px solid rgba(255,255,255,0.16); border-radius: 20px; background: linear-gradient(180deg, rgba(255,255,255,0.18), rgba(255,255,255,0.06)), rgba(15,23,42,0.42); box-shadow: inset 0 1px 0 rgba(255,255,255,0.18); }
        .result-summary-card, .result-performance-card, .result-question-card { padding: 1rem; }
        .result-summary-label { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgb(203 213 225); }
        .result-summary-value { margin-top: 0.7rem; color: white; font-size: 1.25rem; font-weight: 600; line-height: 1.4; }
        .result-performance-card { padding: 1.1rem 1rem; }
        .result-performance-label { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; color: rgb(226 232 240); }
        .result-performance-value { margin-top: 0.65rem; font-size: 2rem; font-weight: 700; line-height: 1; }
        .result-performance-attempted { color: rgb(191 219 254); background: linear-gradient(180deg, rgba(59,130,246,0.32), rgba(15,23,42,0.4)); }
        .result-performance-correct { color: rgb(167 243 208); background: linear-gradient(180deg, rgba(16,185,129,0.32), rgba(15,23,42,0.4)); }
        .result-performance-wrong { color: rgb(254 205 211); background: linear-gradient(180deg, rgba(244,63,94,0.32), rgba(15,23,42,0.4)); }
        .result-performance-empty { color: rgb(253 230 138); background: linear-gradient(180deg, rgba(245,158,11,0.32), rgba(15,23,42,0.4)); }
        .result-question-card { padding: 1.2rem; background: linear-gradient(180deg, rgba(255,255,255,0.12), rgba(255,255,255,0.05)), rgba(15,23,42,0.42); }
        .result-question-card-correct { border-color: rgba(52,211,153,0.32); box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 14px 30px rgba(5,150,105,0.12); }
        .result-question-card-wrong { border-color: rgba(251,113,133,0.32); box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 14px 30px rgba(190,24,93,0.12); }
        .result-question-card-empty { border-color: rgba(251,191,36,0.32); box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 14px 30px rgba(202,138,4,0.12); }
        .result-review-pill, .result-chip { display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; border: 1px solid transparent; padding: 0.4rem 0.78rem; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; white-space: nowrap; }
        .result-review-pill-correct, .result-chip-correct { background: rgba(16,185,129,0.28); border-color: rgba(110,231,183,0.32); color: rgb(167 243 208); }
        .result-review-pill-wrong { background: rgba(244,63,94,0.28); border-color: rgba(253,164,175,0.36); color: rgb(254 205 211); }
        .result-review-pill-empty { background: rgba(245,158,11,0.24); border-color: rgba(252,211,77,0.36); color: rgb(253 230 138); }
        .result-chip-selected { background: rgba(59,130,246,0.24); border-color: rgba(147,197,253,0.36); color: rgb(191 219 254); }
        .result-option { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1rem 1rem; color: rgb(226 232 240); transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease; }
        .result-option:hover { transform: translateY(-1px); }
        .result-option-text { flex: 1 1 auto; line-height: 1.6; }
        .result-option-selected { border-color: rgba(96,165,250,0.34); }
        .result-option-correct { background: rgba(5,150,105,0.36); border-color: rgba(52,211,153,0.34); }
        .result-option-wrong { background: rgba(220,38,38,0.32); border-color: rgba(251,113,133,0.34); }
        .analysis-chip { display: inline-flex; align-items: center; border-radius: 999px; padding: 0.45rem 0.85rem; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; }
        .analysis-chip-strong { background: rgba(16,185,129,0.28); color: rgb(167 243 208); border: 1px solid rgba(110,231,183,0.32); }
        .analysis-chip-weak { background: rgba(244,63,94,0.28); color: rgb(254 205 211); border: 1px solid rgba(251,113,133,0.32); }
        .analysis-chip-next { background: rgba(59,130,246,0.24); color: rgb(191 219 254); border: 1px solid rgba(147,197,253,0.32); }
        .analysis-layout { display: grid; gap: 1rem; }
        .analysis-list-grid { display: grid; gap: 1rem; }
        .analysis-topic-grid { display: grid; gap: 0.75rem; }
        .analysis-chat-shell { display: grid; gap: 1rem; }
        .analysis-chat-empty { min-height: 5.5rem; display: flex; align-items: center; }
        .analysis-overview-hidden { display: none; }
        .analysis-toggle-btn { padding-inline: 0.8rem; min-width: 2.8rem; }
        .analysis-toggle-btn svg { transition: transform 0.2s ease; }
        .analysis-toggle-btn.is-collapsed svg { transform: rotate(180deg); }
        .chat-message { border: 1px solid rgba(255,255,255,0.14); border-radius: 1.35rem; padding: 0.95rem 1rem; background: rgba(15,23,42,0.42); }
        .chat-message-user { align-self: flex-end; background: rgba(8,145,178,0.24); border-color: rgba(103,232,249,0.24); }
        .chat-message-assistant { align-self: flex-start; background: rgba(255,255,255,0.08); }
        @media (max-width: 640px) {
            .result-option { flex-direction: column; }
            .result-summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                align-items: stretch;
            }
            .result-summary-card-exam {
                grid-column: 1 / -1;
            }
            .result-summary-card,
            .result-performance-card {
                padding: 0.85rem 0.8rem;
                border-radius: 18px;
            }
            .result-summary-label,
            .result-performance-label {
                font-size: 0.62rem;
                letter-spacing: 0.16em;
            }
            .result-summary-value {
                margin-top: 0.55rem;
                font-size: 1rem;
                line-height: 1.35;
            }
            .result-performance-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .result-performance-card-attempted { order: 1; }
            .result-performance-card-empty { order: 2; }
            .result-performance-card-correct { order: 3; }
            .result-performance-card-wrong { order: 4; }
            .result-performance-value {
                margin-top: 0.55rem;
                font-size: 1.7rem;
            }
        }
        .student-action-btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 0.85rem; padding: 0.6rem 0.95rem; font-size: 0.8rem; font-weight: 600; transition: all 0.25s ease; }
        .student-action-muted { background: rgba(51,65,85,0.56); color: rgb(226 232 240); }
        .student-action-muted:hover { background: rgba(71,85,105,0.7); }
        .student-action-primary { background: rgba(6,182,212,0.88); color: rgb(2 6 23); }
        .student-action-primary:hover { background: rgba(34,211,238,1); }
        .student-reveal { opacity: 0; transform: translateY(22px); animation: studentReveal 0.75s cubic-bezier(0.22,1,0.36,1) forwards; will-change: transform, opacity; }
        .student-reveal-delay-1 { animation-delay: 0.06s; }
        .student-reveal-delay-2 { animation-delay: 0.14s; }
        .student-shimmer { position: relative; overflow: hidden; }
        .student-shimmer::after { content: ""; position: absolute; inset: 0; background: linear-gradient(110deg, transparent 20%, rgba(255,255,255,0.22) 45%, transparent 70%); transform: translateX(-135%); animation: studentBadgeShimmer 6.5s ease-in-out infinite; }
        @keyframes studentReveal { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes studentHeroSweep { 0%,100% { transform: translateX(-120%); } 45%,55% { transform: translateX(120%); } }
        @keyframes studentBadgeShimmer { 0%,100% { transform: translateX(-135%); } 48%,60% { transform: translateX(135%); } }
        @media (prefers-reduced-motion: reduce) { .student-hero::after, .student-shimmer::after, .student-reveal { animation: none !important; opacity: 1; transform: none; } }

        /* Skeleton loaders for AI analysis */
        .analysis-skeleton { display: grid; gap: 1rem; }
        .skeleton-line { height: 12px; background: linear-gradient(90deg,#23262b 25%, #2f343b 50%, #23262b 75%); background-size: 200% 100%; border-radius: 8px; animation: skeletonShimmer 1.4s linear infinite; }
        .skeleton-title { height: 18px; width: 40%; }
        .skeleton-paragraph { height: 12px; width: 100%; }
        @keyframes skeletonShimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }

        /* Typing indicator for AI chat */
        .typing-dots { display: inline-flex; align-items: center; gap: 4px; }
        .typing-dots span { display: inline-block; width: 8px; height: 8px; background: rgba(203,213,225,0.32); border-radius: 999px; transform: translateY(0); opacity: 0.5; animation: typingDots 0.9s infinite ease-in-out; }
        .typing-dots span:nth-child(2) { animation-delay: 0.12s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.24s; }
        @keyframes typingDots { 0% { transform: translateY(0); opacity: 0.4; } 50% { transform: translateY(-6px); opacity: 1; } 100% { transform: translateY(0); opacity: 0.4; } }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll(".counter");
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const analysisBtn = document.getElementById('generateAnalysisBtn');
            const analysisStatus = document.getElementById('analysisStatus');
            const analysisContent = document.getElementById('analysisContent');
            const analysisEmpty = document.getElementById('analysisEmpty');
            const toggleOverviewBtn = document.getElementById('toggleAnalysisOverviewBtn');
            const chatMessagesEl = document.getElementById('aiChatMessages');
            const chatEmpty = document.getElementById('aiChatEmpty');
            const chatForm = document.getElementById('aiChatForm');
            const chatInput = document.getElementById('aiChatInput');
            const chatStatus = document.getElementById('chatStatus');
            let analysis = @json($existingAnalysis);
            let chatMessages = @json($chatMessages);
            let isOverviewCollapsed = false;

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

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function renderAnalysis() {
                if (!analysis) {
                    analysisContent.innerHTML = '';
                    analysisEmpty.classList.remove('hidden');
                    toggleOverviewBtn?.classList.add('hidden');
                    return;
                }

                analysisEmpty.classList.add('hidden');
                toggleOverviewBtn?.classList.remove('hidden');
                const strengthsList = (analysis.strengths || []).slice(0, 3);
                const weaknessesList = (analysis.weaknesses || []).slice(0, 3);
                const suggestionsList = (analysis.suggestions || []).slice(0, 3);
                const topicList = (analysis.topic_breakdown || [])
                    .slice()
                    .sort((a, b) => String(b?.status || '').toLowerCase().includes('need') - String(a?.status || '').toLowerCase().includes('need'))
                    .slice(0, 4);
                const strengths = strengthsList.map((item) => `<div class="analysis-chip analysis-chip-strong">${escapeHtml(item)}</div>`).join('');
                const weaknesses = weaknessesList.map((item) => `<div class="analysis-chip analysis-chip-weak">${escapeHtml(item)}</div>`).join('');
                const suggestions = suggestionsList.map((item) => `<li class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-slate-200">${escapeHtml(item)}</li>`).join('');
                const topicBreakdown = topicList.map((item) => `
                    <article class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-4 h-full">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm font-semibold text-white">${escapeHtml(item.topic)}</p>
                            <span class="analysis-chip ${String(item.status).toLowerCase().includes('need') ? 'analysis-chip-weak' : 'analysis-chip-strong'}">${escapeHtml(item.status)}</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-300">${escapeHtml(item.detail)}</p>
                    </article>
                `).join('');

                analysisContent.innerHTML = `
                    <div class="analysis-layout">
                        <div class="analysis-overview-card rounded-2xl border border-cyan-300/15 bg-cyan-400/10 px-5 py-4 text-sm leading-6 text-cyan-50">${escapeHtml(analysis.overview)}</div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-300">What went well</p>
                                <div class="mt-4 flex flex-wrap gap-2">${strengths}</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-300">Needs attention</p>
                                <div class="mt-4 flex flex-wrap gap-2">${weaknesses}</div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-300">Priority next steps</p>
                            <ul class="mt-4 space-y-3">${suggestions}</ul>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-300">Most important topics</p>
                            <div class="mt-4 grid gap-3">${topicBreakdown}</div>
                        </div>
                    </div>
                `;

                syncOverviewVisibility();
            }

            function syncOverviewVisibility() {
                if (analysisContent) {
                    analysisContent.classList.toggle('analysis-overview-hidden', isOverviewCollapsed);
                }

                if (toggleOverviewBtn) {
                    toggleOverviewBtn.classList.toggle('is-collapsed', isOverviewCollapsed);
                    toggleOverviewBtn.setAttribute('aria-expanded', String(!isOverviewCollapsed));
                    toggleOverviewBtn.setAttribute('aria-label', isOverviewCollapsed ? 'Show overview' : 'Hide overview');
                    toggleOverviewBtn.title = isOverviewCollapsed ? 'Show overview' : 'Hide overview';
                }
            }

            function renderChat() {
                chatMessagesEl.innerHTML = chatMessages.map((item) => `
                    <article class="chat-message ${item.role === 'user' ? 'chat-message-user' : 'chat-message-assistant'}">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] ${item.role === 'user' ? 'text-cyan-100' : 'text-slate-300'}">${item.role === 'user' ? 'You' : 'AI Tutor'}</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-white">${escapeHtml(item.message)}</p>
                    </article>
                `).join('');

                chatEmpty.classList.toggle('hidden', chatMessages.length > 0);
                chatMessagesEl.scrollTop = chatMessagesEl.scrollHeight;
            }

            async function request(url, body) {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(body),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(data.message || 'Request failed.');
                }

                return data;
            }

            function renderAnalysisSkeleton() {
                analysisEmpty.classList.add('hidden');
                analysisContent.innerHTML = `
                    <div class="analysis-skeleton md:grid md:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-4">
                            <div class="skeleton-line skeleton-title"></div>
                            <div class="mt-3 space-y-2">
                                <div class="skeleton-line skeleton-paragraph"></div>
                                <div class="skeleton-line skeleton-paragraph" style="width:80%"></div>
                                <div class="skeleton-line skeleton-paragraph" style="width:60%"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-4">
                            <div class="skeleton-line skeleton-title"></div>
                            <div class="mt-3 space-y-2">
                                <div class="skeleton-line skeleton-paragraph"></div>
                                <div class="skeleton-line skeleton-paragraph" style="width:75%"></div>
                            </div>
                        </div>
                    </div>
                `;
            }

            analysisBtn?.addEventListener('click', async () => {
                analysisBtn.disabled = true;
                analysisStatus.textContent = 'Generating AI analysis...';
                renderAnalysisSkeleton();

                try {
                    const data = await request(@json(route('student.results.ai_analysis', $result)), {});
                    analysis = data.analysis;
                    analysisStatus.textContent = data.message;
                    renderAnalysis();
                } catch (error) {
                    analysisStatus.textContent = error.message;
                    analysisContent.innerHTML = '';
                    analysisEmpty.classList.remove('hidden');
                } finally {
                    analysisBtn.disabled = false;
                }
            });

            toggleOverviewBtn?.addEventListener('click', () => {
                isOverviewCollapsed = !isOverviewCollapsed;
                syncOverviewVisibility();
            });

            chatForm?.addEventListener('submit', async (event) => {
                event.preventDefault();

                const message = chatInput.value.trim();
                if (!message) {
                    chatStatus.textContent = 'Type a question first.';
                    return;
                }

                chatStatus.textContent = 'Sending to AI...';
                chatInput.disabled = true;

                // append typing indicator
                const typingEl = document.createElement('article');
                typingEl.className = 'chat-message chat-message-assistant';
                typingEl.innerHTML = `
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-300">AI Tutor</p>
                    <p class="mt-2 text-sm leading-6 text-white"><span class="typing-dots"><span></span><span></span><span></span></span></p>
                `;
                chatMessagesEl.appendChild(typingEl);
                chatMessagesEl.scrollTop = chatMessagesEl.scrollHeight;

                try {
                    const data = await request(@json(route('student.results.ai_chat', $result)), { message });
                    // remove typing indicator
                    typingEl.remove();

                    chatMessages = [...chatMessages, ...data.messages];
                    chatInput.value = '';
                    chatStatus.textContent = data.message;
                    renderChat();
                } catch (error) {
                    // remove typing indicator and show error
                    typingEl.remove();
                    chatStatus.textContent = error.message;
                } finally {
                    chatInput.disabled = false;
                    chatInput.focus();
                }
            });

            renderAnalysis();
            renderChat();
        });
    </script>
</x-app-layout>
