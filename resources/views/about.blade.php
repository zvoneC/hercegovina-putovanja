@extends('layout')
@section('title','O projektu')
@section('content')
<article class="content-narrow"><span class="eyebrow">HERCEGOVINA PUTOVANJA</span><h1>Mjesta koja vrijedi upoznati.</h1><p>Hercegovina putovanja je studentska aplikacija za pregled turističkih ponuda i organizaciju vlastitog izleta. Posjetitelji mogu istraživati gradove, pretraživati ponude i filtrirati ih prema interesima, cijeni i trajanju.</p>
<h2>Kako koristiti aplikaciju</h2><ol><li>Pronađi ponudu koja ti odgovara.</li><li>Registriraj se i dodaj ponudu u svoj plan.</li><li>U planu odaberi datum, broj osoba i bilješku te spremi izmjene.</li><li>Otvori prikaz za ispis i ispiši plan ili ga spremi kao PDF.</li><li>Nakon posjeta ostavi ili uredi svoj dojam.</li></ol>
<h2>O podacima</h2><p>Ovo je demonstracijski projekt kolegija Programiranje za internet, FPMOZ. Nazivi i opisi ponuda sastavljeni su za demonstraciju. Cijene nisu službeni cjenici, a aplikacija ne obrađuje plaćanja niti potvrđuje rezervacije. Uvjeti posjeta provjeravaju se kod pružatelja usluge.</p>
<h2>Tim i tehnologije</h2><p>Projektna tema: Aplikacija za turističku ponudu Hercegovine. Tim: Radmila Milidragović i Zvonimir Ćavar.</p><p>Aplikacija koristi PHP i Laravel, Eloquent ORM, Blade prikaze i Vue za asinkronu pretragu. Baza za lokalnu demonstraciju je SQLite, a migracije su pripremljene i za MySQL.</p>
<h2>Izvori</h2><p>Projektni zahtjevi temelje se na <a href="https://github.com/RobertRozic/PZI">materijalima kolegija</a> i dostavljenim PDF uputama. Vremenski podaci dolaze iz servisa <a href="https://open-meteo.com/">Open-Meteo</a>, a karta područja koristi <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>.</p>
<p>Fotografije su preuzete s Wikimedia Commonsa preko povezanih članaka Wikipedije. Detaljni podaci o autorima, licencama i izvornim datotekama nalaze se u <a href="{{ asset('photo-credits.html') }}">popisu fotografija</a>.</p>
</article>
@endsection
