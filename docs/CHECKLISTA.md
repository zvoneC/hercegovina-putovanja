# Checklist projekta

Stanje verzije koja je objavljena na studentskom serveru.

| Zahtjev | Stanje | Gdje / napomena |
|---|---|---|
| PHP framework | Da | Laravel 12; kontroleri, middleware i Blade prikazi. |
| MVC i ORM | Da | Eloquent modeli s relacijama; CRUD koristi ORM. |
| Baza i strani ključevi | Da | 10 domenskih tablica; migracije i početni podaci. |
| Uloge i dozvole | Da | 3 prijavljene uloge i gost; 4 role/permission tablice. |
| Prijava i registracija | Da | Sesija, hash lozinke, CSRF i provjera ovlasti. |
| CRUD | Da, uz ograničenje | Ponude, gradovi, kategorije, korisnici; vlastiti plan i recenzije. Uloge/dozvole su fiksno definirane. |
| Pretraga i barem 3 filtra | Da | Grad, kategorija, cijena i trajanje zajedno. |
| Web košarica | Prilagođeno temi | Moj plan sprema ponude, broj osoba i zbraja cijene; nema naručivanja. |
| Upload slike i PDF-a | Da | Administracija ponude; provjera formata i veličine. |
| Vanjski servis | Da | Open-Meteo; jasna poruka kada servis nije dostupan. |
| Ispis | Da | Prikaz plana s print CSS-om; PDF kroz ispis preglednika. |
| Upravljanje korisnicima | Da | Dodavanje, izmjena, deaktivacija i promjena uloge. |
| AJAX i Vue | Da | Vue upravlja asinkronom pretragom preko Fetch API-ja. |
| Pomagalo za sučelje | Da | Flatpickr odabir datuma. |
| WYSIWYG | Da | Quill za opis ponude, HTML se čisti na serveru. |
| Responzivnost | Da | Vlastiti CSS; provjeren desktop i uski prikaz. |
| Bootstrap tema | Ne | Korišten je vlastiti CSS bez Bootstrapa. |
| Git suradnja | Treba dovršiti | Objavljeno na zvoneC/hercegovina-putovanja; oba člana trebaju stvarno sudjelovati. |
| Studentski server | Da | pzi042026.studenti.sum.ba; PHP 8.2 i MySQL, migracije i prijava provjereni. |

Lista dodatnih funkcija pokriva 11 tehničkih stavki iz odjeljka 8 PDF-a, uz plan izleta kao prilagodbu košarice. Konačnu prihvatljivost tumačenja košarice i opseg CRUD-a provjerava nastavnik.
