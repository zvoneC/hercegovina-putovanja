@extends('layout')
@section('title',$offer->title)
@section('content')
<div class="breadcrumbs"><a href="{{ route('catalog') }}">Sve ponude</a> / <a href="{{ route('catalog',['city'=>$offer->city_id]) }}">{{ $offer->city->name }}</a> / {{ $offer->title }}</div>
<div class="page-heading"><div><span class="eyebrow">{{ $offer->category->name }} / {{ $offer->city->name }}</span><h1>{{ $offer->title }}</h1></div>@if(!$offer->active)<span class="pill">Arhivirana ponuda</span>@endif</div>
<div class="detail-grid"><div>
<img class="detail-image" src="{{ \App\Support\Media::url($offer->image) }}" alt="{{ $offer->title }}">
<section class="panel"><h2>O ovom doživljaju</h2><div class="detail-content">{!! $offer->description !!}</div><div class="facts"><span class="pill">{{ $offer->duration }} sata</span><span class="pill">{{ $offer->city->name }}</span><span class="pill">{{ $offer->category->name }}</span></div>
@if($offer->brochure)<a class="btn btn-outline" href="{{ \App\Support\Media::url($offer->brochure) }}">Preuzmi PDF brošuru ↓</a>@endif</section>
<section class="panel"><h2>Dojmovi posjetitelja</h2>
@forelse($offer->reviews as $review)<article class="review"><div class="review-head"><strong>{{ $review->user->name }}</strong><span class="rating">{{ str_repeat('★',$review->rating) }} <span>{{ $review->rating }}/5</span></span></div><span class="small muted">{{ $review->created_at->format('d.m.Y.') }}</span><p>{{ $review->comment }}</p>
@if(auth()->id() === $review->user_id || auth()->user()?->hasPermission('manage_catalog'))<form action="{{ route('reviews.destroy',$review) }}" method="post" data-confirm="Obrisati ovu recenziju?">@csrf @method('DELETE')<button class="text-link danger-link">Obriši recenziju</button></form>@endif</article>
@empty<p class="muted">Još nema recenzija. Podijeli prvi dojam nakon posjeta.</p>@endforelse
@if(auth()->user()?->hasPermission('plan_trip') && $offer->active)
@php($ownReview = $offer->reviews->firstWhere('user_id',auth()->id()))
<form method="post" action="{{ route('reviews.store',$offer) }}">@csrf<h3 style="margin-top:24px">{{ $ownReview ? 'Uredi svoj dojam' : 'Tvoj dojam' }}</h3><label for="rating">Ocjena</label><select id="rating" name="rating">@for($i=5;$i>=1;$i--)<option value="{{ $i }}" @selected(old('rating',$ownReview?->rating ?? 5)==$i)>{{ $i }} / 5</option>@endfor</select><label for="comment">Što bi izdvojio/la?</label><textarea id="comment" name="comment" required minlength="5" maxlength="1500">{{ old('comment',$ownReview?->comment) }}</textarea><button class="btn full">Spremi recenziju</button></form>
@else<p><a class="text-link" href="{{ route('login') }}">Prijavi se za ostavljanje dojma</a></p>@endif</section></div>
<aside><section class="panel"><span class="eyebrow">DODAJ U SVOJ PLAN</span><strong class="price-large">{{ (float)$offer->price === 0.0 ? 'Besplatno' : number_format($offer->price,2,',','.').' KM' }}</strong><span class="muted">{{ (float)$offer->price > 0 ? 'Ogledna cijena po osobi' : 'Samostalni posjet' }}</span><p style="margin-top:20px">Odaberi datum i broj osoba nakon dodavanja u plan.</p>
@if($offer->active)<form method="post" action="{{ route('trip.store',$offer) }}">@csrf<button class="btn btn-orange full">＋ Dodaj u moj plan</button></form>@endif
<p class="field-help" style="margin-top:15px">Plan služi organizaciji izleta. Dodavanje nije rezervacija niti kupnja.</p></section>
<section class="panel"><span class="eyebrow">PRIJE POLASKA</span><h3>{{ $offer->city->name }}</h3><p class="weather" data-weather="{{ route('weather',$offer->city) }}" role="status">Dohvaćam trenutačno vrijeme…</p><a class="small" href="https://open-meteo.com/" target="_blank" rel="noopener">Vrijeme: Open-Meteo ↗</a><hr style="border:0;border-top:1px solid var(--line);margin:20px 0"><p class="small muted">{{ $offer->city->description }}</p><a class="text-link" href="https://www.openstreetmap.org/?mlat={{ $offer->city->latitude }}&mlon={{ $offer->city->longitude }}#map=13/{{ $offer->city->latitude }}/{{ $offer->city->longitude }}" target="_blank" rel="noopener">Prikaži područje na karti ↗</a></section></aside></div>
@endsection
