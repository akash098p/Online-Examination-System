<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="guest-layout bg-gray-900 text-gray-100 min-h-screen">

<nav class="guest-navbar">
    <a href="{{ route('home') }}#home" class="nav-logo flex items-center gap-2 logo-glow">
        <x-application-logo class="w-12 h-12 fill-current text-white logo-glow-item" />
        <img src="{{ asset('images/app-name.png') }}" alt="Academix Text Logo" class="h-12 logo-glow-item">
    </a>

    <ul class="nav-links">
        <li><a href="{{ route('home') }}#home">Home</a></li>
        <li><a href="{{ route('home') }}#features">Features</a></li>
        <li><a href="{{ route('home') }}#about">About</a></li>
        <li><a href="{{ route('home') }}#benefits">Benefits</a></li>
        <li><a href="{{ route('home') }}#contact">Contact</a></li>
        <li><a href="{{ route('home') }}#support">Support</a></li>
    </ul>

    <div class="nav-actions">
        <a href="{{ route('login') }}" class="nav-btn bg-blue-600/70 text-black font-semibold hover:bg-blue-800/100 hover:text-white">Login</a>
        <a href="{{ route('register') }}" class="nav-btn bg-orange-600/70 hover:bg-orange-700/90 hidden sm:inline-flex">Sign Up</a>
    </div>
</nav>

<main class="px-4 py-10">
    {{ $slot }}
</main>

</body>
</html>
