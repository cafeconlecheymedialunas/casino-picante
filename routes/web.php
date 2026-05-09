<?php

use App\Http\Controllers\PerfilController;
use App\Livewire\Agentes;
use App\Livewire\Auth\ClientLogin;
use App\Livewire\Auth\Login;
use App\Livewire\Banners;
use App\Livewire\Bonos;
use App\Livewire\Chats;
use App\Livewire\EditorHome;
use App\Livewire\Lineas;
use App\Livewire\LineDetail;
use App\Livewire\Novedades;
use App\Livewire\Overview;
use App\Livewire\PlatformsMaster;
use App\Livewire\Promociones;
use App\Livewire\Settings;
use App\Livewire\Sorteos;
use App\Livewire\Tickets;
use App\Livewire\Users\UsersIndex;
use App\Livewire\Ventas;
use App\Models\Line;
use App\Models\LineAgent;
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', Login::class)->name('admin.login')->middleware('guest_or_agent');
Route::get('/login', ClientLogin::class)->name('login')->middleware('guest_or_agent');
Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/logout', function () {
    Auth::logout();
    session()->flush();
    session()->regenerateToken();

    return redirect()->route('admin.login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');
});

// Switch active line only to a line the current panel user can access.
Route::post('/session/line/{id}', function (int $id) {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('admin.login');
    }

    if ($user->hasRole(Roles::ADMIN)) {
        if ($id !== 0) {
            Line::findOrFail($id);
        }

        session(['active_line_id' => $id ?: null]);

        return back();
    }

    if (! $user->hasRole(Roles::AGENTE)) {
        abort(403, 'No tenes acceso al panel.');
    }

    $agentId = session('active_agent_id') ?: $user->agent?->id;
    if (! $agentId || ! LineAgent::where('agent_id', $agentId)->where('line_id', $id)->where('is_active', true)->exists()) {
        abort(403, 'No podes cambiar a una linea que no tenes asignada.');
    }

    session(['active_agent_id' => $agentId]);
    session(['active_line_id' => $id]);

    return back();
})->middleware('auth')->name('session.line');

// Dashboard – no line restriction (admin mode passes through)
Route::middleware('line.authorize')->group(function () {
    Route::get('/dashboard', Overview::class)->middleware('admin')->name('dashboard');
    Route::get('/clientes', UsersIndex::class)->middleware('line.authorize:'.Permissions::USER_READ)->name('clientes');
    Route::get('/usuarios', UsersIndex::class)->middleware('line.authorize:'.Permissions::USER_READ)->name('users.index');
    Route::get('/agentes', Agentes::class)->middleware('line.authorize:'.implode('|', [
        Permissions::AGENT_CREATE,
        Permissions::AGENT_ASSIGN,
        Permissions::AGENT_UPDATE,
        Permissions::AGENT_PERMISSIONS,
    ]))->name('agentes');
    Route::get('/chats', Chats::class)->middleware('line.authorize:'.implode('|', [
        Permissions::TICKET_READ,
        Permissions::USER_READ,
    ]))->name('chats');
    Route::get('/platforms', PlatformsMaster::class)->middleware('admin')->name('platforms.master');
    Route::get('/ventas/{line?}', Ventas::class)->middleware('line.authorize:'.Permissions::LINE_EDIT)->name('ventas');

    Route::get('/lineas', Lineas::class)->middleware('line.authorize:'.Permissions::LINE_READ)->name('lineas');
    Route::get('/lineas/{id}', LineDetail::class)->middleware('line.authorize')->name('lineas.detail');

    Route::middleware('line.authorize:'.Permissions::PROMO_READ)->group(function () {
        Route::get('/promociones', Promociones::class)->name('promociones');
    });

    Route::middleware('line.authorize:'.Permissions::NEWS_READ)->group(function () {
        Route::get('/novedades', Novedades::class)->name('novedades');
    });

    Route::middleware('line.authorize:'.Permissions::BONO_READ)->group(function () {
        Route::get('/bonos', Bonos::class)->name('bonos');
    });

    Route::get('/editor-home', EditorHome::class)->middleware('line.authorize:'.Permissions::HOME_EDIT)->name('editor-home');

    Route::get('/tickets', Tickets::class)->middleware('line.authorize')->name('tickets');

    Route::middleware('line.authorize:'.Permissions::SORTEO_READ)->group(function () {
        Route::get('/sorteos', Sorteos::class)->name('sorteos');
    });

    Route::get('/banners', Banners::class)->middleware('line.authorize:'.Permissions::LINE_EDIT)->name('banners');

    // Settings – admin only
    Route::get('/settings', Settings::class)->middleware('admin')->name('settings');

});
