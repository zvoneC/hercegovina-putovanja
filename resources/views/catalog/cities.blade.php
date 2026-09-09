@extends('layout')
@section('title','Gradovi Hercegovine')
@section('content')
<div class="page-heading"><div><span class="eyebrow">SEDAM POLAZIŠTA. BEZBROJ MALIH PRIČA.</span><h1>Upoznaj gradove Hercegovine.</h1><p>Odaberi grad i istraži što se nalazi u njegovoj okolici.</p></div></div>
<div class="city-grid">@foreach($cities as $city)<article class="city-card"><a href="{{ route('catalog',['city'=>$city->id]) }}#ponuda"><img src="{{ \App\Support\Media::url($city->image) }}" alt="{{ $city->name }} i okolica" loading="lazy"><h2>{{ $city->name }}</h2></a><p>{{ $city->description }}</p><a class="text-link" href="{{ route('catalog',['city'=>$city->id]) }}#ponuda">Istraži ponude ({{ $city->offers_count }}) ↗</a></article>@endforeach</div>
@endsection
