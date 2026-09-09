@extends('layout')
@section('title','404')
@section('content')
<div class="empty-state" style="margin-top:50px"><span class="eyebrow">404</span><h1>Ova stranica nije pronađena.</h1><p>Ponuda možda više nije dostupna ili poveznica nije ispravna.</p><a class="btn" href="{{ route('catalog') }}">Natrag na ponude</a></div>
@endsection
