<x-app-layout>
    @php
        $user = auth()->user();
        $profileFields = collect([
            'name' => ['label' => 'name', 'value' => $user->name],
            'email' => ['label' => 'email', 'value' => $user->email],
            'registration_no' => ['label' => 'registration number', 'value' => $user->registration_no],
            'college_name' => ['label' => 'college name', 'value' => $user->college_name],
            'department' => ['label' => 'department', 'value' => $user->department],
            'semester' => ['label' => 'semester', 'value' => $user->semester],
            'phone' => ['label' => 'phone number', 'value' => $user->phone],
            'sex' => ['label' => 'sex', 'value' => $user->sex],
            'date_of_birth' => ['label' => 'date of birth', 'value' => $user->date_of_birth],
            'profile_photo' => ['label' => 'profile photo', 'value' => $user->profile_photo],
        ]);
        $profileChecks = $profileFields->pluck('value');
        $profileCompletion = (int) round(($profileChecks->filter(fn ($value) => filled($value))->count() / max($profileChecks->count(), 1)) * 100);
        $missingProfileFields = $profileFields
            ->filter(fn ($field) => blank($field['value']))
            ->pluck('label')
            ->values();
    @endphp

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-white">
            {{ __('Student Profile') }}
        </h2>
    </x-slot>

    <div class="profile-page min-h-screen py-8 sm:py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section class="profile-hero overflow-hidden rounded-[28px] border border-white/10">
                <div class="grid gap-6 px-6 py-8 sm:px-8 lg:grid-cols-[1.45fr,0.95fr] lg:items-center lg:px-10 lg:py-9">
                    <div class="space-y-5 profile-reveal profile-reveal-delay-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-100 shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] profile-shimmer">
                            Academic Profile
                        </div>

                        <div class="space-y-3">
                            <h1 class="max-w-2xl text-3xl font-semibold tracking-tight text-white sm:text-4xl hero-title">
                                Manage your academic identity with a clean, professional profile experience.
                            </h1>

                            <div class="sm:hidden">
                                <button
                                    type="button"
                                    id="heroEditBtn"
                                    class="inline-flex items-center justify-center rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400"
                                >
                                    Edit Profile
                                </button>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="profile-stat profile-reveal profile-reveal-delay-2">
                                <span class="profile-stat-label profile-stat-label-gold">Profile completion</span>
                                <div class="profile-progress-ring" style="--profile-progress: {{ $profileCompletion }}%;">
                                    <strong class="profile-stat-value profile-stat-value-ring">{{ $profileCompletion }}%</strong>
                                </div>
                                <p class="profile-stat-note">
                                    @if($missingProfileFields->isEmpty())
                                        Your profile is fully complete.
                                    @else
                                        Add {{ $missingProfileFields->join(', ', ' and ') }} to reach 100%.
                                    @endif
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 sm:col-span-2">
                                <div class="profile-stat profile-reveal profile-reveal-delay-3">
                                    <span class="profile-stat-label profile-stat-label-soft">Account email</span>
                                    <strong class="profile-stat-value profile-stat-value-compact break-all profile-stat-value-highlight">{{ $user->email }}</strong>
                                </div>

                                <div class="profile-stat profile-reveal profile-reveal-delay-4">
                                    <span class="profile-stat-label profile-stat-label-soft">Member since</span>
                                    <strong class="profile-stat-value profile-stat-value-compact profile-stat-value-highlight">{{ $user->created_at->format('d M Y') }}</strong>
                                </div>

                                <div class="profile-stat profile-reveal profile-reveal-delay-3">
                                    <span class="profile-stat-label profile-stat-label-soft">Contact</span>
                                    <strong class="profile-stat-value profile-stat-value-compact profile-stat-value-highlight">{{ $user->phone ?: 'Not set' }}</strong>
                                </div>

                                <div class="profile-stat profile-reveal profile-reveal-delay-4">
                                    <span class="profile-stat-label profile-stat-label-soft">D.O.B</span>
                                    <strong class="profile-stat-value profile-stat-value-compact profile-stat-value-highlight">{{ optional($user->date_of_birth)->format('d M Y') ?: 'Not set' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="profile-sidecard profile-reveal profile-reveal-delay-2">
                        <div class="flex items-start gap-4">
                            <img
                                src="{{ $user->profilePhotoUrl() }}"
                                alt="{{ $user->name }}"
                                class="h-20 w-20 rounded-2xl object-cover ring-4 ring-white/10"
                            >

                            <div class="min-w-0 space-y-1">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Student identity</p>
                                <h2 class="student-identity-name text-2xl font-semibold text-white">{{ $user->name }}</h2>
                                <p class="text-sm text-slate-100">{{ $user->registration_no ?: 'Registration number not added yet' }}</p>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                        
                            <div class="profile-mini-card">
                                <span class="profile-mini-label">College</span>
                                <span class="profile-mini-value">{{ $user->college_name ?: 'Not set' }}</span>
                            </div>
                            <div class="profile-mini-card">
                                <span class="profile-mini-label">Semester</span>
                                <span class="profile-mini-value">{{ $user->semester ?: 'Not set' }}</span>
                            </div>
                            <div class="profile-mini-card">
                                <span class="profile-mini-label">Department</span>
                                <span class="profile-mini-value">{{ $user->department ?: 'Not set' }}</span>
                            </div>
                            <div class="profile-mini-card">
                                <span class="profile-mini-label">Status</span>
                                <span class="profile-mini-value text-emerald-200">Active</span>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <div class="grid gap-6">
                <section class="profile-panel p-5 sm:p-7 profile-reveal profile-reveal-delay-2">
                    @include('profile.partials.update-profile-information-form')
                </section>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="profile-panel p-5 sm:p-7 profile-reveal profile-reveal-delay-3">
                    @include('profile.partials.update-password-form')
                </section>

                <section class="profile-panel profile-panel-danger p-5 sm:p-7 profile-reveal profile-reveal-delay-4">
                    @include('profile.partials.delete-user-form')
                </section>
            </div>
        </div>
    </div>

    <style>
        .profile-page {
            position: relative;
        }

        .profile-hero {
            position: relative;
            background:
                linear-gradient(135deg, rgba(10, 16, 30, 0.78), rgba(15, 23, 42, 0.58)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.04));
            backdrop-filter: blur(12px) saturate(125%);
            -webkit-backdrop-filter: blur(12px) saturate(125%);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.18),
                inset 0 -1px 0 rgba(255, 255, 255, 0.06),
                0 28px 70px rgba(2, 6, 23, 0.45);
        }

        .profile-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 20%, rgba(255, 255, 255, 0.16) 40%, transparent 58%);
            transform: translateX(-120%);
            animation: profileHeroSweep 8s ease-in-out infinite;
            pointer-events: none;
        }

        .profile-panel,
        .profile-sidecard {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.06)),
                rgba(15, 23, 42, 0.70);
            backdrop-filter: blur(10px) saturate(125%);
            -webkit-backdrop-filter: blur(10px) saturate(125%);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.18),
                0 20px 50px rgba(2, 6, 23, 0.38);
            border-radius: 24px;
        }

        .profile-panel::before,
        .profile-sidecard::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.14), transparent 34%);
            pointer-events: none;
        }

        .profile-sidecard {
            padding: 1.35rem;
            display: grid;
            gap: 1rem;
            align-self: stretch;
        }

        .profile-stat,
        .profile-mini-card,
        .profile-tip {
            border: 1px solid rgba(255, 255, 255, 0.16);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.06)),
                rgba(15, 23, 42, 0.42);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            padding: 1rem;
            transition: transform 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease;
        }

        .profile-stat:hover,
        .profile-mini-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.18);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.14),
                0 16px 28px rgba(2, 6, 23, 0.2);
        }

        .profile-stat-label,
        .profile-mini-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgb(162 173 194);
        }

        .profile-stat-value,
        .profile-mini-value,
        .profile-tip-title {
            display: block;
            margin-top: 0.55rem;
            color: white;
            font-weight: 600;
        }

        .profile-stat-value {
            font-size: 1.8rem;
            line-height: 1.1;
        }

        .profile-progress-ring {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 6rem;
            height: 6rem;
            margin-top: 0.7rem;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background:
                radial-gradient(closest-side, rgba(15, 23, 42, 0.96) 74%, transparent 75% 100%),
                conic-gradient(
                    from 220deg,
                    rgba(251, 191, 36, 0.96) 0%,
                    rgba(245, 158, 11, 1) calc(var(--profile-progress, 0%) * 0.6),
                    rgba(253, 224, 71, 0.96) var(--profile-progress, 0%),
                    rgba(255, 255, 255, 0.1) var(--profile-progress, 0%),
                    rgba(255, 255, 255, 0.08) 100%
                );
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.08),
                0 0 0 1px rgba(255, 255, 255, 0.05),
                0 14px 24px rgba(15, 23, 42, 0.22);
        }

        .profile-stat-value-ring {
            margin-top: 0;
            font-size: 1.45rem;
            color: rgb(255 248 220);
            letter-spacing: -0.02em;
            text-shadow: 0 1px 10px rgba(245, 158, 11, 0.18);
        }

        .profile-stat-value-compact {
            font-size: 1rem;
            line-height: 1.45;
            letter-spacing: 0;
            word-break: break-word;
        }

        .hero-title {
            text-wrap: balance;
            text-shadow: 0 6px 24px rgba(15, 23, 42, 0.34);
        }

        .hero-copy {
            max-width: 44rem;
        }

        .profile-stat-label-gold {
            color: rgb(253 224 71);
        }

        .profile-stat-label-soft {
            color: rgb(226 232 240);
        }

        .profile-stat-value-highlight {
            color: rgb(255 248 220);
            text-shadow: 0 4px 18px rgba(245, 158, 11, 0.12);
        }

        .profile-stat-note,
        .profile-tip-copy {
            margin-top: 0.55rem;
            color: rgb(226 232 240);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .profile-panel-danger {
            border-color: rgba(248, 113, 113, 0.28);
            background:
                linear-gradient(180deg, rgba(127, 29, 29, 0.28), rgba(15, 23, 42, 0.68)),
                rgba(15, 23, 42, 0.62);
        }

        .profile-reveal {
            opacity: 0;
            transform: translateY(22px);
            animation: profileReveal 0.75s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            will-change: transform, opacity;
        }

        .profile-reveal-delay-1 {
            animation-delay: 0.06s;
        }

        .profile-reveal-delay-2 {
            animation-delay: 0.14s;
        }

        .profile-reveal-delay-3 {
            animation-delay: 0.22s;
        }

        .profile-reveal-delay-4 {
            animation-delay: 0.3s;
        }

        .profile-shimmer {
            position: relative;
            overflow: hidden;
        }

        .profile-shimmer::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 20%, rgba(255, 255, 255, 0.22) 45%, transparent 70%);
            transform: translateX(-135%);
            animation: profileBadgeShimmer 6.5s ease-in-out infinite;
        }

        @keyframes profileReveal {
            from {
                opacity: 0;
                transform: translateY(22px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes profileHeroSweep {
            0%, 100% {
                transform: translateX(-120%);
            }
            45%, 55% {
                transform: translateX(120%);
            }
        }

        @keyframes profileBadgeShimmer {
            0%, 100% {
                transform: translateX(-135%);
            }
            48%, 60% {
                transform: translateX(135%);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .profile-hero::after,
            .profile-shimmer::after,
            .profile-reveal {
                animation: none !important;
                opacity: 1;
                transform: none;
            }

            .profile-stat,
            .profile-mini-card {
                transition: none;
            }
        }

        @media (max-width: 640px) {
            .profile-sidecard {
                padding: 1.25rem;
            }

            .profile-stat-value {
                font-size: 1.45rem;
            }

            .profile-stat-value-compact {
                font-size: 0.95rem;
            }

            .profile-progress-ring {
                width: 5.25rem;
                height: 5.25rem;
            }

            .profile-stat-value-ring {
                font-size: 1.2rem;
            }

            .student-identity-name {
                font-size: clamp(1.1rem, 5vw, 1.4rem);
                line-height: 1.15;
                white-space: normal;
                overflow-wrap: anywhere;
            }
        }
    </style>
</x-app-layout>
