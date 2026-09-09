@extends('layout')
@section('title','429')
@section('content')
<div class="empty-state" style="margin-top:50px"><span class="eyebrow">429</span><h1>Previše pokušaja.</h1><p>Pričekaj minutu pa pokušaj ponovno.</p><a class="btn" href="{{ route('catalog') }}">Natrag na ponude</a></div>
@endsection
