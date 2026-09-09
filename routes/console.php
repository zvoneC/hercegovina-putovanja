<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

Artisan::command('app:admin {email}', function () {
    $data = [
        'email' => $this->argument('email'),
        'name' => $this->ask('Ime i prezime'),
        'username' => $this->ask('Korisnicko ime'),
        'password' => $this->secret('Lozinka (najmanje 8 znakova)'),
    ];
    $validator = Validator::make($data, [
        'email' => 'required|email|unique:users',
        'name' => 'required|string|min:2|max:80',
        'username' => 'required|alpha_dash|min:3|max:40|unique:users',
        'password' => 'required|string|min:8|max:72',
    ]);
    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $message) {
            $this->error($message);
        }

        return 1;
    }
    DB::transaction(function () use ($data) {
        $user = User::create($data);
        $user->assignRole('superadmin');
    });
    $this->info('Superadministrator je kreiran.');

    return 0;
})->purpose('Kreiraj prvi administratorski racun bez javne zadane lozinke');
