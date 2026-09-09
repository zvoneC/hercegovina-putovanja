@extends('layout')
@section('title','Ispis plana')
@section('content')
<div class="page-heading"><div><span class="eyebrow">HERCEGOVINA PUTOVANJA</span><h1>Plan izleta</h1><p>{{ auth()->user()->name }} · Pripremljeno {{ date('d.m.Y.') }}</p></div><div class="actions no-print"><a href="{{ route('trip') }}">Natrag na uređivanje</a><button class="btn" data-print>Ispiši / spremi PDF</button></div></div>
@forelse($items as $item)<section class="panel trip-card"><img src="{{ \App\Support\Media::url($item->offer->image) }}" alt=""><div><h2>{{ $item->offer->title }}</h2><p>{{ $item->offer->city->name }} · {{ $item->offer->duration }} h · {{ $item->persons }} osoba</p><p>Datum: <strong>{{ $item->visit_date ? \Carbon\Carbon::parse($item->visit_date)->format('d.m.Y.') : 'Nije odabran' }}</strong></p><p>{{ $item->note }}</p><strong>{{ number_format($item->offer->price*$item->persons,2,',','.') }} KM</strong>@if(!$item->offer->active)<p>Arhivirana ponuda — isključena iz ukupnog iznosa.</p>@endif</div></section>@empty<p>Još nema ponuda u planu.</p>@endforelse
<section class="panel"><h2>Ukupna procjena: {{ number_format($total,2,',','.') }} KM</h2><p>Plan nije potvrda rezervacije. Cijene su ogledni podaci studentskog projekta; prije puta provjerite dostupnost i stvarne cijene kod pružatelja usluge.</p></section>
@endsection
