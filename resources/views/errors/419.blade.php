@extends('layout')
@section('title','419')
@section('content')
<div class="empty-state" style="margin-top:50px"><span class="eyebrow">419</span><h1>Obrazac je istekao.</h1><p>Osvježi stranicu i pokušaj ponovno poslati obrazac.</p><a class="btn" href="{{ route('catalog') }}">Natrag na ponude</a></div>
@endsection
