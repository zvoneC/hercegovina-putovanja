<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog');
Route::get('/gradovi', [CatalogController::class, 'cities'])->name('cities');
Route::get('/ponude/{offer}', [CatalogController::class, 'show'])->name('offers.show');
Route::get('/vrijeme/{city}', [WeatherController::class, 'show'])->middleware('throttle:30,1')->name('weather');
Route::get('/mediji/{file}', [MediaController::class, 'show'])->name('media');
Route::view('/o-projektu', 'about')->name('about');
Route::middleware('guest')->group(function () {
    Route::view('/prijava', 'auth.login')->name('login');
    Route::post('/prijava', [AuthController::class, 'login'])->middleware('throttle:8,1');
    Route::view('/registracija', 'auth.register')->name('register');
    Route::post('/registracija', [AuthController::class, 'register'])->middleware('throttle:5,1');
});
Route::post('/odjava', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::middleware(['auth', 'permission:plan_trip'])->group(function () {
    Route::get('/moj-plan', [TripController::class, 'index'])->name('trip');
    Route::post('/moj-plan/{offer}', [TripController::class, 'store'])->name('trip.store');
    Route::put('/moj-plan/{item}', [TripController::class, 'update'])->name('trip.update');
    Route::delete('/moj-plan/{item}', [TripController::class, 'destroy'])->name('trip.destroy');
    Route::get('/ispis-plana', [TripController::class, 'print'])->name('trip.print');
    Route::post('/ponude/{offer}/recenzije', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/recenzije/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});
Route::middleware('permission:manage_catalog')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/ponude/nova', [AdminController::class, 'offerForm'])->name('offers.create');
    Route::get('/ponude/{offer}/uredi', [AdminController::class, 'offerForm'])->name('offers.edit');
    Route::post('/ponude', [AdminController::class, 'saveOffer'])->name('offers.store');
    Route::put('/ponude/{offer}', [AdminController::class, 'saveOffer'])->name('offers.update');
    Route::delete('/ponude/{offer}', [AdminController::class, 'deleteOffer'])->name('offers.destroy');
    Route::get('/sifrarnici', [AdminController::class, 'lists'])->name('lists');
    Route::post('/gradovi', [AdminController::class, 'saveCity'])->name('cities.store');
    Route::put('/gradovi/{city}', [AdminController::class, 'saveCity'])->name('cities.update');
    Route::delete('/gradovi/{city}', [AdminController::class, 'deleteCity'])->name('cities.destroy');
    Route::post('/kategorije', [AdminController::class, 'saveCategory'])->name('categories.store');
    Route::put('/kategorije/{category}', [AdminController::class, 'saveCategory'])->name('categories.update');
    Route::delete('/kategorije/{category}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');
    Route::middleware('permission:manage_users')->group(function () {
        Route::get('/korisnici', [AdminController::class, 'users'])->name('users');
        Route::post('/korisnici', [AdminController::class, 'saveUser'])->name('users.store');
        Route::put('/korisnici/{user}', [AdminController::class, 'saveUser'])->name('users.update');
        Route::delete('/korisnici/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    });
});
