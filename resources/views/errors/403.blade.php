@extends('layout')
@section('title','403')
@section('content')
<div class="empty-state" style="margin-top:50px"><span class="eyebrow">403</span><h1>Pristup nije dozvoljen.</h1><p>Tvoj račun nema ovlasti za ovu radnju.</p><a class="btn" href="{{ route('catalog') }}">Natrag na ponude</a></div>
@endsection
