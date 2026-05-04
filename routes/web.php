<?php

use App\Livewire\Agentes;
use App\Livewire\Ajustes;
use App\Livewire\Banners;
use App\Livewire\Bonos;
use App\Livewire\Caja;
use App\Livewire\Lineas;
use App\Livewire\LineDetail;
use App\Livewire\Logs;
use App\Livewire\Novedades;
use App\Livewire\Overview;
use App\Livewire\PlatformsMaster;
use App\Livewire\Promociones;
use App\Livewire\Reportes;
use App\Livewire\Sorteos;
use App\Livewire\Tickets;
use App\Livewire\UserBonos;
use App\Livewire\Users\UsersIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Switch active line (stores in session, no full reload needed)
Route::post('/session/line/{id}', function (int $id) {
    session(['active_line_id' => $id]);

    return back();
})->name('session.line');

// Dashboard – no line restriction (admin mode passes through)
Route::middleware('line.authorize')->group(function () {
    Route::get('/dashboard', Overview::class)->name('dashboard');
    Route::get('/clientes', UsersIndex::class)->name('clientes');
    Route::get('/usuarios', UsersIndex::class)->name('users.index');
    Route::get('/agentes', Agentes::class)->name('agentes');
    Route::get('/platforms', PlatformsMaster::class)->name('platforms.master');
    Route::get('/ajustes', Ajustes::class)->name('ajustes');
    Route::get('/reportes', Reportes::class)->name('reportes');
    Route::get('/logs', Logs::class)->name('logs');

    Route::get('/lineas', Lineas::class)->name('lineas');
    Route::get('/lineas/{id}', LineDetail::class)->name('lineas.detail');

    Route::middleware('line.authorize:promo.read')->group(function () {
        Route::get('/promociones', Promociones::class)->name('promociones');
    });

    Route::middleware('line.authorize:novedad.read')->group(function () {
        Route::get('/novedades', Novedades::class)->name('novedades');
    });

    Route::middleware('line.authorize:ticket.read')->group(function () {
        Route::get('/tickets', Tickets::class)->name('tickets');
    });

    Route::middleware('line.authorize:bonus.read')->group(function () {
        Route::get('/bonos', Bonos::class)->name('bonos');
        Route::get('/user-bonos', UserBonos::class)->name('user-bonos');
    });

    Route::middleware('line.authorize:sorteo.read')->group(function () {
        Route::get('/sorteos', Sorteos::class)->name('sorteos');
    });

    Route::get('/caja', Caja::class)->name('caja');

    Route::get('/banners', Banners::class)->name('banners');
});
