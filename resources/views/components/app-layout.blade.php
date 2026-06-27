<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $isDemoPage = request()->routeIs('demo.*');
    $isExamPage = request()->routeIs('student.exams.start')
        || request()->routeIs('demo.start')
        || request()->routeIs('exam.terminated');
    $isHome = request()->routeIs('home');
    $isAdmin = request()->is('admin/*') || request()->routeIs('admin.*');
    $isAdminResults = request()->routeIs('admin.results.*');
    $isGuest = ! auth()->check();
    $isStudent = auth()->check()
        && auth()->user()->role === 'student'
        && (request()->routeIs('student.*')
            || request()->routeIs('profile.*')
            || request()->routeIs('exam.terminated'));
    $adminBackgroundImage = ($isAdminResults && file_exists(public_path('images/admin-results-bg.jpg')))
        ? asset('images/admin-results-bg.jpg')
        : asset('images/admin-bg.jpg');
    $isGuestDemoLikePage = ! $isHome && ($isGuest || $isDemoPage);
    $showAmbientEffects = $isGuestDemoLikePage || (($isAdmin || $isStudent) && ! $isExamPage);
@endphp

<body
class="min-h-screen text-gray-100 relative overflow-x-hidden {{ $isAdmin ? 'admin-layout' : (($isGuest || $isHome || $isDemoPage) ? 'guest-layout bg-gray-900' : ($isStudent ? 'student-layout' : '')) }}"
@if($isAdmin)
style="
    margin: 0;
    padding-top: 0;
    background-image:
        linear-gradient(rgba(0,0,0,0.62), rgba(0,0,0,0.62)),
        url('{{ $adminBackgroundImage }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
"
@elseif($isStudent && ! $isDemoPage)
style="
    background-image:
        linear-gradient(rgba(0,0,0,0.62), rgba(0,0,0,0.62)),
        url('{{ asset('images/admin-bg.jpg') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
"
@elseif($isGuestDemoLikePage)
style="
    background-image:
        linear-gradient(rgba(0,0,0,0.62), rgba(0,0,0,0.62)),
        url('{{ asset('images/hero1.jpg') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
"
@elseif($isGuest)
style="background:#020617;"
@endif
>

@if($isAdmin)
<style>
html,
body.admin-layout {
    min-height: 100%;
    height: auto;
    margin: 0 !important;
    padding: 0 !important;
    overscroll-behavior-y: contain;
    background-color: #020617 !important;
}

body.admin-layout {
    padding-top: 0 !important;
    overflow-y: visible;
    overflow-x: hidden;
    overscroll-behavior-y: contain;
    background-color: #020617 !important;
}

body.admin-layout > .flex.min-h-screen.relative {
    min-height: 100vh;
    height: auto;
}

body.admin-layout #main {
    min-height: 0;
}

body.admin-layout main {
    overscroll-behavior-y: contain;
    overflow-y: visible !important;
}

@media (max-width: 768px) {
    body.admin-layout main {
        padding: 0 !important;
    }
}

body.admin-layout > .flex.min-h-screen.relative,
body.admin-layout #main,
body.admin-layout #main > header {
    margin-top: -16 !important;
    padding-top: 12 !important;
}
</style>
@endif

@if($isStudent)
<style>
html,
body.student-layout {
    min-height: 100%;
    height: auto;
    background-color: #020617 !important;
    margin: 0 !important;
    padding: 0 !important;
}

body.student-layout {
    overflow-y: visible;
    overflow-x: hidden;
}

body.student-layout > .flex.min-h-screen.relative {
    min-height: 100vh;
    height: auto;
}

body.student-layout #main {
    min-height: 0;
}

body.student-layout main {
    overscroll-behavior-y: contain;
    overflow-y: visible !important;
}

@media (max-width: 768px) {
    body.student-layout {
        overflow: visible !important;
        min-height: auto !important;
    }

    body.student-layout > .flex.min-h-screen.relative {
        min-height: auto !important;
        height: auto !important;
    }

    body.student-layout main {
        padding: 0 !important;
    }
}
</style>
@endif

@if(!$isAdmin && ($isGuest || $isHome || $isDemoPage))
<nav class="guest-navbar">

    <a href="{{ route('home') }}#home" target="_self"
   class="nav-logo flex items-center gap-2 shrink-0 min-w-fit">

    <!-- App Icon -->
    <x-application-logo
        class="h-16 sm:h-14 md:h-14 w-auto object-contain flex-shrink-0" />

    <!-- App Name -->
    <img src="{{ asset('images/app-name.png') }}"
         alt="Academix Text Logo"
         class="h-18 sm:h-14 md:h-14 w-auto object-contain flex-shrink-0">

</a>

    <ul class="nav-links">
        <li><a href="{{ route('home') }}#home" target="_self">Home</a></li>
        <li><a href="{{ route('home') }}#features" target="_self">Features</a></li>
        <li><a href="{{ route('home') }}#about" target="_self">About</a></li>
        <li><a href="{{ route('home') }}#benefits" target="_self">Benefits</a></li>
        <li><a href="{{ route('home') }}#contact" target="_self">Contact</a></li>
        <li><a href="{{ route('home') }}#support" target="_self">Support</a></li>
    </ul>

    <div class="nav-actions flex items-center gap-2">
        @guest
        <a href="{{ route('login') }}" class="nav-btn bg-blue-600/70 text-black font-semibold hover:bg-blue-800/100 hover:text-white">Login</a>
        <a href="{{ route('register') }}" class="nav-btn bg-orange-600/70 hover:bg-orange-700/90 hidden sm:inline-flex">SignUp</a>
        @else
            @if(auth()->user()->role === 'student')
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg border border-white/25 bg-black/25 px-2 py-1 sm:px-3 sm:py-2 text-white hover:bg-black/40 transition">
                    <img
                        src="{{ auth()->user()->profilePhotoUrl() }}"
                        alt="{{ auth()->user()->name }}"
                        class="h-8 w-8 rounded-full object-cover border border-white/30"
                    >
                    <span class="text-sm font-semibold">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}"
                        x-data
                        @submit.prevent="appConfirm('Are you sure you want to logout?', {
                            title: 'Logout',
                            confirmText: 'Logout',
                            variant: 'logout'
                        }).then(confirmed => { if (confirmed) $el.submit(); });"
                        class="inline-flex items-center m-0">
                    @csrf
                    <button type="submit" class="flex items-center justify-center rounded-lg border border-red-300/30 bg-red-900/40 px-2 py-2 text-red-100 hover:bg-red-800/60 transition" title="Logout">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H9m8 7v1a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2h9a2 2 0 012 2v1"/>
                        </svg>
                    </button>
                </form>
                </a>
                
            @elseif(auth()->user()->role === 'admin')
                <div class="flex items-center gap-2 rounded-lg border border-white/25 bg-black/25 px-3 py-1 text-white">
                    <svg class="h-5 w-5 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A7.5 7.5 0 0112 14.5a7.5 7.5 0 016.879 3.304M15 8a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm font-semibold">Admin</span>
                    <form method="POST" action="{{ route('logout') }}"
                        x-data
                        @submit.prevent="appConfirm('Are you sure you want to logout?', {
                            title: 'Logout',
                            confirmText: 'Logout',
                            variant: 'logout'
                        }).then(confirmed => { if (confirmed) $el.submit(); });"
                        class="inline-flex items-center m-0">
                        @csrf
                        <button type="submit" class="flex items-center justify-center rounded-lg border border-red-300/30 bg-red-900/40 px-2 py-1 text-red-100 hover:bg-red-800/60 transition" title="Logout">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H9m8 7v1a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2h9a2 2 0 012 2v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @endif
        @endguest
    </div>
</nav>
@endif

@if(auth()->check() && !$isExamPage && !$isHome && !$isDemoPage)
<style>
#sidebar {
    transition: transform 0.2s ease-in-out, width 0.2s ease-in-out;
}

#sidebar .sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    white-space: nowrap;
    position: relative;
    overflow: hidden;
    border-radius: 0.95rem;
    border: 1px solid transparent;
    padding: 0.85rem 0.95rem;
    color: rgb(226 232 240);
    transition: transform 0.24s ease, border-color 0.24s ease, background-color 0.24s ease, box-shadow 0.24s ease, color 0.24s ease;
}

#sidebar .sidebar-icon {
    width: 18px !important;
    height: 18px !important;
    min-width: 18px !important;
    min-height: 18px !important;
    max-width: 18px !important;
    max-height: 18px !important;
    flex: 0 0 18px !important;
    display: inline-block !important;
    overflow: visible;
    stroke-width: 2;
}

#sidebar .sidebar-text {
    transition: opacity 0.15s ease-in-out;
}

#sidebar .sidebar-link::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(115deg, rgba(255,255,255,0.12), transparent 36%);
    opacity: 0;
    transition: opacity 0.24s ease;
    pointer-events: none;
}

#sidebar .sidebar-link::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, transparent 20%, rgba(255,255,255,0.14) 45%, transparent 70%);
    transform: translateX(-135%);
    opacity: 0;
    pointer-events: none;
}

#sidebar .sidebar-link:hover,
#sidebar .sidebar-link.is-active {
    transform: translateY(-2px);
    border-color: rgba(255, 255, 255, 0.14);
    background: linear-gradient(180deg, rgba(255,255,255,0.1), rgba(255,255,255,0.04));
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.12), 0 12px 24px rgba(2, 6, 23, 0.22);
    color: white;
}

#sidebar .sidebar-link:hover::before,
#sidebar .sidebar-link.is-active::before {
    opacity: 1;
}

body.student-layout #sidebar .sidebar-link:hover::after,
body.student-layout #sidebar .sidebar-link.is-active::after,
body.admin-layout #sidebar .sidebar-link:hover::after,
body.admin-layout #sidebar .sidebar-link.is-active::after {
    opacity: 1;
    animation: sidebarLinkShimmer 4.6s ease-in-out infinite;
}

#sidebar .student-card {
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.14);
    background:
        linear-gradient(180deg, rgba(204, 164, 6, 0.11), rgba(202, 169, 5, 0.04)),
        rgba(15, 23, 42, 0.34);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.14), 0 16px 32px rgba(2, 6, 23, 0.22);
    backdrop-filter: blur(3px) saturate(115%);
    -webkit-backdrop-filter: blur(3px) saturate(115%);
    animation: sidebarReveal 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

#sidebar .student-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.14), transparent 34%);
    pointer-events: none;
}

#sidebar .student-card::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(110deg, transparent 20%, rgba(255,255,255,0.16) 45%, transparent 68%);
    transform: translateX(-135%);
    animation: sidebarCardShimmer 6.8s ease-in-out infinite;
    pointer-events: none;
}

#sidebar .student-card .student-avatar-ring {
    position: relative;
    width: 5.9rem;
    height: 5.9rem;
    margin: 0 auto 0.7rem;
    border-radius: 9999px;
    display: grid;
    place-items: center;
    box-shadow:
        0 0 0 1px rgba(255, 255, 255, 0.08),
        0 0 18px rgba(245, 158, 11, 0.12),
        0 12px 24px rgba(2, 6, 23, 0.22);
}

#sidebar .student-card .student-avatar-ring::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 4px;
    background:
        conic-gradient(
            from 210deg,
            rgba(255, 243, 176, 0.98),
            rgba(251, 191, 36, 1),
            rgba(245, 158, 11, 0.98),
            rgba(253, 224, 71, 1),
            rgba(255, 243, 176, 0.98)
        );
    -webkit-mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
    mask-composite: exclude;
    animation: sidebarAvatarRingRotate 6.8s linear infinite, sidebarAvatarRingPulse 3.2s ease-in-out infinite;
    box-shadow:
        inset 0 0 0 1px rgba(255, 248, 220, 0.18),
        0 0 22px rgba(245, 158, 11, 0.2);
}

#sidebar .student-card .student-avatar-ring::after {
    content: "✦";
    position: absolute;
    z-index: 2;
    top: 50%;
    left: 50%;
    font-size: 0.78rem;
    line-height: 1;
    color: rgba(255, 251, 235, 0.95);
    text-shadow:
        0 0 8px rgba(255, 255, 255, 0.95),
        0 0 16px rgba(251, 191, 36, 0.6);
    animation: sidebarAvatarStarOrbit 6.8s linear infinite, sidebarAvatarStarPulse 1.8s ease-in-out infinite;
    transform-origin: center;
    pointer-events: none;
}

#sidebar .student-card .student-avatar-ring img {
    position: relative;
    z-index: 1;
    width: calc(100% - 10px);
    height: calc(100% - 10px);
    display: block;
    border-radius: 9999px;
    object-fit: cover;
    object-position: center 25%;
    background: rgba(15, 23, 42, 0.82);
    border: 3px solid rgba(15, 23, 42, 0.96);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

body.student-layout #sidebar .sidebar-link,
body.admin-layout #sidebar .sidebar-link {
    animation: sidebarReveal 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    opacity: 0;
    transform: translateY(14px);
}

body.student-layout #sidebar .sidebar-link:nth-of-type(1),
body.admin-layout #sidebar .sidebar-link:nth-of-type(1) { animation-delay: 0.08s; }
body.student-layout #sidebar .sidebar-link:nth-of-type(2),
body.admin-layout #sidebar .sidebar-link:nth-of-type(2) { animation-delay: 0.14s; }
body.student-layout #sidebar .sidebar-link:nth-of-type(3),
body.admin-layout #sidebar .sidebar-link:nth-of-type(3) { animation-delay: 0.20s; }
body.student-layout #sidebar .sidebar-link:nth-of-type(4),
body.admin-layout #sidebar .sidebar-link:nth-of-type(4) { animation-delay: 0.26s; }
body.admin-layout #sidebar .sidebar-link:nth-of-type(5) { animation-delay: 0.32s; }

@keyframes sidebarReveal {
    from {
        opacity: 0;
        transform: translateY(14px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes sidebarCardShimmer {
    0%, 100% {
        transform: translateX(-135%);
    }
    46%, 58% {
        transform: translateX(135%);
    }
}

@keyframes sidebarLinkShimmer {
    0%, 100% {
        transform: translateX(-135%);
    }
    48%, 60% {
        transform: translateX(135%);
    }
}

@keyframes sidebarAvatarRingRotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes sidebarAvatarRingPulse {
    0%, 100% {
        box-shadow:
            0 0 0 1px rgba(255, 255, 255, 0.08),
            0 0 20px rgba(245, 158, 11, 0.12),
            0 12px 24px rgba(2, 6, 23, 0.22);
    }
    50% {
        box-shadow:
            0 0 0 1px rgba(255, 255, 255, 0.12),
            0 0 34px rgba(245, 158, 11, 0.24),
            0 14px 28px rgba(2, 6, 23, 0.24);
    }
}

@keyframes sidebarAvatarStarOrbit {
    from {
        transform: rotate(0deg) translateY(calc(-50% - 2.95rem)) rotate(0deg);
    }
    to {
        transform: rotate(360deg) translateY(calc(-50% - 2.95rem)) rotate(-360deg);
    }
}

@keyframes sidebarAvatarStarPulse {
    0%, 100% {
        opacity: 0.72;
        text-shadow:
            0 0 7px rgba(255, 255, 255, 0.85),
            0 0 14px rgba(251, 191, 36, 0.48);
    }
    50% {
        opacity: 1;
        text-shadow:
            0 0 10px rgba(255, 255, 255, 1),
            0 0 20px rgba(251, 191, 36, 0.72);
    }
}

@media (prefers-reduced-motion: reduce) {
    #sidebar .student-card,
    body.student-layout #sidebar .sidebar-link,
    body.admin-layout #sidebar .sidebar-link {
        animation: none !important;
        opacity: 1;
        transform: none;
    }

    #sidebar .student-card::after,
    #sidebar .sidebar-link::after,
    #sidebar .student-card .student-avatar-ring,
    #sidebar .student-card .student-avatar-ring::before,
    #sidebar .student-card .student-avatar-ring::after {
        animation: none !important;
    }
}

#sidebar.sidebar-collapsed {
    width: 5rem;
}

#sidebar.sidebar-collapsed .sidebar-text {
    opacity: 0;
    width: 0;
    overflow: hidden;
}

#sidebar.sidebar-collapsed .student-card,
#sidebar.sidebar-collapsed hr {
    display: none;
}

#sidebar .sidebar-logo .logo-icon {
    display: none;
}

#sidebar.sidebar-collapsed .sidebar-logo .logo-full {
    display: none;
}

#sidebar.sidebar-collapsed .sidebar-logo .logo-icon {
    display: block;
    width: 42px;
    height: 42px;
    object-fit: contain;
}

#sidebar.sidebar-collapsed .sidebar-link {
    justify-content: center;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}

#sidebar.sidebar-collapsed form button {
    width: 2.75rem;
    padding-left: 0;
    padding-right: 0;
}

#sidebar.sidebar-collapsed form button .logout-text {
    display: none;
}

/* Show the compact profile widget always in header */
.compact-top-profile {
    display: flex;
    align-items: center;
}

@media (min-width: 1024px) {
    /* Remove logout button when the sidebar is expanded on desktop */
    #sidebar:not(.sidebar-collapsed) + #main header .compact-top-profile .logout-link {
        display: none;
    }

    /* Show logout button only when sidebar is collapsed on desktop */
    #sidebar.sidebar-collapsed + #main header .compact-top-profile .logout-link {
        display: inline-flex;
    }
}

/* Ensure logout remains visible on mobile regardless of sidebar state */
@media (max-width: 1023px) {
    .compact-top-profile {
        padding: 0.5rem 0.75rem;
    }
    .compact-top-profile .logout-link {
        display: inline-flex;
    }
}

.admin-gap-cover {
    margin-top: -56px;
    padding-top: 12px;
}
</style>
@endif

@if($showAmbientEffects)
<style>
#particles {
    position: fixed;
    width: 100%;
    height: 100%;
    z-index: 0;
}

#mouseGlow {
    position: fixed;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle,#3b82f6,transparent);
    pointer-events: none;
    border-radius: 50%;
    filter: blur(80px);
    opacity: .25;
    z-index: 1;
}
</style>

<canvas id="particles"></canvas>
<div id="mouseGlow"></div>
@endif

<div class="flex min-h-screen relative">

@auth
@if(!$isExamPage && !$isHome && !$isDemoPage)
<div id="overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

<aside id="sidebar"
    style="
        background:
            linear-gradient(rgba(0,0,0,0.75), transparent),
            url('/images/sidebar.jpg') no-repeat center center;
        background-size: cover;
    "
    class="fixed top-0 left-0 z-40 w-64 h-full bg-black/40 backdrop-blur-sm border-r border-gray-700 transform -translate-x-full transition-transform duration-200 ease-in-out">

    <div class="p-4 border-b border-gray-700 flex justify-center sidebar-logo">
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/app-name.png') }}" alt="Academix" class="h-14 object-contain logo-full">
            <img src="{{ asset('App-logo.png') }}" alt="Academix Icon" class="logo-icon">
        </a>
    </div>

    <nav class="p-4 space-y-2 text-sm">
        @php
            $role = auth()->user()->role;
        @endphp

        @if($role === 'student')
            <div class="student-card rounded-xl p-4 text-center mb-4">
                <div class="student-avatar-ring">
                    <img
                        src="{{ auth()->user()->profilePhotoUrl() }}"
                        alt="{{ auth()->user()->name }}"
                    >
                </div>

                <h3 class="font-semibold text-base leading-tight">{{ auth()->user()->name }}</h3>
                <p class="text-xs text-gray-300 mt-1">Reg No: {{ auth()->user()->registration_no ?? 'N/A' }}</p>
                <p class="text-xs text-gray-400">Semester: {{ auth()->user()->semester ?? 'N/A' }}</p>
                @if(auth()->user()->college_name)
                <p class="text-[11px] text-gray-400 mt-1">{{ auth()->user()->college_name }}</p>
                @endif
            </div>
            <hr class="border-gray-600/40 mb-3">
        @endif

        @if($role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" title="Dashboard">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10h6v-6h4v6h6V10"/></svg>
                <span class="sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'is-active' : '' }}" title="Student Management">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-8 0v2m8 0H9m8-10a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="sidebar-text">Student Management</span>
            </a>
            <a href="{{ route('admin.exams.index') }}" class="sidebar-link {{ request()->routeIs('admin.exams.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.options.*') ? 'is-active' : '' }}" title="Manage Exams">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6M5 7h14M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                <span class="sidebar-text">Manage Exams</span>
            </a>
            <a href="{{ route('admin.analytics') }}" class="sidebar-link {{ request()->routeIs('admin.analytics') ? 'is-active' : '' }}" title="Analytics">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18m-6-6h12m-9 6V9m6 12V5"/></svg>
                <span class="sidebar-text">Analytics</span>
            </a>
            <a href="{{ route('admin.results.index') }}" class="sidebar-link {{ request()->routeIs('admin.results.*') ? 'is-active' : '' }}" title="Results">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="sidebar-text">Results</span>
            </a>
            <a href="{{ route('admin.violations.index') }}" class="sidebar-link {{ request()->routeIs('admin.violations.*') ? 'is-active' : '' }}" title="Violations">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zm8.25-.75c0 4.556-3.694 8.25-8.25 8.25S3.75 20.306 3.75 15.75 7.444 7.5 12 7.5s8.25 3.694 8.25 8.25zM12 3v3"/></svg>
                <span class="sidebar-text">Violations</span>
            </a>
        @endif

        @if($role === 'student')
            <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'is-active' : '' }}" title="Dashboard">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10h6v-6h4v6h6V10"/></svg>
                <span class="sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}" title="My Profile">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A7.5 7.5 0 0112 14.5a7.5 7.5 0 016.879 3.304M15 8a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="sidebar-text">My Profile</span>
            </a>
            <a href="{{ route('student.exams.index') }}" class="sidebar-link {{ request()->routeIs('student.exams.*') ? 'is-active' : '' }}" title="Exams">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                <span class="sidebar-text">Exams</span>
            </a>
            <a href="{{ route('student.results.index') }}" class="sidebar-link {{ request()->routeIs('student.results.*') ? 'is-active' : '' }}" title="My Results">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m3 6V7m3 10V4m5 16H4a1 1 0 01-1-1V4a1 1 0 011-1h16a1 1 0 011 1v15a1 1 0 01-1 1z"/></svg>
                <span class="sidebar-text">My Results</span>
            </a>
        @endif

        <center>
        <form method="POST" action="{{ route('logout') }}"
            x-data
            @submit.prevent="appConfirm('Are you sure you want to logout?', {
                title: 'Logout',
                confirmText: 'Logout',
                variant: 'logout'
            }).then(confirmed => { if (confirmed) $el.submit(); });"
            class="pt-10">
    
            @csrf

            <button type="submit"
                class="w-20 h-10 flex items-center justify-center rounded-lg bg-red-800/70 text-red-200 font-semibold hover:bg-red-600/100 hover:text-red-200 hover:underline transition duration-200">
        
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H9m8 7v1a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2h9a2 2 0 012 2v1"/>
                </svg>

                <span class="logout-text ml-2">Logout</span>
            </button>
        </form>
        </center>
    </nav>
</aside>
@endif
@endauth

<div id="main" class="flex-1 flex flex-col transition-all duration-200 ease-in-out">
@auth
@if(!$isExamPage && !$isHome && !$isDemoPage)
<header class="flex items-center gap-3 p-4 bg-black/40 backdrop-blur-sm shadow {{ $isAdmin ? 'admin-gap-cover' : '' }}">
    <button onclick="toggleSidebar()" class="p-2 rounded bg-gray-700 hover:bg-gray-600">
        &#9776;
    </button>
    <span class="font-semibold hidden sm:inline">
        {{ auth()->user()->role === 'admin' ? 'Admin Panel' : 'Menu' }}
    </span>
    
    <!-- Compact top-right profile (visible when sidebar is collapsed) -->
    <div class="compact-top-profile items-center gap-2 ml-auto rounded-2xl border border-white/20 bg-slate-100/15 px-2 py-1 backdrop-blur-sm shadow-sm">
        @if(auth()->user()->role === 'student')
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-full bg-slate-950/80 px-2 py-1 text-white hover:bg-slate-900/90 transition">
                <img src="{{ auth()->user()->profilePhotoUrl() }}" alt="{{ auth()->user()->name }}" class="h-7 w-7 rounded-full object-cover border border-white/25">
                <span class="text-xs font-semibold">{{ auth()->user()->name }}</span>
            </a>
        @else
            <div class="flex items-center gap-2 rounded-full bg-slate-950/80 px-2 py-1 text-white">
                <svg class="h-7 w-7 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A7.5 7.5 0 0112 14.5a7.5 7.5 0 016.879 3.304M15 8a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-xs font-semibold">{{ auth()->user()->name }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('logout') }}"
            x-data
            @submit.prevent="appConfirm('Are you sure you want to logout?', { title: 'Logout', confirmText: 'Logout', variant: 'logout' }).then(confirmed => { if (confirmed) $el.submit(); });"
            class="logout-link inline-flex items-center m-0">
            @csrf
            <button type="submit" class="flex items-center justify-center rounded-lg border border-red-300/30 bg-red-900/40 px-2 py-1 text-red-100 hover:bg-red-800/60 transition" title="Logout">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H9m8 7v1a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2h9a2 2 0 012 2v1"/>
                </svg>
            </button>
        </form>
    </div>
</header>
@endif
@endauth

<main class="flex-1 overflow-y-auto {{ ($isStudent || $isHome || $isDemoPage) ? 'p-0' : 'p-6' }} bg-transparent relative z-10">
    {{ $slot }}
</main>

</div>
</div>

<div id="appDialog" class="app-dialog hidden" aria-hidden="true">
    <div class="app-dialog-backdrop"></div>
    <div class="app-dialog-panel" role="dialog" aria-modal="true" aria-labelledby="appDialogTitle">
        <div class="app-dialog-accent"></div>
        <div class="app-dialog-body">
            <p id="appDialogEyebrow" class="app-dialog-eyebrow">Notice</p>
            <h3 id="appDialogTitle" class="app-dialog-title">Message</h3>
            <p id="appDialogMessage" class="app-dialog-message"></p>
            <div id="appDialogLogoutWrap" class="app-dialog-logout hidden" aria-hidden="true">
                <button type="button" id="appDialogLogoutAction" class="logoutButton">
                    <svg class="doorway" viewBox="0 0 100 100" aria-hidden="true">
                        <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                        <path class="bang" d="M40.5 43.7L26.6 31.4l-2.5 6.7zM41.9 50.4l-19.5-4-1.4 6.3zM40 57.4l-17.7 3.9 3.9 5.7z" />
                    </svg>
                    <svg class="figure" viewBox="0 0 100 100" aria-hidden="true">
                        <circle cx="52.1" cy="32.4" r="6.4" />
                        <path d="M50.7 62.8c-1.2 2.5-3.6 5-7.2 4-3.2-.9-4.9-3.5-4-7.8.7-3.4 3.1-13.8 4.1-15.8 1.7-3.4 1.6-4.6 7-3.7 4.3.7 4.6 2.5 4.3 5.4-.4 3.7-2.8 15.1-4.2 17.9z" />
                        <g class="arm1">
                            <path d="M55.5 56.5l-6-9.5c-1-1.5-.6-3.5.9-4.4 1.5-1 3.7-1.1 4.6.4l6.1 10c1 1.5.3 3.5-1.1 4.4-1.5.9-3.5.5-4.5-.9z" />
                            <path class="wrist1" d="M69.4 59.9L58.1 58c-1.7-.3-2.9-1.9-2.6-3.7.3-1.7 1.9-2.9 3.7-2.6l11.4 1.9c1.7.3 2.9 1.9 2.6 3.7-.4 1.7-2 2.9-3.8 2.6z" />
                        </g>
                        <g class="arm2">
                            <path d="M34.2 43.6L45 40.3c1.7-.6 3.5.3 4 2 .6 1.7-.3 4-2 4.5l-10.8 2.8c-1.7.6-3.5-.3-4-2-.6-1.6.3-3.4 2-4z" />
                            <path class="wrist2" d="M27.1 56.2L32 45.7c.7-1.6 2.6-2.3 4.2-1.6 1.6.7 2.3 2.6 1.6 4.2L33 58.8c-.7 1.6-2.6 2.3-4.2 1.6-1.7-.7-2.4-2.6-1.7-4.2z" />
                        </g>
                        <g class="leg1">
                            <path d="M52.1 73.2s-7-5.7-7.9-6.5c-.9-.9-1.2-3.5-.1-4.9 1.1-1.4 3.8-1.9 5.2-.9l7.9 7c1.4 1.1 1.7 3.5.7 4.9-1.1 1.4-4.4 1.5-5.8.4z" />
                            <path class="calf1" d="M52.6 84.4l-1-12.8c-.1-1.9 1.5-3.6 3.5-3.7 2-.1 3.7 1.4 3.8 3.4l1 12.8c.1 1.9-1.5 3.6-3.5 3.7-2 0-3.7-1.5-3.8-3.4z" />
                        </g>
                        <g class="leg2">
                            <path d="M37.8 72.7s1.3-10.2 1.6-11.4 2.4-2.8 4.1-2.6c1.7.2 3.6 2.3 3.4 4l-1.8 11.1c-.2 1.7-1.7 3.3-3.4 3.1-1.8-.2-4.1-2.4-3.9-4.2z" />
                            <path class="calf2" d="M29.5 82.3l9.6-10.9c1.3-1.4 3.6-1.5 5.1-.1 1.5 1.4.4 4.9-.9 6.3l-8.5 9.6c-1.3 1.4-3.6 1.5-5.1.1-1.4-1.3-1.5-3.5-.2-5z" />
                        </g>
                    </svg>
                    <svg class="door" viewBox="0 0 100 100" aria-hidden="true">
                        <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                        <circle cx="66" cy="50" r="3.7" />
                    </svg>
                    <span class="button-text">Log Out</span>
                </button>
            </div>
        </div>
        <div class="app-dialog-actions">
            <button type="button" id="appDialogCancel" class="app-dialog-btn app-dialog-btn-muted hidden">Cancel</button>
            <button type="button" id="appDialogConfirm" class="app-dialog-btn app-dialog-btn-primary">OK</button>
        </div>
    </div>
</div>

<style>
.app-dialog {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.app-dialog.hidden {
    display: none;
}

.app-dialog-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.52);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    animation: appDialogBackdropIn 0.2s ease forwards;
}

.app-dialog-panel {
    position: relative;
    width: min(100%, 27rem);
    overflow: hidden;
    border-radius: 0.95rem;
    border: 1px solid rgba(148, 163, 184, 0.24);
    background: rgba(15, 23, 42, 0.96);
    box-shadow: 0 20px 48px rgba(2, 6, 23, 0.42);
    animation: appDialogPanelIn 0.28s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

.app-dialog-accent {
    height: 1px;
    background: rgba(148, 163, 184, 0.18);
}

.app-dialog-body {
    padding: 1.1rem 1.1rem 0.75rem;
}

.app-dialog-eyebrow {
    margin: 0 0 0.45rem;
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: rgb(148 163 184);
}

.app-dialog.is-confirm .app-dialog-eyebrow {
    color: rgb(248 113 113);
}

.app-dialog-title {
    margin: 0;
    color: white;
    font-size: 1.02rem;
    font-weight: 600;
}

.app-dialog-message {
    margin: 0.65rem 0 0;
    color: rgb(203 213 225);
    font-size: 0.92rem;
    line-height: 1.55;
    white-space: pre-line;
}

.app-dialog-logout {
    display: flex;
    justify-content: center;
    margin-top: 1.25rem;
}

.app-dialog-logout.hidden,
.app-dialog-logout[aria-hidden="true"] {
    display: none;
}

.app-dialog.is-logout .app-dialog-panel {
    width: min(100%, 30rem);
    overflow: visible;
}

.app-dialog.is-logout .app-dialog-eyebrow {
    color: rgb(248 113 113);
}

.app-dialog.is-logout .app-dialog-message {
    text-align: center;
}

.app-dialog.is-logout .app-dialog-body,
.app-dialog.is-logout .app-dialog-logout {
    overflow: visible;
}

.app-dialog-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.6rem;
    padding: 0.85rem 1.1rem 1.1rem;
}

.app-dialog-btn {
    min-width: 5.5rem;
    border: 1px solid transparent;
    border-radius: 0.7rem;
    padding: 0.62rem 0.95rem;
    font-weight: 600;
    font-size: 0.9rem;
    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.app-dialog-btn:hover {
    transform: none;
    filter: none;
}

.app-dialog-btn-muted {
    background: rgba(51, 65, 85, 0.82);
    border-color: rgba(148, 163, 184, 0.16);
    color: rgb(226 232 240);
}

.app-dialog-btn-primary {
    background: rgb(37 99 235);
    border-color: rgba(96, 165, 250, 0.32);
    color: rgb(254 202 202);
}

.app-dialog-btn-muted:hover {
    background: rgba(71, 85, 105, 0.92);
}

.app-dialog-btn-primary:hover {
    background: rgb(29 78 216);
}

.logoutButton {
    --figure-duration: 100ms;
    --transform-figure: none;
    --walking-duration: 100ms;
    --transform-arm1: none;
    --transform-wrist1: none;
    --transform-arm2: none;
    --transform-wrist2: none;
    --transform-leg1: none;
    --transform-calf1: none;
    --transform-leg2: none;
    --transform-calf2: none;
    background: none;
    border: 0;
    color: #f4f7ff;
    cursor: pointer;
    display: block;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    height: 44px;
    outline: none;
    padding: 0 0 0 20px;
    perspective: 100px;
    position: relative;
    text-align: left;
    width: 148px;
    overflow: visible;
    -webkit-tap-highlight-color: transparent;
}

.logoutButton::before {
    background-color: #1e2235;
    border-radius: 0.75rem;
    content: '';
    display: block;
    height: 100%;
    left: 0;
    position: absolute;
    top: 0;
    transform: none;
    transition: transform 50ms ease;
    width: 100%;
    z-index: 2;
}

.logoutButton:hover .door {
    transform: rotateY(20deg);
}

.logoutButton:active::before {
    transform: scale(.96);
}

.logoutButton:active .door {
    transform: rotateY(28deg);
}

.logoutButton.clicked::before {
    transform: none;
}

.logoutButton.clicked .door {
    transform: rotateY(35deg);
}

.logoutButton.door-slammed .door {
    transform: none;
    transition: transform 100ms ease-in 250ms;
}

.logoutButton.falling {
    animation: shake 200ms linear;
}

.logoutButton.falling .bang {
    animation: flash 300ms linear;
}

.logoutButton.falling .figure {
    animation: spin 1000ms infinite linear;
    bottom: -1080px;
    filter: blur(3px);
    opacity: 0;
    right: 1px;
    transition: transform calc(var(--figure-duration) * 1ms) linear,
        bottom calc(var(--figure-duration) * 1ms) cubic-bezier(0.7, 0.1, 1, 1) 100ms,
        opacity calc(var(--figure-duration) * 0.25ms) linear calc(var(--figure-duration) * 0.75ms),
        filter calc(var(--figure-duration) * 0.28ms) linear calc(var(--figure-duration) * 0.38ms);
    z-index: 1;
}

.logoutButton .button-text {
    color: #f4f7ff;
    font-weight: 600;
    position: relative;
    z-index: 10;
}

.logoutButton svg {
    display: block;
    position: absolute;
}

.logoutButton .figure {
    bottom: 6px;
    fill: #4371f7;
    filter: blur(0);
    right: 18px;
    transform: var(--transform-figure);
    transition: transform calc(var(--figure-duration) * 1ms) cubic-bezier(0.2, 0.1, 0.80, 0.9),
        filter calc(var(--figure-duration) * 0.35ms) linear;
    width: 30px;
    z-index: 4;
}

.logoutButton .door,
.logoutButton .doorway {
    bottom: 4px;
    fill: #f4f7ff;
    right: 12px;
    width: 32px;
}

.logoutButton .door {
    transform: rotateY(20deg);
    transform-origin: 100% 50%;
    transform-style: preserve-3d;
    transition: transform 200ms ease;
    z-index: 5;
}

.logoutButton .door path {
    fill: #4371f7;
    stroke: #4371f7;
    stroke-width: 4;
}

.logoutButton .doorway {
    z-index: 3;
}

.logoutButton .bang {
    opacity: 0;
}

.logoutButton .arm1,
.logoutButton .wrist1,
.logoutButton .arm2,
.logoutButton .wrist2,
.logoutButton .leg1,
.logoutButton .calf1,
.logoutButton .leg2,
.logoutButton .calf2 {
    transition: transform calc(var(--walking-duration) * 1ms) ease-in-out;
}

.logoutButton .arm1 {
    transform: var(--transform-arm1);
    transform-origin: 52% 45%;
}

.logoutButton .wrist1 {
    transform: var(--transform-wrist1);
    transform-origin: 59% 55%;
}

.logoutButton .arm2 {
    transform: var(--transform-arm2);
    transform-origin: 47% 43%;
}

.logoutButton .wrist2 {
    transform: var(--transform-wrist2);
    transform-origin: 35% 47%;
}

.logoutButton .leg1 {
    transform: var(--transform-leg1);
    transform-origin: 47% 64.5%;
}

.logoutButton .calf1 {
    transform: var(--transform-calf1);
    transform-origin: 55.5% 71.5%;
}

.logoutButton .leg2 {
    transform: var(--transform-leg2);
    transform-origin: 43% 63%;
}

.logoutButton .calf2 {
    transform: var(--transform-calf2);
    transform-origin: 41.5% 73%;
}

@keyframes appDialogBackdropIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes appDialogPanelIn {
    from {
        opacity: 0;
        transform: translateY(90px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes spin {
    from { transform: rotate(0deg) scale(0.94); }
    to { transform: rotate(359deg) scale(0.94); }
}

@keyframes shake {
    0% { transform: rotate(-1deg); }
    50% { transform: rotate(2deg); }
    100% { transform: rotate(-1deg); }
}

@keyframes flash {
    0% { opacity: 0.4; }
    100% { opacity: 0; }
}
</style>

@if(auth()->check() && !$isExamPage && !$isHome && !$isDemoPage)
<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const main = document.getElementById('main');
const DESKTOP_MIN_WIDTH = 1024;
const MAIN_OFFSET_EXPANDED = 'lg:ml-64';
const MAIN_OFFSET_COLLAPSED = 'lg:ml-20';
const SIDEBAR_COLLAPSED_STORAGE_KEY = 'sidebarCollapsed';
let isSidebarCollapsed = false;

function isDesktopView() {
    return window.innerWidth >= DESKTOP_MIN_WIDTH;
}

function applyMainOffset() {
    main?.classList.remove(MAIN_OFFSET_EXPANDED, MAIN_OFFSET_COLLAPSED);
    if (isDesktopView() && !sidebar?.classList.contains('-translate-x-full')) {
        main?.classList.add(isSidebarCollapsed ? MAIN_OFFSET_COLLAPSED : MAIN_OFFSET_EXPANDED);
    }
}

function setSidebarCollapsed(collapsed) {
    isSidebarCollapsed = collapsed;
    sidebar?.classList.toggle('sidebar-collapsed', collapsed);
    applyMainOffset();
    try {
        localStorage.setItem(SIDEBAR_COLLAPSED_STORAGE_KEY, collapsed ? '1' : '0');
    } catch {}
}

function openSidebar() {
    sidebar?.classList.remove('-translate-x-full');
    if (isDesktopView()) {
        applyMainOffset();
        overlay?.classList.add('hidden');
    } else {
        main?.classList.remove(MAIN_OFFSET_EXPANDED, MAIN_OFFSET_COLLAPSED);
        sidebar?.classList.remove('sidebar-collapsed');
        overlay?.classList.remove('hidden');
    }
}

function closeSidebar() {
    sidebar?.classList.add('-translate-x-full');
    overlay?.classList.add('hidden');
    main?.classList.remove(MAIN_OFFSET_EXPANDED, MAIN_OFFSET_COLLAPSED);
}

function toggleSidebar() {
    if (isDesktopView()) {
        if (sidebar?.classList.contains('-translate-x-full')) {
            openSidebar();
        } else {
            setSidebarCollapsed(!isSidebarCollapsed);
        }
    } else {
        if (sidebar?.classList.contains('-translate-x-full')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    }
}

overlay?.addEventListener('click', () => {
    closeSidebar();
});

window.addEventListener('resize', () => {
    if (isDesktopView()) {
        sidebar?.classList.remove('-translate-x-full');
        setSidebarCollapsed(isSidebarCollapsed);
        overlay?.classList.add('hidden');
    } else {
        main?.classList.remove(MAIN_OFFSET_EXPANDED, MAIN_OFFSET_COLLAPSED);
    }
});

document.querySelectorAll('#sidebar a').forEach((link) => {
    link.addEventListener('click', () => {
        if (!isDesktopView()) {
            closeSidebar();
        }
    });
});

try {
    isSidebarCollapsed = localStorage.getItem(SIDEBAR_COLLAPSED_STORAGE_KEY) === '1';
} catch {}

if (isDesktopView()) {
    openSidebar();
    setSidebarCollapsed(isSidebarCollapsed);
} else {
    closeSidebar();
}
</script>
@endif

<script>
(() => {
    const dialog = document.getElementById('appDialog');
    const title = document.getElementById('appDialogTitle');
    const message = document.getElementById('appDialogMessage');
    const eyebrow = document.getElementById('appDialogEyebrow');
    const confirmBtn = document.getElementById('appDialogConfirm');
    const cancelBtn = document.getElementById('appDialogCancel');
    const logoutWrap = document.getElementById('appDialogLogoutWrap');
    const logoutAction = document.getElementById('appDialogLogoutAction');
    const backdrop = dialog?.querySelector('.app-dialog-backdrop');
    if (!dialog || !title || !message || !confirmBtn || !cancelBtn || !logoutWrap || !logoutAction || !backdrop || window.appConfirm) {
        return;
    }

    let resolver = null;
    let activeOptions = {};
    let logoutTimerIds = [];
    let isLogoutAnimating = false;

    const logoutButtonStates = {
        default: {
            '--figure-duration': '100',
            '--transform-figure': 'none',
            '--walking-duration': '100',
            '--transform-arm1': 'none',
            '--transform-wrist1': 'none',
            '--transform-arm2': 'none',
            '--transform-wrist2': 'none',
            '--transform-leg1': 'none',
            '--transform-calf1': 'none',
            '--transform-leg2': 'none',
            '--transform-calf2': 'none'
        },
        hover: {
            '--figure-duration': '100',
            '--transform-figure': 'translateX(1.5px)',
            '--walking-duration': '100',
            '--transform-arm1': 'rotate(-5deg)',
            '--transform-wrist1': 'rotate(-15deg)',
            '--transform-arm2': 'rotate(5deg)',
            '--transform-wrist2': 'rotate(6deg)',
            '--transform-leg1': 'rotate(-10deg)',
            '--transform-calf1': 'rotate(5deg)',
            '--transform-leg2': 'rotate(20deg)',
            '--transform-calf2': 'rotate(-20deg)'
        },
        walking1: {
            '--figure-duration': '300',
            '--transform-figure': 'translateX(11px)',
            '--walking-duration': '300',
            '--transform-arm1': 'translateX(-4px) translateY(-2px) rotate(120deg)',
            '--transform-wrist1': 'rotate(-5deg)',
            '--transform-arm2': 'translateX(4px) rotate(-110deg)',
            '--transform-wrist2': 'rotate(-5deg)',
            '--transform-leg1': 'translateX(-3px) rotate(80deg)',
            '--transform-calf1': 'rotate(-30deg)',
            '--transform-leg2': 'translateX(4px) rotate(-60deg)',
            '--transform-calf2': 'rotate(20deg)'
        },
        walking2: {
            '--figure-duration': '400',
            '--transform-figure': 'translateX(17px)',
            '--walking-duration': '300',
            '--transform-arm1': 'rotate(60deg)',
            '--transform-wrist1': 'rotate(-15deg)',
            '--transform-arm2': 'rotate(-45deg)',
            '--transform-wrist2': 'rotate(6deg)',
            '--transform-leg1': 'rotate(-5deg)',
            '--transform-calf1': 'rotate(10deg)',
            '--transform-leg2': 'rotate(10deg)',
            '--transform-calf2': 'rotate(-20deg)'
        },
        falling1: {
            '--figure-duration': '1600',
            '--walking-duration': '400',
            '--transform-arm1': 'rotate(-60deg)',
            '--transform-wrist1': 'none',
            '--transform-arm2': 'rotate(30deg)',
            '--transform-wrist2': 'rotate(120deg)',
            '--transform-leg1': 'rotate(-30deg)',
            '--transform-calf1': 'rotate(-20deg)',
            '--transform-leg2': 'rotate(20deg)'
        },
        falling2: {
            '--walking-duration': '300',
            '--transform-arm1': 'rotate(-100deg)',
            '--transform-arm2': 'rotate(-60deg)',
            '--transform-wrist2': 'rotate(60deg)',
            '--transform-leg1': 'rotate(80deg)',
            '--transform-calf1': 'rotate(20deg)',
            '--transform-leg2': 'rotate(-60deg)'
        },
        falling3: {
            '--walking-duration': '500',
            '--transform-arm1': 'rotate(-30deg)',
            '--transform-wrist1': 'rotate(40deg)',
            '--transform-arm2': 'rotate(50deg)',
            '--transform-wrist2': 'none',
            '--transform-leg1': 'rotate(-30deg)',
            '--transform-leg2': 'rotate(20deg)',
            '--transform-calf2': 'none'
        }
    };

    function scheduleLogoutStep(callback, delay) {
        const timerId = window.setTimeout(callback, delay);
        logoutTimerIds.push(timerId);
    }

    function updateLogoutButtonState(state) {
        const nextState = logoutButtonStates[state];
        if (!nextState) return;
        logoutAction.dataset.state = state;
        Object.entries(nextState).forEach(([key, value]) => {
            logoutAction.style.setProperty(key, value);
        });
    }

    function resetLogoutAnimation() {
        logoutTimerIds.forEach((timerId) => window.clearTimeout(timerId));
        logoutTimerIds = [];
        isLogoutAnimating = false;
        logoutAction.disabled = false;
        cancelBtn.disabled = false;
        dialog.classList.remove('logout-running');
        logoutAction.classList.remove('clicked', 'door-slammed', 'falling');
        updateLogoutButtonState('default');
    }

    function runLogoutAnimation() {
        if (isLogoutAnimating) return;

        isLogoutAnimating = true;
        logoutAction.disabled = true;
        cancelBtn.disabled = true;
        dialog.classList.add('logout-running');
        logoutAction.classList.add('clicked');
        updateLogoutButtonState('walking1');

        scheduleLogoutStep(() => {
            logoutAction.classList.add('door-slammed');
            updateLogoutButtonState('walking2');

            scheduleLogoutStep(() => {
                logoutAction.classList.add('falling');
                updateLogoutButtonState('falling1');

                scheduleLogoutStep(() => {
                    updateLogoutButtonState('falling2');

                    scheduleLogoutStep(() => {
                        updateLogoutButtonState('falling3');

                        scheduleLogoutStep(() => {
                            if (resolver) {
                                resolver(true);
                                resolver = null;
                            }
                        }, Number(logoutButtonStates.falling3['--walking-duration'] || 0));
                    }, Number(logoutButtonStates.falling2['--walking-duration']));
                }, Number(logoutButtonStates.falling1['--walking-duration']));
            }, Number(logoutButtonStates.walking2['--figure-duration']));
        }, Number(logoutButtonStates.walking1['--figure-duration']));
    }

    function closeDialog(result = false) {
        resetLogoutAnimation();
        dialog.classList.add('hidden');
        dialog.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('app-dialog-open');
        dialog.classList.remove('is-confirm', 'is-logout');
        logoutWrap.classList.add('hidden');
        logoutWrap.setAttribute('aria-hidden', 'true');
        confirmBtn.classList.remove('hidden');

        if (resolver) {
            resolver(result);
            resolver = null;
        }
    }

    function openDialog(options = {}) {
        activeOptions = options;
        title.textContent = options.title || 'Notice';
        message.textContent = options.message || '';
        eyebrow.textContent = options.eyebrow || 'Notice';
        confirmBtn.textContent = options.confirmText || 'OK';
        cancelBtn.textContent = options.cancelText || 'Cancel';
        cancelBtn.classList.toggle('hidden', !options.showCancel);
        dialog.classList.toggle('is-confirm', !!options.showCancel);
        dialog.classList.toggle('is-logout', options.variant === 'logout');
        confirmBtn.classList.toggle('hidden', options.variant === 'logout');
        logoutWrap.classList.toggle('hidden', options.variant !== 'logout');
        logoutWrap.setAttribute('aria-hidden', options.variant === 'logout' ? 'false' : 'true');
        dialog.classList.remove('hidden');
        dialog.setAttribute('aria-hidden', 'false');
        document.body.classList.add('app-dialog-open');
        resetLogoutAnimation();
        window.setTimeout(() => {
            if (options.variant === 'logout') {
                logoutAction.focus();
            } else {
                confirmBtn.focus();
            }
        }, 20);

        return new Promise((resolve) => {
            resolver = resolve;
        });
    }

    confirmBtn.addEventListener('click', () => closeDialog(true));
    cancelBtn.addEventListener('click', () => closeDialog(false));
    logoutAction.addEventListener('mouseenter', () => {
        if (!isLogoutAnimating && logoutAction.dataset.state === 'default') {
            updateLogoutButtonState('hover');
        }
    });
    logoutAction.addEventListener('mouseleave', () => {
        if (!isLogoutAnimating && logoutAction.dataset.state === 'hover') {
            updateLogoutButtonState('default');
        }
    });
    logoutAction.addEventListener('click', runLogoutAnimation);
    backdrop.addEventListener('click', () => {
        if (activeOptions.variant === 'logout' && isLogoutAnimating) return;
        closeDialog(false);
    });
    document.addEventListener('keydown', (event) => {
        if (dialog.classList.contains('hidden')) return;
        if (event.key === 'Escape') {
            if (activeOptions.variant === 'logout' && isLogoutAnimating) return;
            closeDialog(false);
        }
    });

    window.appAlert = function (messageText, options = {}) {
        return openDialog({
            title: options.title || 'Warning',
            eyebrow: options.eyebrow || 'Notice',
            message: messageText,
            confirmText: options.confirmText || 'OK',
            showCancel: false,
            variant: options.variant,
        });
    };

    window.appConfirm = function (messageText, options = {}) {
        return openDialog({
            title: options.title || 'Please Confirm',
            eyebrow: options.eyebrow || 'Confirmation',
            message: messageText,
            confirmText: options.confirmText || 'Confirm',
            cancelText: options.cancelText || 'Cancel',
            showCancel: true,
            variant: options.variant,
        });
    };
})();
</script>

@if($showAmbientEffects)
<script>
const canvas = document.getElementById("particles");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particles = [];

for (let i = 0; i < 80; i++) {
    particles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        r: Math.random() * 2 + 0.5,
        dx: (Math.random() - 0.5) * 0.5,
        dy: (Math.random() - 0.5) * 0.5
    });
}

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    particles.forEach((p) => {
        p.x += p.dx;
        p.y += p.dy;

        if (p.x < 0 || p.x > canvas.width) p.dx = -p.dx;
        if (p.y < 0 || p.y > canvas.height) p.dy = -p.dy;

        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = "rgba(255,255,255,0.6)";
        ctx.fill();
    });

    requestAnimationFrame(animate);
}

animate();

window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});

const mouseGlow = document.getElementById('mouseGlow');
document.addEventListener("mousemove", (e) => {
    mouseGlow.style.left = e.clientX - 150 + "px";
    mouseGlow.style.top = e.clientY - 150 + "px";
});
</script>
@endif

</body>
</html>
