<x-app-layout>

@php
    $remainingSeconds = max(0, ((int) ($exam->duration_minutes ?? 0)) * 60);
    $proctoringConfig = [
        'enabled' => (bool) $exam->proctoring_enabled,
        'requireCamera' => (bool) $exam->require_camera,
        'requireMicrophone' => (bool) $exam->require_microphone,
        'detectNoFace' => (bool) $exam->detect_no_face,
        'detectMultipleFaces' => (bool) $exam->detect_multiple_faces,
        'detectTalking' => (bool) $exam->detect_talking,
        'maxWarnings' => max(1, (int) ($exam->max_warnings ?? 5)),
        'countdownSeconds' => max(0, (int) ($exam->pre_exam_countdown_seconds ?? 10)),
        'violationStoreUrl' => route('violations.store'),
        'terminatedUrl' => route('exam.terminated'),
        'faceModelUrl' => 'https://justadudewhohacks.github.io/face-api.js/models',
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 py-6 relative z-50">
    <div class="exam-shell">
        <div class="exam-header">
            <div>
                <h2 class="text-2xl font-semibold text-white">{{ $exam->title }}</h2>
                <p class="text-sm text-gray-300">{{ $exam->subject ?? '' }}</p>
                @if($exam->negative_enabled && $exam->negative_marking != 0)
                    <p class="mt-2 text-sm text-amber-200">
                        Each wrong answer deducts <strong>{{ number_format($exam->negative_marking, 2) }}</strong> marks.
                    </p>
                @endif
            </div>

            <div class="exam-actions">
                <div class="mobile-action-stack">
                    <div id="timerBox" class="timer-box">
                        <div class="timer-visual" aria-hidden="true">
                            <svg viewBox="0 0 80 110" class="timer-hourglass" role="presentation">
                                <g class="timer-frame">
                                    <rect x="18" y="6" width="44" height="12" rx="6"></rect>
                                    <rect x="18" y="92" width="44" height="12" rx="6"></rect>
                                    <path d="M24 17 C24 37, 34 37, 40 47 C46 37, 56 37, 56 17"></path>
                                    <path d="M24 93 C24 73, 34 73, 40 63 C46 73, 56 73, 56 93"></path>
                                </g>
                                <g class="timer-glass">
                                    <path d="M27 20 C27 35, 35 38, 40 46 C45 38, 53 35, 53 20 L27 20 Z" class="sand-top"></path>
                                    <path d="M27 90 C27 75, 35 72, 40 64 C45 72, 53 75, 53 90 L27 90 Z" class="sand-bottom"></path>
                                    <rect x="38" y="46" width="4" height="20" rx="2" class="sand-stream"></rect>
                                    <circle cx="40" cy="59" r="2.5" class="sand-dot sand-dot-one"></circle>
                                    <circle cx="36.5" cy="66" r="1.8" class="sand-dot sand-dot-two"></circle>
                                    <circle cx="43.5" cy="69" r="1.5" class="sand-dot sand-dot-three"></circle>
                                </g>
                            </svg>
                        </div>
                        <div class="timer-copy">
                            <div class="text-[11px] uppercase tracking-wide text-gray-300">Time Left</div>
                            <div id="timer" class="text-lg font-mono text-red-300">00:00</div>
                        </div>
                    </div>
                    <button type="button" id="exitBtn" class="btn btn-exit">Exit</button>
                </div>
                <div id="mobileLiveCameraPanel" class="mobile-live-camera-panel hidden" aria-label="Live camera preview">
                    <video id="mobileLiveCameraPreview" class="mobile-live-camera-preview" autoplay muted playsinline></video>
                </div>
            </div>
        </div>

        <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1fr)_320px]">
            <div>
                <div class="w-full bg-gray-700/80 rounded-full h-2.5">
                    <div id="progressBar" class="h-2.5 bg-blue-500 rounded-full transition-all duration-200" style="width: 0%"></div>
                </div>
                <div id="progressText" class="mt-2 text-sm text-gray-300">Question 1 of {{ count($questions) }}</div>
            </div>

            <div class="proctor-summary">
                <div>
                    <p class="proctor-label">Warnings</p>
                    <p class="proctor-value"><span id="warningCount">0</span> / <span id="warningLimit">{{ $proctoringConfig['maxWarnings'] }}</span></p>
                </div>
                <div>
                    <p class="proctor-label">Camera</p>
                    <p id="cameraStatus" class="proctor-status-text">Pending</p>
                </div>
                <div>
                    <p class="proctor-label">Microphone</p>
                    <p id="micStatus" class="proctor-status-text">Pending</p>
                </div>
            </div>
        </div>

        <div id="warningBanner" class="warning-banner hidden" role="status" aria-live="polite"></div>

        <form id="examForm" method="POST" action="{{ route('student.exams.submit', $exam->id) }}" class="mt-5">
            @csrf
            <input type="hidden" name="attempt_id" id="attempt_id" value="{{ session('exam_attempt_'.$exam->id.'.attempt_id') }}">
            <input type="hidden" name="question_order" id="question_order">
            <input type="hidden" name="option_order" id="option_order">

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <section class="xl:col-span-8 question-glass">
                    <div id="questionContainer" class="min-h-[240px]"></div>

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                        <button type="button" id="prevBtn" class="btn btn-secondary hidden">Previous</button>

                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="flagBtn" class="btn btn-warning">Flag</button>
                            <button type="button" id="nextBtn" class="btn btn-primary">Next</button>
                            <button type="submit" id="submitBtnPrimary" class="btn btn-success hidden">Submit</button>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="panel-title">Question Palette</h3>
                        <div id="questionPalette" class="palette-grid"></div>
                    </div>
                </section>

                <aside class="xl:col-span-4 side-glass">
                    <div id="liveCameraPanel" class="live-camera-panel hidden">
                        <div class="live-camera-header">
                            <h3 class="panel-title !mb-0">Live Camera</h3>
                            <span class="live-camera-badge">Proctoring</span>
                        </div>
                        <div class="live-camera-shell">
                            <video id="liveCameraPreview" class="live-camera-preview" autoplay muted playsinline></video>
                        </div>
                    </div>


                    <h3 class="panel-title mt-5">Question Navigator</h3>
                    <div class="flex gap-2 flex-wrap">
                        <button type="button" id="navPrevBtn" class="btn btn-secondary text-sm px-3 py-2">Previous</button>
                        <button type="button" id="navNextBtn" class="btn btn-secondary text-sm px-3 py-2">Next</button>
                        <button type="submit" class="btn btn-success text-sm px-3 py-2">Submit Exam</button>
                    </div>

                    <h3 class="panel-title mt-5">Question Overview</h3>
                    <div class="overview-list">
                        <div><span class="dot bg-green-500"></span>Answered <span id="answeredCount" class="v text-green-400">0</span></div>
                        <div><span class="dot bg-gray-500"></span>Not Answered <span id="notAnsweredCount" class="v text-gray-300">0</span></div>
                        <div><span class="dot bg-yellow-400"></span>Flagged <span id="flaggedCount" class="v text-yellow-300">0</span></div>
                        <div><span class="dot bg-blue-500"></span>Current <span id="currentCount" class="v text-blue-300">1</span></div>
                    </div>

                    <h3 class="panel-title mt-5">Proctoring Status</h3>
                    <div class="overview-list">
                        <div><span class="dot bg-cyan-400"></span><span id="proctorModeText">Preparing pre-check</span></div>
                        <div><span class="dot bg-rose-400"></span><span id="lastWarningText">No violations yet</span></div>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</div>

<div id="precheckOverlay" class="precheck-overlay" aria-live="polite">
    <div class="precheck-card">
        <div class="precheck-copy">
            <p class="precheck-kicker">Exam Checkpoint</p>
            <h3 class="precheck-title">Get ready before the timer starts</h3>
            <p class="precheck-text">
                Review the instructions below, allow any required permissions, and make sure you are ready before starting the exam.
            </p>
        </div>

        <div class="precheck-grid">
            <section class="precheck-panel">
                <h4 class="precheck-section-title">Instructions</h4>
                <ul class="precheck-list">
                    <li>Sit in a well-lit place and keep your face clearly visible.</li>
                    <li>Stay on this exam screen and remain in fullscreen mode after the exam begins.</li>
                    <li>Warnings are shared across tab switches, fullscreen exit, camera, and microphone checks.</li>
                    <li>This exam allows up to <strong>{{ $proctoringConfig['maxWarnings'] }}</strong> warnings before termination.</li>
                </ul>

                <div class="precheck-meta-grid">
                    <div class="precheck-meta-card">
                        <span class="precheck-meta-label">Camera</span>
                        <strong id="precheckCameraRequirement">{{ $proctoringConfig['requireCamera'] ? 'Required' : 'Optional' }}</strong>
                    </div>
                    <div class="precheck-meta-card">
                        <span class="precheck-meta-label">Microphone</span>
                        <strong id="precheckMicRequirement">{{ $proctoringConfig['requireMicrophone'] ? 'Required' : 'Optional' }}</strong>
                    </div>
                    <div class="precheck-meta-card">
                        <span class="precheck-meta-label">Countdown</span>
                        <strong><span id="countdownValue">{{ $proctoringConfig['countdownSeconds'] }}</span>s</strong>
                    </div>
                </div>
            </section>

            <section class="precheck-panel">
                <h4 class="precheck-section-title">Device Check</h4>
                <div class="preview-shell">
                    <video id="cameraPreview" class="camera-preview" autoplay muted playsinline></video>
                    <div id="previewFallback" class="preview-fallback">Camera preview will appear here</div>
                </div>

                <div class="status-stack">
                    <div class="status-line">
                        <span>Camera status</span>
                        <strong id="precheckCameraStatus">Pending</strong>
                    </div>
                    <div class="status-line">
                        <span>Microphone status</span>
                        <strong id="precheckMicStatus">Pending</strong>
                    </div>
                    <div class="status-line">
                        <span>Face check</span>
                        <strong id="precheckFaceStatus">Waiting</strong>
                    </div>
                </div>

                <div id="precheckError" class="precheck-error hidden"></div>

                <div class="precheck-actions">
                    <button type="button" id="retryDevicesBtn" class="btn btn-secondary">Retry Devices</button>
                    <button type="button" id="beginExamBtn" class="btn btn-success" disabled>Begin Exam</button>
                </div>
            </section>
        </div>
    </div>
</div>

<canvas id="violationCanvas" class="hidden"></canvas>
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<style>
.exam-shell {
    border: 1px solid rgba(255, 255, 255, 0.18);
    background: rgba(8, 12, 28, 0.58);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.34);
}

.exam-header,
.exam-actions,
.proctor-summary,
.precheck-meta-grid,
.precheck-actions,
.status-line {
    display: flex;
    align-items: center;
}

.exam-header,
.proctor-summary,
.precheck-grid {
    gap: 1rem;
}

.exam-header {
    justify-content: space-between;
    flex-wrap: wrap;
}

.exam-actions {
    gap: 0.75rem;
}

.mobile-action-stack {
    display: contents;
}

.timer-box {
    min-width: 138px;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.7));
    border-radius: 14px;
    padding: 0.42rem 0.62rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.55rem;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.05),
        0 10px 24px rgba(2, 6, 23, 0.28);
}

.timer-copy {
    text-align: right;
    display: grid;
    gap: 0.1rem;
}

.timer-visual {
    width: 32px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.timer-visual::before {
    content: '';
    position: absolute;
    inset: 2px;
    border-radius: 9999px;
    background: radial-gradient(circle, rgba(251, 191, 36, 0.28), rgba(251, 191, 36, 0));
    filter: blur(3px);
    opacity: 0.18;
    pointer-events: none;
    animation: hourglass-aura 7.2s ease-in-out infinite;
}

.timer-visual::after {
    content: '';
    position: absolute;
    inset: -5px;
    border-left: 2px solid rgba(251, 191, 36, 0.38);
    border-right: 2px solid rgba(251, 191, 36, 0.38);
    border-top: 2px solid transparent;
    border-bottom: 2px solid transparent;
    border-radius: 9999px;
    opacity: 0.12;
    pointer-events: none;
    transform: scale(0.9) rotate(0deg);
    animation: hourglass-orbit 7.2s ease-in-out infinite;
}

.timer-hourglass {
    display: block;
    width: 100%;
    height: auto;
    overflow: visible;
    filter: drop-shadow(0 3px 8px rgba(245, 158, 11, 0.22));
    transform-origin: 50% 50%;
    backface-visibility: hidden;
    transform-style: preserve-3d;
    will-change: transform;
    animation: hourglass-flip 7.2s cubic-bezier(0.65, 0, 0.35, 1) infinite;
}

.timer-frame rect,
.timer-frame path {
    fill: none;
    stroke: #9a5c15;
    stroke-width: 4.2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.timer-frame rect {
    fill: #d2a464;
}

.sand-top,
.sand-bottom,
.sand-stream,
.sand-dot {
    fill: #ffb347;
}

#timer {
    font-size: 1rem;
    line-height: 1;
    letter-spacing: 0.03em;
    text-shadow: 0 0 14px rgba(248, 113, 113, 0.16);
}

.timer-copy .text-\[11px\] {
    font-size: 0.62rem;
    letter-spacing: 0.11em;
}

.sand-top {
    transform-origin: 50% 20%;
    animation: sand-top-flow 7.2s linear infinite;
}

.sand-bottom {
    transform-origin: 50% 90%;
    animation: sand-bottom-fill 7.2s linear infinite;
}

.sand-stream {
    transform-origin: 50% 46px;
    animation: sand-stream-flow 7.2s linear infinite;
}

.sand-dot-one { animation: sand-dot-fall 1.6s linear infinite; }
.sand-dot-two { animation: sand-dot-fall 1.6s linear infinite 0.28s; }
.sand-dot-three { animation: sand-dot-fall 1.6s linear infinite 0.56s; }

.timer-box.timer-warning {
    border-color: rgba(251, 191, 36, 0.55);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.05),
        0 0 0 1px rgba(251, 191, 36, 0.1),
        0 10px 24px rgba(251, 191, 36, 0.12);
}

.timer-box.timer-danger {
    border-color: rgba(248, 113, 113, 0.72);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.05),
        0 0 0 1px rgba(248, 113, 113, 0.16),
        0 10px 24px rgba(248, 113, 113, 0.16);
}

.timer-box.timer-danger .timer-hourglass {
    animation: hourglass-flip 5.8s cubic-bezier(0.65, 0, 0.35, 1) infinite, danger-pulse 1.2s ease-in-out infinite;
}

.timer-box.timer-danger .timer-visual::before {
    animation: hourglass-aura-danger 5.8s ease-in-out infinite;
}

.timer-box.timer-danger .timer-visual::after {
    animation: hourglass-orbit-danger 5.8s ease-in-out infinite;
}

.question-glass {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.question-glass,
.side-glass,
.precheck-panel,
.proctor-summary {
    border: 1px solid rgba(255, 255, 255, 0.16);
    background: rgba(3, 7, 18, 0.42);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 14px;
}

.question-glass,
.side-glass,
.precheck-panel {
    padding: 1rem;
}

.side-glass {
    position: sticky;
    top: 16px;
    height: fit-content;
}

.proctor-summary {
    justify-content: space-between;
    padding: 0.75rem 1rem;
}

.proctor-label,
.precheck-kicker,
.precheck-meta-label {
    font-size: 0.72rem;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: #93c5fd;
}

.proctor-value {
    color: white;
    font-size: 1.05rem;
    font-weight: 700;
}

.proctor-status-text {
    color: #e2e8f0;
    font-size: 0.92rem;
}

.panel-title {
    color: #f3f4f6;
    font-size: 0.95rem;
    font-weight: 700;
    margin-bottom: 0.6rem;
}

.palette-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 0.45rem;
}

.palette-btn {
    border-radius: 8px;
    height: 36px;
    font-size: 13px;
    font-weight: 700;
    border: 1px solid transparent;
    transition: transform 0.15s ease, filter 0.15s ease;
}

.palette-btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.07);
}

.palette-current { background: #2563eb; color: #fff; }
.palette-answered { background: #16a34a; color: #fff; }
.palette-flagged { background: #facc15; color: #111827; }
.palette-pending { background: #4b5563; color: #fff; }

.overview-list {
    color: #d1d5db;
    font-size: 0.88rem;
    display: grid;
    gap: 0.35rem;
}

.overview-list .v {
    font-weight: 700;
    margin-left: 0.35rem;
}

.dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 9999px;
    margin-right: 0.45rem;
    vertical-align: middle;
}

.option-item {
    cursor: pointer;
    user-select: none;
    transition: background-color 0.15s ease, border-color 0.15s ease;
}

input[type="radio"] {
    appearance: auto !important;
    -webkit-appearance: radio !important;
    accent-color: #22c55e;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.btn {
    border-radius: 8px;
    padding: 0.48rem 0.95rem;
    font-size: 0.92rem;
}

.btn:hover { filter: brightness(1.08); }
.btn:disabled { cursor: not-allowed; opacity: 0.55; }
.btn-primary { background: #2563eb; color: #fff; }
.btn-secondary { background: #374151; color: #fff; }
.btn-warning { background: #facc15; color: #111827; }
.btn-success { background: #16a34a; color: #fff; }
.btn-exit { background: #92400e; color: #fff; }

.warning-banner {
    margin-top: 1rem;
    border: 1px solid rgba(248, 113, 113, 0.4);
    background: rgba(127, 29, 29, 0.38);
    color: #fee2e2;
    border-radius: 12px;
    padding: 0.8rem 1rem;
}

.live-camera-panel {
    margin-bottom: 1rem;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.04);
    padding: 0.85rem;
}

.live-camera-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.live-camera-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    padding: 0.25rem 0.55rem;
    font-size: 0.68rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #bae6fd;
    background: rgba(14, 165, 233, 0.15);
    border: 1px solid rgba(125, 211, 252, 0.18);
}

.live-camera-shell {
    overflow: hidden;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: #020617;
    aspect-ratio: 16 / 10;
}

.live-camera-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.mobile-live-camera-panel {
    display: none !important;
    overflow: hidden;
    width: 160px;
    min-width: 160px;
    border-radius: 14px;
    border: 1px solid rgba(125, 211, 252, 0.22);
    background:
        linear-gradient(180deg, rgba(14, 165, 233, 0.12), rgba(15, 23, 42, 0.78)),
        rgba(2, 6, 23, 0.95);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.08),
        0 10px 24px rgba(2, 6, 23, 0.24);
    aspect-ratio: 16 / 10;
}

.mobile-live-camera-panel.is-active {
    display: none !important;
}

.mobile-live-camera-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.precheck-overlay {
    position: fixed;
    inset: 0;
    z-index: 70;
    display: grid;
    place-items: center;
    padding: 1rem;
    background: rgba(2, 6, 23, 0.84);
    backdrop-filter: blur(10px);
    overflow-y: auto;
}

.precheck-card {
    width: min(100%, 1040px);
    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, 0.16);
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.88));
    padding: 1.25rem;
    box-shadow: 0 32px 80px rgba(0, 0, 0, 0.45);
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
}

.precheck-title {
    margin-top: 0.4rem;
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
}

.precheck-text {
    margin-top: 0.7rem;
    color: #cbd5e1;
    max-width: 52rem;
}

.precheck-grid {
    display: grid;
    margin-top: 1.25rem;
}

.precheck-panel {
    min-height: 100%;
}

.precheck-section-title {
    color: white;
    font-size: 1rem;
    font-weight: 700;
}

.precheck-list {
    margin-top: 0.9rem;
    padding-left: 1rem;
    color: #dbeafe;
    display: grid;
    gap: 0.6rem;
}

.precheck-meta-grid {
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
}

.precheck-meta-card {
    flex: 1 1 140px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.05);
    padding: 0.75rem;
    color: white;
}

.preview-shell {
    position: relative;
    margin-top: 0.9rem;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: #020617;
    aspect-ratio: 16 / 10;
}

.camera-preview,
.preview-fallback {
    width: 100%;
    height: 100%;
}

.camera-preview {
    object-fit: cover;
    display: none;
}

.camera-preview.is-visible {
    display: block;
}

.preview-fallback {
    display: grid;
    place-items: center;
    color: #94a3b8;
    font-size: 0.95rem;
}

.preview-fallback.hidden {
    display: none;
}

.status-stack {
    margin-top: 1rem;
    display: grid;
    gap: 0.75rem;
}

.status-line {
    justify-content: space-between;
    gap: 1rem;
    color: #e2e8f0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    padding-bottom: 0.6rem;
}

.precheck-error {
    margin-top: 1rem;
    border-radius: 12px;
    border: 1px solid rgba(248, 113, 113, 0.35);
    background: rgba(127, 29, 29, 0.35);
    color: #fecaca;
    padding: 0.75rem 0.9rem;
}

.hidden {
    display: none !important;
}

.precheck-actions {
    justify-content: space-between;
    gap: 0.75rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

@keyframes hourglass-flip {
    0%, 40% { transform: rotate(0deg) scale(1); }
    50% { transform: rotate(180deg) scale(1.04); }
    60%, 90% { transform: rotate(180deg) scale(1); }
    100% { transform: rotate(360deg) scale(1); }
}

@keyframes hourglass-aura {
    0%, 40%, 60%, 90%, 100% { opacity: 0.12; transform: scale(0.88); }
    50% { opacity: 0.28; transform: scale(1.04); }
}

@keyframes hourglass-aura-danger {
    0%, 40%, 60%, 90%, 100% { opacity: 0.18; transform: scale(0.9); }
    50% { opacity: 0.34; transform: scale(1.08); }
}

@keyframes hourglass-orbit {
    0%, 40%, 60%, 90%, 100% { opacity: 0.08; transform: scale(0.9) rotate(0deg); }
    50% { opacity: 0.22; transform: scale(1) rotate(180deg); }
}

@keyframes hourglass-orbit-danger {
    0%, 40%, 60%, 90%, 100% { opacity: 0.1; transform: scale(0.92) rotate(0deg); }
    50% { opacity: 0.28; transform: scale(1.02) rotate(180deg); }
}

@keyframes sand-top-flow {
    0%, 8% { transform: scaleY(1); opacity: 1; }
    42%, 48% { transform: scaleY(0.12); opacity: 0.22; }
    50%, 58% { transform: scaleY(1); opacity: 1; }
    92%, 100% { transform: scaleY(0.12); opacity: 0.22; }
}

@keyframes sand-bottom-fill {
    0%, 8% { transform: scaleY(0.18); opacity: 0.5; }
    42%, 48% { transform: scaleY(1); opacity: 1; }
    50%, 58% { transform: scaleY(0.18); opacity: 0.5; }
    92%, 100% { transform: scaleY(1); opacity: 1; }
}

@keyframes sand-stream-flow {
    0%, 8%, 50%, 58%, 100% { transform: scaleY(0); opacity: 0; }
    12%, 42%, 62%, 92% { transform: scaleY(1); opacity: 1; }
}

@keyframes sand-dot-fall {
    0% { transform: translateY(-3px); opacity: 0; }
    20% { opacity: 1; }
    100% { transform: translateY(10px); opacity: 0; }
}

@keyframes danger-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.06); }
}

@media (min-width: 860px) {
    .precheck-grid {
        grid-template-columns: minmax(0, 1fr) minmax(320px, 420px);
    }
}

@media (max-width: 1280px) {
    .side-glass {
        position: static;
    }

    .proctor-summary {
        flex-wrap: wrap;
    }
}

@media (max-width: 640px) {
    .precheck-overlay {
        display: block;
        padding: 0.75rem;
    }

    .precheck-card {
        width: 100%;
        max-height: calc(100vh - 1.5rem);
        border-radius: 20px;
        padding: 1rem;
        margin: 0 auto;
    }

    .precheck-grid {
        display: flex;
        flex-direction: column;
    }

    .precheck-grid .precheck-panel:first-child {
        order: 2;
    }

    .precheck-grid .precheck-panel:last-child {
        order: 1;
    }

    .preview-shell {
        aspect-ratio: 16 / 9;
    }

    .status-line {
        align-items: flex-start;
        flex-direction: column;
        gap: 0.35rem;
    }

    .precheck-meta-card {
        flex: 1 1 100%;
    }

    .exam-header {
        align-items: flex-start;
    }

    .exam-actions {
        width: 100%;
        justify-content: space-between;
        align-items: center;
        gap: 0.55rem;
        flex-wrap: nowrap;
    }

    .mobile-action-stack {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        flex: 1 1 auto;
        min-width: 0;
    }

    .timer-box {
        min-width: 112px;
        padding: 0.36rem 0.55rem;
        gap: 0.45rem;
        flex: 0 1 auto;
        align-self: center;
    }

    .timer-visual {
        width: 28px;
    }

    #timer {
        font-size: 0.92rem;
    }

    .timer-copy .text-\[11px\] {
        font-size: 0.58rem;
    }

    .mobile-live-camera-panel {
        width: clamp(112px, 34vw, 148px);
        min-width: clamp(112px, 34vw, 148px);
        display: none !important;
        flex: 0 0 auto;
    }

    .mobile-live-camera-panel.is-active {
        display: block !important;
    }

    .live-camera-panel {
        display: none !important;
    }

    #exitBtn {
        flex: 0 0 auto;
        padding-left: 0.85rem;
        padding-right: 0.85rem;
        align-self: center;
        min-height: 60px;
    }

    .precheck-title {
        font-size: 1.45rem;
    }

    .precheck-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .precheck-actions .btn {
        width: 100%;
    }
}
</style>

<script>
const questions = @json($questions);
const proctoring = @json($proctoringConfig);
let index = 0;
let answers = {};
let flagged = {};
let visited = {};
let warnings = 0;
let remainingSeconds = Number({{ $remainingSeconds }});
let autosaveTimer = null;
const autosaveInterval = 10000;
let isAutoSubmit = false;
let sessionActive = false;
let hasTerminated = false;
let precheckCountdown = Number(proctoring.countdownSeconds || 0);
let precheckReady = precheckCountdown <= 0;
let countdownInterval = null;
const defaultDocumentTitle = document.title;

const state = {
    stream: null,
    audioContext: null,
    analyser: null,
    micData: null,
    modelsReady: false,
    faceReadyForStart: false,
    precheckCompleted: false,
    violationCooldowns: new Map(),
    audioBaselineLevel: 0,
    audioThreshold: 0.08,
    audioCalibrated: false,
    audioSpeechHits: 0,
    noFaceHits: 0,
    multiFaceHits: 0,
    faceGraceUntil: 0,
};

const els = {
    examForm: document.getElementById('examForm'),
    questionContainer: document.getElementById('questionContainer'),
    progressBar: document.getElementById('progressBar'),
    progressText: document.getElementById('progressText'),
    prevBtn: document.getElementById('prevBtn'),
    nextBtn: document.getElementById('nextBtn'),
    navPrevBtn: document.getElementById('navPrevBtn'),
    navNextBtn: document.getElementById('navNextBtn'),
    flagBtn: document.getElementById('flagBtn'),
    submitBtnPrimary: document.getElementById('submitBtnPrimary'),
    timer: document.getElementById('timer'),
    timerBox: document.getElementById('timerBox'),
    warningCount: document.getElementById('warningCount'),
    warningBanner: document.getElementById('warningBanner'),
    warningLimit: document.getElementById('warningLimit'),
    cameraStatus: document.getElementById('cameraStatus'),
    micStatus: document.getElementById('micStatus'),
    proctorModeText: document.getElementById('proctorModeText'),
    lastWarningText: document.getElementById('lastWarningText'),
    precheckOverlay: document.getElementById('precheckOverlay'),
    precheckCameraStatus: document.getElementById('precheckCameraStatus'),
    precheckMicStatus: document.getElementById('precheckMicStatus'),
    precheckFaceStatus: document.getElementById('precheckFaceStatus'),
    precheckError: document.getElementById('precheckError'),
    cameraPreview: document.getElementById('cameraPreview'),
    liveCameraPanel: document.getElementById('liveCameraPanel'),
    liveCameraPreview: document.getElementById('liveCameraPreview'),
    mobileLiveCameraPanel: document.getElementById('mobileLiveCameraPanel'),
    mobileLiveCameraPreview: document.getElementById('mobileLiveCameraPreview'),
    previewFallback: document.getElementById('previewFallback'),
    retryDevicesBtn: document.getElementById('retryDevicesBtn'),
    beginExamBtn: document.getElementById('beginExamBtn'),
    countdownValue: document.getElementById('countdownValue'),
    violationCanvas: document.getElementById('violationCanvas'),
};

document.getElementById('question_order').value = JSON.stringify(questions.map((question) => question.id));
document.getElementById('option_order').value = JSON.stringify(
    Object.fromEntries(questions.map((question) => [question.id, question.options.map((option) => option.id)]))
);

function syncHiddenAnswer(questionId, optionId) {
    let input = document.querySelector(`input[name="answers[${questionId}]"]`);
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = `answers[${questionId}]`;
        els.examForm.appendChild(input);
    }
    input.value = optionId;
}

function isAnswered(questionId) {
    return answers[questionId] !== undefined && answers[questionId] !== null && answers[questionId] !== '';
}

function getQuestionState(questionId, questionIndex) {
    if (questionIndex === index) return 'current';
    if (flagged[questionId]) return 'flagged';
    if (isAnswered(questionId)) return 'answered';
    return 'pending';
}

function renderPalette() {
    const palette = document.getElementById('questionPalette');
    palette.innerHTML = questions.map((question, questionIndex) => {
        const stateClass = {
            current: 'palette-current',
            flagged: 'palette-flagged',
            answered: 'palette-answered',
            pending: 'palette-pending',
        }[getQuestionState(question.id, questionIndex)];

        return `<button type="button" class="palette-btn ${stateClass}" onclick="goToQuestion(${questionIndex})">${questionIndex + 1}</button>`;
    }).join('');
}

function updateOverview() {
    const answeredCount = questions.filter((question) => isAnswered(question.id)).length;
    const flaggedCount = questions.filter((question) => !!flagged[question.id]).length;

    document.getElementById('answeredCount').innerText = answeredCount;
    document.getElementById('flaggedCount').innerText = flaggedCount;
    document.getElementById('notAnsweredCount').innerText = questions.length - answeredCount;
    document.getElementById('currentCount').innerText = index + 1;
}

function renderQuestion() {
    const question = questions[index];
    visited[question.id] = true;

    els.questionContainer.innerHTML = `
        <h3 class="text-xl font-semibold mb-4 text-white">${index + 1}. ${question.question_text}</h3>
        <div class="space-y-3">
            ${question.options.map((option, optionIndex) => `
                <label class="option-item flex items-center gap-3 p-3 border rounded ${
                    answers[question.id] == option.id ? 'bg-gray-100/20 border-blue-400' : 'border-gray-500/70'
                }">
                    <input
                        type="radio"
                        name="ui_q_${question.id}"
                        data-qid="${question.id}"
                        value="${option.id}"
                        ${answers[question.id] == option.id ? 'checked' : ''}
                    >
                    <span class="text-gray-100">
                        <strong class="mr-2">${String.fromCharCode(65 + optionIndex)})</strong>${option.option_text}
                    </span>
                </label>
            `).join('')}
        </div>
    `;

    updateUI();
}

function updateUI() {
    els.progressText.innerText = `Question ${index + 1} of ${questions.length}`;
    els.progressBar.style.width = `${((index + 1) / questions.length) * 100}%`;

    els.prevBtn.classList.toggle('hidden', index === 0);
    els.nextBtn.classList.toggle('hidden', index === questions.length - 1);
    els.submitBtnPrimary.classList.toggle('hidden', index !== questions.length - 1);

    els.navPrevBtn.disabled = index === 0;
    els.navNextBtn.disabled = index === questions.length - 1;

    const question = questions[index];
    els.flagBtn.innerText = flagged[question.id] ? 'Unflag' : 'Flag';

    renderPalette();
    updateOverview();
}

async function saveToServer() {
    if (hasTerminated) return;

    clearTimeout(autosaveTimer);

    try {
        await fetch("{{ route('student.exams.autosave', $exam->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
            },
            body: JSON.stringify({
                attempt_id: document.getElementById('attempt_id').value,
                answers,
                flagged: Object.keys(flagged).filter((questionId) => flagged[questionId]),
            }),
        });
    } catch {}

    if (sessionActive) {
        autosaveTimer = setTimeout(saveToServer, autosaveInterval);
    }
}

function autoSubmitExam() {
    if (isAutoSubmit || hasTerminated) return;
    isAutoSubmit = true;

    if (typeof els.examForm.requestSubmit === 'function') {
        els.examForm.requestSubmit();
    } else {
        els.examForm.submit();
    }
}

function updateTimer() {
    const safe = Math.max(0, remainingSeconds);
    const hrs = Math.floor(safe / 3600);
    const mins = Math.floor((safe % 3600) / 60);
    const secs = safe % 60;

    els.timer.innerText = hrs > 0
        ? `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`
        : `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

    els.timerBox.classList.toggle('timer-warning', safe <= 300 && safe > 60);
    els.timerBox.classList.toggle('timer-danger', safe <= 60);
}

function startTimerLoop() {
    updateTimer();

    window.setInterval(() => {
        if (!sessionActive || hasTerminated) return;

        if (remainingSeconds <= 0) {
            autoSubmitExam();
            return;
        }

        remainingSeconds -= 1;
        updateTimer();
    }, 1000);
}

function updateWarningUI(message = '') {
    els.warningCount.textContent = warnings;
    if (!message) return;

    els.warningBanner.textContent = message;
    els.warningBanner.classList.remove('hidden');
    els.lastWarningText.textContent = message;
    window.setTimeout(() => {
        els.warningBanner.classList.add('hidden');
    }, 4500);
}

function setCameraStatus(text) {
    els.cameraStatus.textContent = text;
    els.precheckCameraStatus.textContent = text;
}

function setMicStatus(text) {
    els.micStatus.textContent = text;
    els.precheckMicStatus.textContent = text;
}

function setFaceStatus(text) {
    els.precheckFaceStatus.textContent = text;
}

function setPrecheckError(message = '') {
    els.precheckError.textContent = message;
    els.precheckError.classList.toggle('hidden', !message);
}

function updateBeginButtonState() {
    const faceCheckReady = !proctoring.requireCamera
        || (!proctoring.detectNoFace && !proctoring.detectMultipleFaces)
        || state.faceReadyForStart;
    const micCheckReady = !proctoring.detectTalking || state.audioCalibrated;
    const devicesReady = (!proctoring.requireCamera || !!state.stream)
        && (!proctoring.requireMicrophone || !!state.analyser)
        && (!proctoring.detectNoFace && !proctoring.detectMultipleFaces || state.modelsReady || !proctoring.requireCamera)
        && faceCheckReady
        && micCheckReady;

    els.beginExamBtn.disabled = !precheckReady || !devicesReady;
}

function showPreview(stream) {
    els.cameraPreview.srcObject = stream;
    els.cameraPreview.classList.add('is-visible');
    els.previewFallback.classList.add('hidden');

    if (els.liveCameraPreview) {
        els.liveCameraPreview.srcObject = stream;
    }

    if (els.mobileLiveCameraPreview) {
        els.mobileLiveCameraPreview.srcObject = stream;
    }
}

function setLiveCameraVisibility(visible) {
    if (els.liveCameraPanel) {
        els.liveCameraPanel.classList.toggle('hidden', !visible);
    }

    if (els.mobileLiveCameraPanel) {
        els.mobileLiveCameraPanel.classList.toggle('is-active', !!visible);
        els.mobileLiveCameraPanel.classList.toggle('hidden', !visible);
    }
}

async function loadFaceModelsIfNeeded() {
    if (!proctoring.requireCamera || (!proctoring.detectNoFace && !proctoring.detectMultipleFaces)) {
        state.modelsReady = true;
        setFaceStatus('Not required');
        updateBeginButtonState();
        return;
    }

    if (state.modelsReady) {
        setFaceStatus('Ready');
        updateBeginButtonState();
        return;
    }

    if (!window.faceapi) {
        setFaceStatus('Loading library...');
        return;
    }

    setFaceStatus('Loading model...');

    await faceapi.nets.tinyFaceDetector.loadFromUri(proctoring.faceModelUrl);
    state.modelsReady = true;
    setFaceStatus('Ready');
    updateBeginButtonState();
}

function setupAudioAnalyser(stream) {
    if (!proctoring.requireMicrophone && !proctoring.detectTalking) {
        setMicStatus('Optional');
        updateBeginButtonState();
        return;
    }

    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    if (!AudioContextClass) {
        setMicStatus('Unsupported');
        return;
    }

    if (state.audioContext && state.audioContext.state !== 'closed') {
        state.audioContext.close().catch(() => {});
    }

    state.audioContext = new AudioContextClass();
    const source = state.audioContext.createMediaStreamSource(stream);
    state.analyser = state.audioContext.createAnalyser();
    state.analyser.fftSize = 2048;
    state.micData = new Uint8Array(state.analyser.fftSize);
    source.connect(state.analyser);
    state.audioCalibrated = !proctoring.detectTalking;
    state.audioBaselineLevel = 0;
    state.audioThreshold = 0.08;
    state.audioSpeechHits = 0;
    setMicStatus(proctoring.detectTalking ? 'Calibrating...' : 'Ready');
    updateBeginButtonState();
}

async function calibrateMicrophone() {
    if (!proctoring.detectTalking || !state.analyser) {
        state.audioCalibrated = true;
        updateBeginButtonState();
        return;
    }

    setMicStatus('Calibrating...');

    const levels = [];
    for (let sample = 0; sample < 16; sample += 1) {
        levels.push(computeAudioLevel());
        await new Promise((resolve) => window.setTimeout(resolve, 120));
    }

    const baseline = levels.reduce((sum, level) => sum + level, 0) / Math.max(levels.length, 1);
    state.audioBaselineLevel = baseline;
    state.audioThreshold = Math.max(0.055, baseline * 2.8 + 0.018);
    state.audioCalibrated = true;
    state.audioSpeechHits = 0;
    setMicStatus(`Ready (${Math.round(state.audioThreshold * 1000) / 1000})`);
    updateBeginButtonState();
}

async function initializeDevices() {
    setPrecheckError('');
    els.proctorModeText.textContent = 'Running pre-check';
    updateBeginButtonState();

    const needsCamera = proctoring.requireCamera || proctoring.detectNoFace || proctoring.detectMultipleFaces;
    const needsMicrophone = proctoring.requireMicrophone || proctoring.detectTalking;

    if (!needsCamera && !needsMicrophone) {
        setCameraStatus('Not required');
        setMicStatus('Not required');
        setFaceStatus('Not required');
        updateBeginButtonState();
        return;
    }

    try {
        if (state.stream) {
            state.stream.getTracks().forEach((track) => track.stop());
        }

        state.faceReadyForStart = false;
        if (!navigator.mediaDevices?.getUserMedia) {
            throw new Error('This browser does not support camera or microphone access.');
        }

        state.stream = await navigator.mediaDevices.getUserMedia({
            video: needsCamera ? {
                facingMode: 'user',
                width: { ideal: 1280 },
                height: { ideal: 720 },
            } : false,
            audio: needsMicrophone ? {
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true,
                channelCount: 1,
            } : false,
        });

        if (needsCamera) {
            showPreview(state.stream);
            setCameraStatus('Ready');
        } else {
            setCameraStatus('Not required');
        }

        if (needsMicrophone) {
            setupAudioAnalyser(state.stream);
            await calibrateMicrophone();
        } else {
            setMicStatus('Not required');
        }

        await loadFaceModelsIfNeeded();
        await runFaceCheck(true);
        updateBeginButtonState();
    } catch (error) {
        const message = error?.message || 'Permission denied or device unavailable. Please allow the required camera and microphone access.';
        setPrecheckError(message);
        setCameraStatus(needsCamera ? 'Blocked' : 'Not required');
        setMicStatus(needsMicrophone ? 'Blocked' : 'Not required');
    }
}

function computeAudioLevel() {
    if (!state.analyser || !state.micData) return 0;

    state.analyser.getByteTimeDomainData(state.micData);
    let sumSquares = 0;

    for (const sample of state.micData) {
        const normalized = (sample - 128) / 128;
        sumSquares += normalized * normalized;
    }

    return Math.sqrt(sumSquares / state.micData.length);
}

function canRaiseViolation(reasonKey, cooldownMs = 8000) {
    const now = Date.now();
    const lastTime = state.violationCooldowns.get(reasonKey) || 0;

    if (now - lastTime < cooldownMs) {
        return false;
    }

    state.violationCooldowns.set(reasonKey, now);
    return true;
}

async function captureViolationFrame(reason) {
    const canvas = els.violationCanvas;
    const context = canvas.getContext('2d');
    canvas.width = 640;
    canvas.height = 360;

    context.fillStyle = '#020617';
    context.fillRect(0, 0, canvas.width, canvas.height);

    if (state.stream && els.cameraPreview.videoWidth > 0 && els.cameraPreview.videoHeight > 0) {
        context.drawImage(els.cameraPreview, 0, 0, canvas.width, canvas.height);
    }

    context.fillStyle = 'rgba(2, 6, 23, 0.75)';
    context.fillRect(0, canvas.height - 78, canvas.width, 78);
    context.fillStyle = '#f8fafc';
    context.font = 'bold 24px sans-serif';
    context.fillText('Exam Violation', 20, canvas.height - 42);
    context.font = '16px sans-serif';
    context.fillText(reason, 20, canvas.height - 16);

    return canvas.toDataURL('image/jpeg', 0.84);
}

async function storeViolation(reason) {
    try {
        const image = await captureViolationFrame(reason);
        await fetch(proctoring.violationStoreUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
            },
            body: JSON.stringify({
                exam_id: {{ $exam->id }},
                reason,
                image,
            }),
        });
    } catch {}
}

async function terminateExam(message) {
    if (hasTerminated) return;
    hasTerminated = true;
    sessionActive = false;
    updateWarningUI(message);
    await saveToServer();
    stopMediaStream();
    window.location.href = proctoring.terminatedUrl;
}

async function registerViolation(reasonKey, message) {
    if (!sessionActive || hasTerminated || !canRaiseViolation(reasonKey)) return;

    warnings += 1;
    updateWarningUI(`${message} Warning ${warnings}/${proctoring.maxWarnings}.`);
    await storeViolation(message);

    if (warnings >= proctoring.maxWarnings) {
        await terminateExam('Maximum warnings reached. Exam terminated.');
    }
}

async function runFaceCheck(precheckOnly = false) {
    if (!state.modelsReady || !window.faceapi || !state.stream || !els.cameraPreview.videoWidth) {
        return;
    }

    const detections = await faceapi.detectAllFaces(
        els.cameraPreview,
        new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 })
    );

    if (precheckOnly) {
        state.noFaceHits = 0;
        state.multiFaceHits = 0;
        if (!proctoring.requireCamera) {
            setFaceStatus('Not required');
            state.faceReadyForStart = true;
        } else if (detections.length === 1) {
            setFaceStatus('1 face detected');
            state.faceReadyForStart = true;
        } else if (detections.length === 0) {
            setFaceStatus('No face detected');
            state.faceReadyForStart = false;
        } else {
            setFaceStatus(`${detections.length} faces detected`);
            state.faceReadyForStart = false;
        }
        updateBeginButtonState();
        return;
    }

    if (Date.now() < state.faceGraceUntil) {
        return;
    }

    if (detections.length === 1) {
        state.noFaceHits = 0;
        state.multiFaceHits = 0;
        return;
    }

    if (proctoring.detectNoFace && detections.length === 0) {
        state.noFaceHits += 1;
        state.multiFaceHits = 0;

        if (state.noFaceHits >= 2) {
            state.noFaceHits = 0;
            await registerViolation('no-face', 'No face detected');
        }
        return;
    }

    if (proctoring.detectMultipleFaces && detections.length > 1) {
        state.multiFaceHits += 1;
        state.noFaceHits = 0;

        if (state.multiFaceHits >= 2) {
            state.multiFaceHits = 0;
            await registerViolation('multiple-faces', 'Multiple faces detected');
        }
        return;
    }
}

async function runTalkingCheck() {
    if (!proctoring.detectTalking || !state.analyser || !state.audioCalibrated) return;

    const audioLevel = computeAudioLevel();
    if (audioLevel > state.audioThreshold) {
        state.audioSpeechHits += 1;
    } else {
        state.audioSpeechHits = Math.max(0, state.audioSpeechHits - 1);
    }

    if (state.audioSpeechHits >= 2) {
        state.audioSpeechHits = 0;
        await registerViolation('talking', 'Talking detected');
    }
}

function stopMediaStream() {
    if (state.stream) {
        state.stream.getTracks().forEach((track) => track.stop());
        state.stream = null;
    }

    if (state.audioContext && state.audioContext.state !== 'closed') {
        state.audioContext.close().catch(() => {});
    }

    state.audioContext = null;
    state.analyser = null;
    state.micData = null;
    state.audioCalibrated = false;
    state.audioBaselineLevel = 0;
    state.audioThreshold = 0.08;
    state.audioSpeechHits = 0;
    state.noFaceHits = 0;
    state.multiFaceHits = 0;
    state.faceGraceUntil = 0;
    if (els.liveCameraPreview) {
        els.liveCameraPreview.srcObject = null;
    }
    if (els.mobileLiveCameraPreview) {
        els.mobileLiveCameraPreview.srcObject = null;
    }
    setLiveCameraVisibility(false);
}

function startPrecheckCountdown() {
    els.countdownValue.textContent = precheckCountdown;

    if (precheckReady) {
        updateBeginButtonState();
        return;
    }

    countdownInterval = window.setInterval(() => {
        precheckCountdown = Math.max(0, precheckCountdown - 1);
        els.countdownValue.textContent = precheckCountdown;

        if (precheckCountdown === 0) {
            precheckReady = true;
            window.clearInterval(countdownInterval);
            updateBeginButtonState();
        }
    }, 1000);
}

async function beginExamSession() {
    if (els.beginExamBtn.disabled || hasTerminated) return;

    try {
        if (document.documentElement.requestFullscreen) {
            await document.documentElement.requestFullscreen().catch(() => {});
        }

        if (state.audioContext && state.audioContext.state === 'suspended') {
            await state.audioContext.resume().catch(() => {});
        }
    } catch {}

    state.precheckCompleted = true;
    sessionActive = true;
    state.faceGraceUntil = Date.now() + 6000;
    state.noFaceHits = 0;
    state.multiFaceHits = 0;
    els.precheckOverlay.classList.add('hidden');
    els.proctorModeText.textContent = 'Exam is live';
    setLiveCameraVisibility(!!proctoring.requireCamera);
    updateWarningUI('');
    saveToServer();

    window.setInterval(async () => {
        if (!sessionActive || hasTerminated) return;
        await runFaceCheck(false);
        await runTalkingCheck();
    }, 2000);
}

function goToQuestion(questionIndex) {
    if (questionIndex < 0 || questionIndex >= questions.length) return;
    index = questionIndex;
    renderQuestion();
}

window.goToQuestion = goToQuestion;

document.addEventListener('change', (event) => {
    if (event.target.type === 'radio' && event.target.dataset.qid) {
        const questionId = event.target.dataset.qid;
        answers[questionId] = event.target.value;
        syncHiddenAnswer(questionId, event.target.value);
        updateUI();
        if (sessionActive) {
            saveToServer();
        }
    }
});

els.examForm.addEventListener('submit', async function (event) {
    if (isAutoSubmit) return;

    event.preventDefault();
    const unanswered = questions.filter((question) => !answers[question.id]).length;

    const confirmed = unanswered > 0
        ? await appConfirm(`You have ${unanswered} unanswered question(s).\nSubmit anyway?`, {
            title: 'Submit Exam',
            confirmText: 'Submit',
        })
        : await appConfirm('Are you sure you want to submit the exam?', {
            title: 'Submit Exam',
            confirmText: 'Submit',
        });

    if (confirmed) {
        this.submit();
    }
});

document.addEventListener('visibilitychange', async () => {
    if (document.hidden && sessionActive) {
        await registerViolation('tab-switch', 'Tab switch detected');
    }
});

document.addEventListener('fullscreenchange', async () => {
    if (!document.fullscreenElement && sessionActive) {
        await registerViolation('fullscreen-exit', 'Fullscreen mode exited');
    }
});

document.addEventListener('contextmenu', (event) => event.preventDefault());

document.getElementById('nextBtn').onclick = () => goToQuestion(index + 1);
document.getElementById('prevBtn').onclick = () => goToQuestion(index - 1);
els.navNextBtn.onclick = () => goToQuestion(index + 1);
els.navPrevBtn.onclick = () => goToQuestion(index - 1);

els.flagBtn.onclick = () => {
    const question = questions[index];
    if (flagged[question.id]) delete flagged[question.id];
    else flagged[question.id] = true;
    updateUI();
    if (sessionActive) {
        saveToServer();
    }
};

document.getElementById('exitBtn').onclick = async () => {
    const confirmed = await appConfirm('Exit exam? Answers are saved.', {
        title: 'Leave Exam',
        confirmText: 'Exit',
    });

    if (confirmed) {
        stopMediaStream();
        window.location = "{{ route('student.exams.index') }}";
    }
};

els.retryDevicesBtn.addEventListener('click', initializeDevices);
els.beginExamBtn.addEventListener('click', beginExamSession);

window.addEventListener('beforeunload', () => {
    stopMediaStream();
});

fetch("{{ route('student.exams.load_saved', $exam->id) }}")
    .then((response) => response.json())
    .then((data) => {
        answers = data.answers || {};
        Object.entries(answers).forEach(([questionId, optionId]) => syncHiddenAnswer(questionId, optionId));
        flagged = {};
        (data.flagged || []).forEach((questionId) => {
            flagged[questionId] = true;
        });
        renderQuestion();
    })
    .catch(renderQuestion);

startTimerLoop();
updateTimer();
setCameraStatus(proctoring.requireCamera ? 'Pending' : 'Not required');
setMicStatus(proctoring.requireMicrophone ? 'Pending' : 'Not required');
els.warningLimit.textContent = proctoring.maxWarnings;
updateWarningUI('');
startPrecheckCountdown();
initializeDevices();
</script>

</x-app-layout>
