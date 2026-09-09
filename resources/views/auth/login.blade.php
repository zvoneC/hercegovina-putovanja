@extends('layout')
@section('title','Prijava')
@section('content')
<section class="panel auth-panel"><span class="eyebrow">DOBRO DOŠLI NATRAG</span><h1>Nastavi svoj plan.</h1><p>Prijavi se i spremi mjesta koja želiš posjetiti.</p>
<form method="post" action="{{ route('login') }}">@csrf
<label for="email">Email adresa</label><input type="email" id="email" name="email" required autocomplete="username" value="{{ old('email') }}">
<label for="password">Lozinka</label><input type="password" id="password" name="password" required autocomplete="current-password">
<button class="btn full">Prijavi se →</button></form>
<p class="under-form">Nemaš račun? <a href="{{ route('register') }}">Registriraj se</a></p></section>
@endsection
