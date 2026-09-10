@extends('layout')
@section('content')
<section class="hero">
    <div class="hero-copy"><span class="eyebrow">BOSNA I HERCEGOVINA / HERCEGOVINA</span><h1>Tvoj sljedeći izlet<br>je bliže nego misliš.</h1><p>Od Neretve do mora. Pronađi mjesto za svoj slobodan dan.</p><a href="#ponuda" class="hero-link">Istraži turističke ponude <span>↓</span></a></div>
    <div class="hero-photo"><img src="{{ asset('images/mostar.jpg') }}" alt="Stari most i Neretva u Mostaru"><span class="photo-caption">01 / MOSTAR · STARI MOST</span></div>
</section>
<div class="section-heading" id="ponuda"><div><span class="eyebrow">KRENI OD ONOGA ŠTO VOLIŠ</span><h2>Istraži Hercegovinu</h2></div><span class="muted">Tvoj tempo. Tvoj plan.</span></div>
<div id="catalog-app" class="catalog-layout" data-url="{{ route('catalog') }}">
    <aside class="filters">
        <form method="get" action="{{ route('catalog') }}" ref="filters" @submit.prevent="search">
            <div class="filter-heading"><h3>Pronađi izlet</h3><a href="{{ route('catalog') }}#ponuda">Poništi</a></div>
            <label for="q">Što želiš istražiti?</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Naziv ponude ili grada" maxlength="100">
            <label for="city">Grad / područje</label><select v-pre id="city" name="city"><option value="">Svi gradovi</option>@foreach($cities as $city)<option value="{{ $city->id }}" @selected(request('city') == $city->id)>{{ $city->name }}</option>@endforeach</select>
            <label for="category">Vrsta doživljaja</label><select v-pre id="category" name="category"><option value="">Sve kategorije</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach</select>
            <label for="price">Cijena do (KM / osoba)</label><input type="number" id="price" name="price" min="0" max="10000" step="0.01" value="{{ request('price') }}" placeholder="Bez ograničenja">
            <label for="duration">Koliko vremena imaš?</label><select id="duration" name="duration"><option value="">Bilo koje trajanje</option>@foreach([2 => 'Do 2 sata', 4 => 'Do 4 sata', 8 => 'Cijeli dan (do 8 sati)'] as $hours => $label)<option value="{{ $hours }}" @selected(request('duration') == $hours)>{{ $label }}</option>@endforeach</select>
            <label for="sort">Poredaj ponude</label><select id="sort" name="sort">@foreach(['newest'=>'Najnovije prvo','price'=>'Od najniže cijene','title'=>'Po nazivu'] as $value=>$label)<option value="{{ $value }}" @selected(request('sort') == $value)>{{ $label }}</option>@endforeach</select>
            <button class="btn full" :disabled="loading">Prikaži ponude <span>→</span></button>
        </form>
        <div class="plan-tip"><span class="eyebrow">BEZ ŽURBE</span><h3>Složi dan po svom.</h3><p>Spremi ponude u svoj plan, odaberi datum i ponesi ispis na put.</p><a href="{{ route('trip') }}">Otvori moj plan ↗</a></div>
    </aside>
    <section class="catalog-results" aria-label="Turističke ponude" :aria-busy="loading">
        <div class="results-bar"><span><strong v-text="count">{{ $offers->total() }}</strong> ponuda za istražiti</span><span class="small muted">Ogledne cijene u KM</span></div>
        <p v-if="error" v-text="error" class="notice error" role="alert" v-cloak></p>
        <p v-if="loading" v-cloak role="status">Učitavam ponude…</p>
        <div ref="results" data-results v-html="initialHtml">@include('catalog.cards')</div>
    </section>
</div>
@endsection
@push('scripts')<script src="{{ asset('vendor/vue.js') }}" defer></script><script src="{{ asset('js/catalog.js') }}" defer></script>@endpush
