<?php

use App\Http\Controllers\PerfilController;
use App\Livewire\Agentes;
use App\Livewire\Banners;
use App\Livewire\Bonos;
use App\Livewire\Chats;
use App\Livewire\EditorHome;
use App\Livewire\Lineas;
use App\Livewire\LineDetail;
use App\Livewire\Logs;
use App\Livewire\Novedades;
use App\Livewire\Overview;
use App\Livewire\PlatformsMaster;
use App\Livewire\Promociones;
use App\Livewire\Reportes;
use App\Livewire\Settings;
use App\Livewire\Sorteos;
use App\Livewire\Tickets;
use App\Livewire\Users\UsersIndex;
use App\Livewire\Ventas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::post('/logout', function () {
    Auth::logout();
    session()->flush();
    session()->regenerateToken();

    return redirect('/');
})->name('logout');

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
    Route::get('/chats', Chats::class)->name('chats');
    Route::get('/platforms', PlatformsMaster::class)->name('platforms.master');
    Route::get('/reportes', Reportes::class)->name('reportes');
    Route::get('/logs', Logs::class)->name('logs');
    Route::get('/ventas', Ventas::class)->name('ventas');

    Route::get('/lineas', Lineas::class)->name('lineas');
    Route::get('/lineas/{id}', LineDetail::class)->name('lineas.detail');

    Route::middleware('line.authorize:promo.read')->group(function () {
        Route::get('/promociones', Promociones::class)->name('promociones');
    });

    Route::middleware('line.authorize:novedad.read')->group(function () {
        Route::get('/novedades', Novedades::class)->name('novedades');
    });

    Route::middleware('line.authorize:bonus.read')->group(function () {
        Route::get('/bonos', Bonos::class)->name('bonos');
        Route::get('/editor-home', EditorHome::class)->name('editor-home');
    });

    Route::middleware('line.authorize:ticket.read')->group(function () {
        Route::get('/tickets', Tickets::class)->name('tickets');
    });

    Route::middleware('line.authorize:sorteo.read')->group(function () {
        Route::get('/sorteos', Sorteos::class)->name('sorteos');
    });

    Route::get('/banners', Banners::class)->name('banners');

    // Settings
    Route::get('/settings', Settings::class)->name('settings');

    // Perfil routes
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');
});
