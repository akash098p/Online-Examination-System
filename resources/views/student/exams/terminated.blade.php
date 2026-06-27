<x-app-layout>
    <style>
        .terminated-shell {
            min-height: calc(100vh - 2rem);
            display: grid;
            place-items: center;
            padding: 1.5rem;
        }

        .terminated-card {
            position: relative;
            overflow: hidden;
            width: min(100%, 840px);
            border-radius: 2rem;
            border: 1px solid rgba(251, 113, 133, 0.22);
            background:
                radial-gradient(circle at top, rgba(190, 24, 93, 0.16), transparent 38%),
                linear-gradient(180deg, rgba(15, 23, 42, 0.94), rgba(2, 6, 23, 0.92));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.08),
                0 30px 70px rgba(2, 6, 23, 0.42);
            padding: 2.2rem;
            text-align: center;
        }

        .terminated-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.14), transparent 35%);
            pointer-events: none;
        }

        .terminated-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            border: 1px solid rgba(251, 191, 36, 0.22);
            background: rgba(251, 191, 36, 0.08);
            color: #fbcfe8;
            padding: 0.5rem 0.9rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .terminated-title {
            margin-top: 1.15rem;
            color: white;
            font-size: clamp(2rem, 5vw, 3.1rem);
            font-weight: 700;
            line-height: 1.05;
        }

        .terminated-copy {
            margin: 1rem auto 0;
            max-width: 38rem;
            color: #cbd5e1;
            font-size: 1rem;
            line-height: 1.75;
        }

        .terminated-alert {
            margin: 1.5rem auto 0;
            max-width: 34rem;
            border-radius: 1.2rem;
            border: 1px solid rgba(248, 113, 113, 0.16);
            background: rgba(127, 29, 29, 0.18);
            padding: 0.95rem 1rem;
            color: #fecdd3;
            font-size: 0.95rem;
        }

        .terminated-actions {
            margin-top: 2rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.85rem;
        }

        .terminated-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 180px;
            border-radius: 1rem;
            padding: 0.95rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 700;
            transition: transform 0.2s ease, filter 0.2s ease, box-shadow 0.2s ease;
        }

        .terminated-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.06);
            box-shadow: 0 18px 28px rgba(2, 6, 23, 0.26);
        }

        .terminated-btn-primary {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
        }

        .terminated-btn-secondary {
            background: rgba(51, 65, 85, 0.88);
            color: white;
        }

        @media (max-width: 640px) {
            .terminated-shell {
                min-height: auto;
                padding: 1rem;
            }

            .terminated-card {
                padding: 1.5rem;
                border-radius: 1.5rem;
            }

            .terminated-actions {
                flex-direction: column;
            }

            .terminated-btn {
                width: 100%;
            }
        }
    </style>

    <div class="terminated-shell relative z-50">
        <div class="terminated-card">
            <span class="terminated-badge">Exam Status</span>
            <h1 class="terminated-title">Exam Terminated</h1>
            <p class="terminated-copy">
                Your exam session was ended because the warning limit was reached during proctoring.
            </p>

            <div class="terminated-alert">
                Please review the exam instructions and ensure your face, camera, microphone, and browser focus remain stable before attempting another proctored exam.
            </div>

            <div class="terminated-actions">
                <a href="{{ route('student.exams.index') }}" class="terminated-btn terminated-btn-primary">
                    Back to Exams
                </a>
                <a href="{{ route('student.dashboard') }}" class="terminated-btn terminated-btn-secondary">
                    Student Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
