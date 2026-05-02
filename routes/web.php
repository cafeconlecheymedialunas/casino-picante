<?php

use App\Livewire\Agentes;
use App\Livewire\Ajustes;
use App\Livewire\Banners;
use App\Livewire\Bonos;
use App\Livewire\Caja;
use App\Livewire\Juegos;
use App\Livewire\Lineas;
use App\Livewire\Logs;
use App\Livewire\Novedades;
use App\Livewire\Overview;
use App\Livewire\Promociones;
use App\Livewire\Reportes;
use App\Livewire\Tickets;
use App\Livewire\Users\UsersIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', Overview::class)->name('dashboard');
Route::get('/usuarios', UsersIndex::class)->name('users.index');
Route::get('/agentes', Agentes::class)->name('agentes');
Route::get('/promociones', Promociones::class)->name('promociones');
Route::get('/lineas', Lineas::class)->name('lineas');
Route::get('/tickets', Tickets::class)->name('tickets');
Route::get('/novedades', Novedades::class)->name('novedades');
Route::get('/ajustes', Ajustes::class)->name('ajustes');
Route::get('/caja', Caja::class)->name('caja');
Route::get('/bonos', Bonos::class)->name('bonos');
Route::get('/juegos', Juegos::class)->name('juegos');
Route::get('/banners', Banners::class)->name('banners');
Route::get('/reportes', Reportes::class)->name('reportes');
Route::get('/logs', Logs::class)->name('logs');
