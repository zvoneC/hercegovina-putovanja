@extends('layout')
@section('title','Administracija')
@section('content')
<div class="page-heading"><div><span class="eyebrow">UREĐIVANJE SADRŽAJA</span><h1>Administracija</h1></div><a class="btn" href="{{ route('admin.offers.create') }}">＋ Nova ponuda</a></div>@include('admin.nav')
<div class="stats">@foreach($stats as $label=>$value)<div class="stat"><strong>{{ $value }}</strong><span>{{ $label }}</span></div>@endforeach</div>
<section class="panel"><form method="get" class="actions"><label for="admin-search">Pretraži ponude</label><input id="admin-search" name="q" value="{{ request('q') }}" maxlength="100" style="max-width:400px" placeholder="Naziv ponude"><button class="btn btn-small">Traži</button></form>
<div class="table-wrap" style="margin-top:20px"><table><thead><tr><th>Ponuda</th><th>Grad / kategorija</th><th>Cijena</th><th>Status</th><th>Radnje</th></tr></thead><tbody>@forelse($offers as $offer)<tr><td><a href="{{ route('offers.show',$offer) }}">{{ $offer->title }}</a><small>{{ $offer->duration }} h</small></td><td>{{ $offer->city->name }}<small>{{ $offer->category->name }}</small></td><td>{{ number_format($offer->price,2,',','.') }} KM</td><td><span class="pill">{{ $offer->active ? 'Aktivna' : 'Arhivirana' }}</span></td><td><div class="actions"><a class="text-link" href="{{ route('admin.offers.edit',$offer) }}">Uredi</a>@if($offer->active)<form action="{{ route('admin.offers.destroy',$offer) }}" method="post" data-confirm="Arhivirati ovu ponudu? Nestat će iz javnog kataloga.">@csrf @method('DELETE')<button class="text-link danger-link">Arhiviraj</button></form>@endif</div></td></tr>@empty<tr><td colspan="5">Nema pronađenih ponuda.</td></tr>@endforelse</tbody></table></div>
@include('partials.pagination',['page'=>$offers])</section>
@endsection
