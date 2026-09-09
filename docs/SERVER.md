# Studentski poslužitelj

Objavljeno 9. rujna 2026. za grupu 04.

- Aplikacija: http://pzi042026.studenti.sum.ba
- GitHub: https://github.com/zvoneC/hercegovina-putovanja
- SSH: studenti.sum.ba, korisnički račun pzi042026
- Projekt: /home/pzi042026/hercegovina-putovanja
- Baza: MySQL, pzi042026, na localhost
- PHP: 8.2.2; Composer paketi instalirani bez razvojnih ovisnosti
- Javni direktorij: /home/pzi042026/public -> /home/pzi042026/hercegovina-putovanja/public

Prethodni javni direktorij bio je prazan i sačuvan je kao public-prazno-prije-objave. Nije brisan postojeći projekt ni baza. Račun za 2025. nije mijenjan.

## Postavke

Na serveru su APP_ENV=production, APP_DEBUG=false i DB_CONNECTION=mysql. Lozinke i ključ nalaze se samo u serverskoj .env datoteci. .env i bootstrap/cache/config.php imaju dozvole 640 i grupu www-data. storage i bootstrap/cache zapisivi su za web proces. Seeder u production okruženju stvara katalog i ovlasti, ali ne stvara lokalne demo račune.

Osobni pristup aplikaciji zapisan je u privatnoj datoteci PRISTUP-SERVER.txt na laptopu, izvan Git repozitorija i paketa za predaju. Dodatne račune kreira superadministrator kroz Korisnici i uloge; novi korisnik može se sam registrirati.

Studentska domena trenutačno radi preko HTTP-a; HTTPS certifikat ne odgovara domeni. Zato je SESSION_SECURE_COOKIE=false. Za javnu upotrebu izvan demonstracije administrator poslužitelja treba omogućiti valjan HTTPS certifikat, nakon čega treba promijeniti APP_URL i SESSION_SECURE_COOKIE=true.

## Ažuriranje

Nakon što se promjene pošalju na GitHub, spojiti se na račun i otvoriti bash:

    bash
    cd ~/hercegovina-putovanja
    git pull --ff-only
    composer install --no-dev --optimize-autoloader --no-interaction
    php artisan migrate --force
    php artisan config:cache
    php artisan view:cache
    chgrp www-data bootstrap/cache/config.php
    chmod 640 bootstrap/cache/config.php
    chgrp -R www-data storage bootstrap/cache
    chmod -R ug+rwX storage bootstrap/cache

Ne pokretati migrate:fresh na serveru: ta naredba briše tablice. Prije promjene strukture postojeće baze izraditi sigurnosnu kopiju. Za vraćanje početnih ponuda postoji php artisan db:seed --force; seeder ne prepisuje postojeće zapise.

Vue, Flatpickr i Quill već su u public/vendor: npm i dist nisu potrebni jer Laravel i Blade poslužuju cijelu aplikaciju. Nema odvojenog /backend servisa. Upload se poslužuje kontrolerom i storage:link nije potreban.

## Provjera objave

Prošle su MySQL migracije i početni podaci, javni katalog, prijava i administracija. Neprijavljen pristup /admin vraća 403, a /.env i /composer.json nisu javno dostupni. Lokalno prolazi 16 integracijskih testova sa 139 provjera; dodatno su pregledani mobilni prikaz, AJAX filtri i obrasci.
