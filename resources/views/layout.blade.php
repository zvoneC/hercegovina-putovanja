<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Istraži Hercegovinu') · Hercegovina putovanja</title>
    <meta name="description" content="Istražite gradove, prirodu i turističku ponudu Hercegovine. Sastavite svoj plan izleta.">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<a class="skip-link" href="#main">Preskoči na sadržaj</a>
<header class="site-header">
    <div class="nav-wrap">
        <a class="brand" href="{{ route('catalog') }}"><span class="brand-icon">h.</span><span>hercegovina<span class="brand-small">PUTUJ LOKALNO</span></span></a>
        <nav aria-label="Glavna navigacija">
            <a class="{{ request()->routeIs('catalog', 'offers.*') ? 'active' : '' }}" href="{{ route('catalog') }}">Istraži ponudu</a>
            <a class="{{ request()->routeIs('cities') ? 'active' : '' }}" href="{{ route('cities') }}">Gradovi</a>
            <a class="{{ request()->routeIs('trip*') ? 'active' : '' }}" href="{{ route('trip') }}">Moj plan <span class="nav-count">{{ auth()->user()?->tripItems()->count() ?? 0 }}</span></a>
            @if(auth()->user()?->hasPermission('manage_catalog'))<a href="{{ route('admin.index') }}">Administracija</a>@endif
        </nav>
        <div class="nav-account">
        @auth
            <span class="user-name">{{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="post">@csrf<button class="btn btn-small btn-outline">Odjava</button></form>
        @else
            <a href="{{ route('login') }}">Prijava</a><a class="btn btn-small" href="{{ route('register') }}">Registracija ↗</a>
        @endauth
        </div>
    </div>
</header>
<main id="main" class="container">
    @if(session('success'))<div class="notice success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="notice error" role="alert"><strong>Provjerite unesene podatke.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
<footer class="site-footer container"><a class="footer-brand" href="{{ route('catalog') }}">hercegovina.</a><p>Gradovi, ljudi i mjesta kojima se vraćamo.</p><a href="{{ route('about') }}">O projektu i izvorima</a><span>Studentski projekt · FPMOZ</span></footer>
@stack('scripts')
</body></html>
