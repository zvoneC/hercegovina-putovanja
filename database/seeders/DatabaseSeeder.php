<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Offer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (['plan_trip' => 'Slaganje vlastitog plana i pisanje recenzija', 'manage_catalog' => 'Uređivanje gradova, kategorija i ponuda', 'manage_users' => 'Upravljanje korisnicima i dodjeljivanje uloga'] as $name => $description) {
                Permission::firstOrCreate(['name' => $name], ['description' => $description]);
            }
            foreach (['superadmin', 'admin', 'korisnik'] as $name) {
                $role = Role::firstOrCreate(['name' => $name]);
                $names = match ($name) {
                    'superadmin' => ['plan_trip', 'manage_catalog', 'manage_users'], 'admin' => ['plan_trip', 'manage_catalog'], default => ['plan_trip']
                };
                $role->permissions()->sync(Permission::whereIn('name', $names)->pluck('id'));
            }
            if (app()->environment(['local', 'testing'])) {
                foreach (['superadmin' => 'Voditelj projekta', 'admin' => 'Urednik ponuda', 'korisnik' => 'Demo putnik'] as $role => $name) {
                    $user = User::firstOrCreate(['email' => $role.'@hercegovina.test'], ['name' => $name, 'username' => $role, 'password' => 'Hercegovina2026!', 'role' => $role]);
                    $user->assignRole($role);
                }
            }
            $cities = [
                ['Mostar', 'Stari most, kamene ulice i Neretva čine Mostar polazištem za istraživanje Hercegovine.', 43.3438, 17.8078, 'mostar'],
                ['Ljubuški', 'Rijeka Trebižat, slapovi i mirna sela za dan proveden uz vodu.', 43.1969, 17.5456, 'kravica'],
                ['Čapljina', 'Počitelj i okolica Neretve spajaju povijesnu baštinu i otvorene krajolike.', 43.1125, 17.7148, 'pocitelj'],
                ['Trebinje', 'Kameni stari grad, platani i šetnice uz Trebišnjicu.', 42.7119, 18.3436, 'trebinje'],
                ['Neum', 'Morska obala Hercegovine za laganu šetnju i predah uz Jadran.', 42.9231, 17.6156, 'neum'],
                ['Konjic', 'Stara ćuprija i dolina Neretve, na putu prema planinama i izletištima.', 43.6513, 17.9608, 'konjic'],
                ['Stolac', 'Bregava, stare mlinice i kamena baština u mirnijem dijelu Hercegovine.', 43.0840, 17.9596, 'stolac'],
            ];
            foreach ($cities as [$name, $description, $latitude, $longitude, $image]) {
                City::firstOrCreate(['name' => $name], compact('description', 'latitude', 'longitude') + ['image' => 'images/'.$image.'.jpg']);
            }
            foreach (['Kultura i baština', 'Priroda i izleti', 'Aktivni odmor', 'Gastro doživljaj'] as $name) {
                Category::firstOrCreate(['name' => $name]);
            }
            // Cijene su za demonstraciju plana izleta, nisu cjenik stvarnih pružatelja usluge.
            $offers = [
                ['Konjic', 'Aktivni odmor', 'Dan uz Neretvu', 65, 6, 'konjic', 'Šetnja uz Neretvu i obilazak stare ćuprije. Prijedlog dana za manju grupu koja želi upoznati Konjic i njegovu okolicu.'],
                ['Stolac', 'Kultura i baština', 'Stolac: tragovima kamena', 20, 3, 'stolac', 'Obilazak povijesne jezgre Stoca uz priču o Bregavi i lokalnoj baštini. Ponesite udobnu obuću za kamene staze.'],
                ['Mostar', 'Gastro doživljaj', 'Okusi mostarske čaršije', 35, 2, 'mostar', 'Prijedlog gastro šetnje kroz stari dio Mostara. Vrijeme za domaću kavu, razgovor i upoznavanje lokalne kuhinje.'],
                ['Čapljina', 'Priroda i izleti', 'Dan u dolini Neretve', 30, 5, 'pocitelj', 'Lagani izlet kroz okolicu Čapljine s pauzama za fotografiranje i odmor. Plan je zamišljen za samostalnu organizaciju.'],
                ['Trebinje', 'Gastro doživljaj', 'Kava ispod platana', 15, 2, 'trebinje', 'Jutarnja šetnja Trebinjem i predah u hladu platana. U planu ostavite dovoljno vremena za gradsku tržnicu.'],
                ['Neum', 'Priroda i izleti', 'Neum: dan uz more', 0, 4, 'neum', 'Šetnja obalom i slobodno vrijeme za odmor uz Jadran. Hrana, prijevoz i eventualni sadržaji na plaži nisu uključeni.'],
                ['Stolac', 'Priroda i izleti', 'Šetnja uz Bregavu', 0, 2, 'stolac', 'Lagano upoznavanje Stoca uz rijeku Bregavu, mostove i mlinice. Izlet prilagodite vremenu i prohodnosti staza.'],
                ['Konjic', 'Kultura i baština', 'Konjic i stara ćuprija', 20, 2, 'konjic', 'Obilazak središta Konjica, stare ćuprije i ulica uz Neretvu. Kratki prijedlog za posjet na putovanju kroz Hercegovinu.'],
                ['Trebinje', 'Kultura i baština', 'Trebinje, polako', 25, 3, 'trebinje', 'Upoznajte stari grad i prošetajte obalom Trebišnjice. Ovaj izlet ostavlja prostor za pauzu i samostalno istraživanje.'],
                ['Čapljina', 'Kultura i baština', 'Kamene priče Počitelja', 15, 2, 'pocitelj', 'Prođite kamenim ulicama Počitelja i popnite se do pogleda na Neretvu. Na usponu ima stepenica; preporučuje se udobna obuća.'],
                ['Ljubuški', 'Priroda i izleti', 'Predah na Kravicama', 20, 4, 'kravica', 'Dan uz slapove Trebižata, u zelenilu i uz zvuk vode. Ogledna cijena nije službena cijena ulaznice; provjerite uvjete prije posjeta.'],
                ['Mostar', 'Kultura i baština', 'Mostar s druge strane mosta', 25, 3, 'mostar', 'Upoznajte Stari most, čaršiju i mirnije ulice uz Neretvu. Prijedlog pješačkog obilaska s dovoljno vremena za kavu i fotografije.'],
            ];
            foreach ($offers as [$city,$category,$title,$price,$duration,$image,$text]) {
                Offer::firstOrCreate(['title' => $title], [
                    'city_id' => City::where('name', $city)->value('id'), 'category_id' => Category::where('name', $category)->value('id'),
                    'price' => $price, 'duration' => $duration, 'image' => 'images/'.$image.'.jpg', 'description' => '<p>'.$text.'</p>',
                ]);
            }
        });
    }
}
