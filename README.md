# Hercegovina putovanja

Studentski projekt: **Aplikacija za turističku ponudu Hercegovine**, PZI, grupa 04.

**Aplikacija:** http://pzi042026.studenti.sum.ba  
**Članovi tima:** Zvonimir Ćavar i Radmila Milidragović

Katalog 7 hercegovačkih gradova i 12 oglednih ponuda, višestruki filtri, registracija i prijava, tri uloge, administracija, plan izleta, recenzije, ispis, upload slike/PDF-a i vremenski podaci. Ponude i cijene služe demonstraciji; plan izleta nije rezervacija niti naplata.

## Pokretanje iz Git repozitorija

Potrebni su PHP 8.2+ i Composer. PHP treba PDO/SQLite za lokalnu bazu, odnosno PDO/MySQL za server, te mbstring, openssl, fileinfo, DOM/XML, cURL i ZIP.

    git clone https://github.com/zvoneC/hercegovina-putovanja.git
    cd hercegovina-putovanja
    composer install
    cp .env.example .env
    php artisan key:generate
    php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
    php artisan migrate --seed
    php artisan serve --host=127.0.0.1 --port=8017

Na Windowsu za kopiranje može poslužiti `Copy-Item .env.example .env`. Otvoriti http://127.0.0.1:8017. Vue, Quill i Flatpickr su lokalni; npm build nije potreban.

Pripremljeni Windows paket ima PHP i sve pakete: raspakirati cijeli ZIP pa otvoriti `Pokreni.cmd` u njegovoj glavnoj mapi. Pokretač ne briše postojeće podatke.

## Lokalni demo računi

Sva tri imaju lozinku **Hercegovina2026!**. Ovi računi se ne stvaraju na produkcijskom serveru.

| Email | Ovlasti |
|---|---|
| superadmin@hercegovina.test | Sve, uključujući korisnike i dodjelu uloga |
| admin@hercegovina.test | Ponude, gradovi, kategorije i moderiranje recenzija |
| korisnik@hercegovina.test | Vlastiti plan i recenzije |

Na serveru se koristi zaseban pristup. Novi korisnici mogu se registrirati.

## Kod i dokumentacija

- `app/Models` — Eloquent modeli, relacije i filtri
- `app/Http/Controllers` — obrada zahtjeva i CRUD
- `app/Http/Middleware/CheckPermission.php` — provjera ovlasti
- `app/Support` — čišćenje HTML-a i putanje medija
- `resources/views` — Blade prikazi
- `public/css` i `public/js` — stilovi i JavaScript
- `database/migrations` i `database/seeders` — tablice i početni podaci
- [Dokumentacija](docs/Dokumentacija.docx) — vizija, tehnička i korisnička dokumentacija
- [Checklist](docs/CHECKLISTA.md) — ostvareni zahtjevi i preostale obveze
- [Server](docs/SERVER.md) — stvarna konfiguracija i ažuriranje
- [Izvori fotografija](public/photo-credits.html) — autori i licence

## Provjere

    php artisan test

Prolazi 16 integracijskih testova sa 139 provjera. Testovi koriste zasebnu SQLite bazu u memoriji. Provjereni su i desktop/mobilni prikaz, AJAX filtri, obrasci te objava na studentskom serveru s MySQL bazom.

## Ograničenja i predaja

Uloge i dozvole su unaprijed definirane; nema posebnog CRUD sučelja za njih. Nema plaćanja i potvrđenih rezervacija. Korišten je vlastiti CSS bez Bootstrapa.
