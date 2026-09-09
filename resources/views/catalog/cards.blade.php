<div class="cards">
@forelse($offers as $offer)
<article class="offer-card">
    <a class="card-photo" href="{{ route('offers.show', $offer) }}"><img src="{{ \App\Support\Media::url($offer->image) }}" alt="{{ $offer->title }}" loading="lazy"><span class="category-badge">{{ $offer->category->name }}</span></a>
    <div class="card-body"><span class="location">↗ {{ $offer->city->name }} <span>· {{ $offer->duration }} h</span></span><h3><a href="{{ route('offers.show', $offer) }}">{{ $offer->title }}</a></h3><p>{{ \Illuminate\Support\Str::limit(strip_tags($offer->description), 87) }}</p>
    <div class="card-bottom"><div><strong>{{ (float)$offer->price === 0.0 ? 'Besplatno' : number_format($offer->price, 0, ',', '.').' KM' }}</strong><span>{{ (float)$offer->price > 0 ? '/ osoba' : 'za posjet' }}</span></div><a class="circle-link" aria-label="Detalji: {{ $offer->title }}" href="{{ route('offers.show', $offer) }}">↗</a></div>
    @if($offer->reviews_avg_rating)<div class="rating">★ {{ number_format($offer->reviews_avg_rating, 1) }} <span>ocjena posjetitelja</span></div>@endif
    </div>
</article>
@empty
<div class="empty-state"><h3>Nema ponuda za ove uvjete.</h3><p>Pokušaj odabrati drugi grad ili povećati iznos cijene.</p><a class="btn" href="{{ route('catalog') }}#ponuda">Prikaži sve ponude</a></div>
@endforelse
</div>
@if($offers->hasPages())<nav class="pagination" aria-label="Stranice ponuda">@if($offers->onFirstPage())<span>← Prethodna</span>@else<a href="{{ $offers->previousPageUrl() }}#ponuda">← Prethodna</a>@endif<strong>{{ $offers->currentPage() }} / {{ $offers->lastPage() }}</strong>@if($offers->hasMorePages())<a href="{{ $offers->nextPageUrl() }}#ponuda">Sljedeća →</a>@else<span>Sljedeća →</span>@endif</nav>@endif
