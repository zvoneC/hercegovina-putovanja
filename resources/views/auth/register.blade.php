@extends('layout')
@section('title','Registracija')
@section('content')
<section class="panel auth-panel"><span class="eyebrow">TVOJA HERCEGOVINA</span><h1>Prvi korak do izleta.</h1><p>Otvori račun za svoj plan i dojmove s putovanja.</p>
<form method="post" action="{{ route('register') }}">@csrf
<label for="name">Ime i prezime</label><input id="name" name="name" required minlength="2" maxlength="80" autocomplete="name" value="{{ old('name') }}">
<label for="username">Korisničko ime</label><input id="username" name="username" required minlength="3" maxlength="40" autocomplete="username" value="{{ old('username') }}">
<label for="email">Email adresa</label><input type="email" id="email" name="email" required maxlength="150" autocomplete="email" value="{{ old('email') }}">
<label for="password">Lozinka</label><input type="password" id="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password"><p class="field-help">Najmanje 8 znakova.</p>
<label for="password_confirmation">Ponovi lozinku</label><input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
<button class="btn full">Otvori račun →</button></form>
<p class="under-form">Već imaš račun? <a href="{{ route('login') }}">Prijavi se</a></p></section>
@endsection
