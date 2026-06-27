@extends('layouts.admin')

@section('content')
<div class="profile-request-page">
    <section class="profile-request-panel p-5 sm:p-7">
        <div class="flex flex-col gap-3 border-b border-white/10 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-100/80">Notifications</p>
                <h1 class="mt-2 text-2xl font-semibold text-white">Academic Change Requests</h1>
                <p class="mt-2 text-sm leading-6 text-slate-300">
                    Review pending department and semester changes requested by students.
                </p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="profile-request-back-btn">
                Back to Dashboard
            </a>
        </div>

        <div class="mt-6 grid gap-4">
            @forelse($requests as $requestItem)
                <article class="profile-request-card">
                    <div class="flex items-start gap-4">
                        <img
                            src="{{ $requestItem->user?->profilePhotoUrl() ?? asset('images/default-male.png') }}"
                            alt="{{ $requestItem->user?->name ?? 'Student' }}"
                            class="h-14 w-14 rounded-2xl object-cover ring-2 ring-white/10"
                        >

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <h2 class="text-lg font-semibold text-white">
                                    {{ $requestItem->user?->name ?? 'Unknown Student' }}
                                </h2>
                                <span class="profile-request-pill">Pending</span>
                            </div>

                            <p class="mt-1 text-sm text-slate-300">
                                {{ $requestItem->user?->registration_no ?? 'N/A' }} | {{ $requestItem->user?->email ?? 'N/A' }}
                            </p>

                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                <div class="profile-request-mini">
                                    <span class="profile-request-label">Department</span>
                                    <p class="profile-request-value">
                                        {{ $requestItem->current_department ?: 'N/A' }}
                                        <span class="text-cyan-200">-></span>
                                        {{ $requestItem->requested_department ?: 'N/A' }}
                                    </p>
                                </div>
                                <div class="profile-request-mini">
                                    <span class="profile-request-label">Semester</span>
                                    <p class="profile-request-value">
                                        {{ $requestItem->current_semester ?: 'N/A' }}
                                        <span class="text-cyan-200">-></span>
                                        {{ $requestItem->requested_semester ?: 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2 sm:justify-end">
                        <form method="POST" action="{{ route('admin.profile_requests.approve', $requestItem) }}">
                            @csrf
                            <button type="submit" class="profile-request-action profile-request-approve">
                                Approve
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.profile_requests.reject', $requestItem) }}">
                            @csrf
                            <button type="submit" class="profile-request-action profile-request-reject">
                                Reject
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-white/12 bg-white/[0.04] px-5 py-8 text-center text-sm text-slate-300">
                    No pending academic change requests.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    </section>
</div>

<style>
    .profile-request-page {
        position: relative;
    }

    .profile-request-panel,
    .profile-request-card {
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

    .profile-request-card {
        padding: 1.1rem;
    }

    .profile-request-back-btn,
    .profile-request-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.9rem;
        padding: 0.7rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
        transition: filter 0.2s ease;
    }

    .profile-request-back-btn {
        background: rgba(71, 85, 105, 0.9);
    }

    .profile-request-action:hover,
    .profile-request-back-btn:hover {
        filter: brightness(1.08);
    }

    .profile-request-pill {
        display: inline-flex;
        width: fit-content;
        border-radius: 999px;
        border: 1px solid rgba(250, 204, 21, 0.24);
        background: rgba(250, 204, 21, 0.12);
        padding: 0.35rem 0.8rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(253 224 71);
    }

    .profile-request-mini {
        border-radius: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(8, 10, 34, 0.34);
        padding: 0.85rem 1rem;
    }

    .profile-request-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgb(148 163 184);
    }

    .profile-request-value {
        margin-top: 0.55rem;
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.6;
        color: white;
    }

    .profile-request-approve {
        background: rgba(5, 150, 105, 0.9);
    }

    .profile-request-reject {
        background: rgba(220, 38, 38, 0.88);
    }
</style>
@endsection
