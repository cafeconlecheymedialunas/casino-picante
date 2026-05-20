<?php

use App\Http\Controllers\HomeSectionController;
use App\Http\Controllers\PerfilController;
use App\Livewire\Agentes;
use App\Livewire\Auth\AdminForgotPassword;
use App\Livewire\Auth\AdminResetPassword;
use App\Livewire\Auth\ClientForgotPassword;
use App\Livewire\Auth\ClientLogin;
use App\Livewire\Auth\ClientRegister;
use App\Livewire\Auth\ClientResetPassword;
use App\Livewire\Auth\Login;
use App\Livewire\BlogEdit;
use App\Livewire\Bonos;
use App\Livewire\Chats;
use App\Livewire\EditorHome;
use App\Livewire\Frontend\Blog;
use App\Livewire\Frontend\BonusesIndex;
use App\Livewire\Frontend\BonusShow;
use App\Livewire\Frontend\ClientAccount;
use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\LinePlatforms;
use App\Livewire\Frontend\LineShow;
use App\Livewire\Frontend\LinesIndex;
use App\Livewire\Frontend\PostShow;
use App\Livewire\Frontend\PublicRaffle;
use App\Livewire\Frontend\RaffleShow;
use App\Livewire\Frontend\VendorDetail;
use App\Livewire\Frontend\VendorsIndex;
use App\Livewire\Lineas;
use App\Livewire\LineDetail;
use App\Livewire\Novedades;
use App\Livewire\Overview;
use App\Livewire\PlatformsMaster;
use App\Livewire\Settings;
use App\Livewire\Sorteos;
use App\Livewire\Tickets;
use App\Livewire\Users\UsersIndex;
use App\Livewire\Ventas;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Vendor;
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', Login::class)
    ->name('admin.login')
    ->middleware('guest_or_agent');

Route::get('/admin/olvide-password', AdminForgotPassword::class)
    ->name('admin.password.request')
    ->middleware('guest_or_agent');

Route::get('/admin/restablecer-password/{token}', AdminResetPassword::class)
    ->name('admin.password.reset')
    ->middleware('guest_or_agent');

Route::get('/ingresar', ClientLogin::class)
    ->name('login')
    ->middleware('guest_or_agent');

Route::get('/registro', ClientRegister::class)
    ->name('client.register')
    ->middleware('guest_or_agent');

Route::get('/recuperar-password', ClientForgotPassword::class)
    ->name('client.password.request')
    ->middleware('guest_or_agent');

Route::get('/recuperar-password/{token}', ClientResetPassword::class)
    ->name('client.password.reset')
    ->middleware('guest_or_agent');

/*
|--------------------------------------------------------------------------
| Frontend
|--------------------------------------------------------------------------
*/

Route::get('/', Home::class)
    ->name('frontend.home');

/*
|--------------------------------------------------------------------------
| Cajeros
|--------------------------------------------------------------------------
*/

Route::get('/cajeros', VendorsIndex::class)
    ->name('frontend.cajeros');

Route::prefix('cajeros/{vendor:slug}')
    ->middleware('public.vendor')
    ->name('frontend.cajero.')
    ->group(function () {
        Route::get('/', VendorDetail::class)
            ->name('inicio');

        Route::get('/lineas', LinesIndex::class)
            ->name('lineas');

        Route::get('/lineas/{line}', LineShow::class)
            ->name('lineas.detalle');

        Route::get('/lineas/{line}/plataformas', LinePlatforms::class)
            ->name('lineas.plataformas');

        Route::get('/plataformas', LinePlatforms::class)
            ->name('plataformas');

        Route::get('/bonos', BonusesIndex::class)
            ->name('bonos');

        Route::get('/bonos/{bonusId}', BonusShow::class)
            ->name('bonos.detalle');

        Route::get('/sorteos', PublicRaffle::class)
            ->name('sorteos');

        Route::get('/sorteos/{raffleId}', RaffleShow::class)
            ->name('sorteos.detalle');

        Route::get('/blog', Blog::class)
            ->name('blog');

        Route::get('/blog/{slug}', PostShow::class)
            ->name('blog.detalle');

        Route::get('/agentes', function (App\Models\Vendor $vendor) {
            $agents = \App\Models\Agent::whereHas('lines', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })->get();

            return view('frontend.pages.vendor-agents', [
                'vendor' => $vendor,
                'agents' => $agents,
            ]);
        })->name('agentes');
    });

/*
|--------------------------------------------------------------------------
| Globales
|--------------------------------------------------------------------------
*/

Route::get('/lineas', LinesIndex::class)
    ->name('frontend.lineas');

Route::get('/lineas/{line}', LineShow::class)
    ->name('frontend.lineas.detalle');

Route::get('/lineas/{line}/plataformas', LinePlatforms::class)
    ->name('frontend.lineas.plataformas');

Route::get('/plataformas', LinePlatforms::class)
    ->name('frontend.plataformas');

Route::get('/bonos', BonusesIndex::class)
    ->name('frontend.bonos');

Route::get('/bonos/{bonusId}', BonusShow::class)
    ->name('frontend.bonos.detalle');

Route::get('/sorteos', PublicRaffle::class)
    ->name('frontend.sorteos');

Route::get('/sorteos/{raffleId}', RaffleShow::class)
    ->name('frontend.sorteos.detalle');

Route::get('/blog', Blog::class)
    ->name('frontend.blog');

Route::get('/blog/{slug}', PostShow::class)
    ->name('frontend.blog.detalle');

Route::get('/mi-cuenta', ClientAccount::class)
    ->middleware('auth')
    ->name('client.account');

Route::post('/salir', function () {
    Auth::logout();

    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('frontend.home');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/perfil', [PerfilController::class, 'index'])
            ->name('perfil');

        Route::put('/perfil', [PerfilController::class, 'update'])
            ->name('perfil.update');

        Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])
            ->name('perfil.password');
    });

    Route::post('/sesion/linea/{id}', function (int $id) {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if ($user->hasRole(Roles::ADMIN)) {
            if (! session('active_vendor_id')) {
                abort(403, 'Selecciona un cajero antes de operar lineas.');
            }

            if ($id !== 0) {
                Line::where('status', 'active')
                    ->where('vendor_id', session('active_vendor_id'))
                    ->findOrFail($id);
            }

            session(['active_line_id' => $id ?: null]);

            return back();
        }

        if ($user->hasRole(Roles::CAJERO)) {
            if ($id !== 0) {
                Line::where('status', 'active')
                    ->when(
                        $user->vendor_id,
                        fn ($q) => $q->where('vendor_id', $user->vendor_id)
                    )
                    ->findOrFail($id);
            }

            session(['active_line_id' => $id ?: null]);

            return back();
        }

        if (! $user->hasRole(Roles::AGENTE)) {
            abort(403, 'No tenes acceso al panel.');
        }

        $agentId = session('active_agent_id') ?: $user->agent?->id;

        if (
            ! $agentId ||
            ! LineAgent::where('agent_id', $agentId)
                ->where('line_id', $id)
                ->where('is_active', true)
                ->exists()
        ) {
            abort(403, 'No podes cambiar a una linea que no tenes asignada.');
        }

        session([
            'active_agent_id' => $agentId,
            'active_line_id' => $id,
        ]);

        return back();
    })->middleware('auth')->name('session.line');

    Route::post('/sesion/cajero/{id}', function (int $id) {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if (! $user->hasRole(Roles::ADMIN)) {
            abort(403, 'Solo el administrador puede cambiar de cajero.');
        }

        if ($id === 0) {
            session()->forget([
                'active_vendor_id',
                'active_line_id',
            ]);

            return redirect()->route('admin.cajeros');
        }

        Vendor::query()
            ->where('is_active', true)
            ->findOrFail($id);

        session([
            'active_vendor_id' => $id,
            'active_line_id' => null,
        ]);

        return back();
    })->middleware(['auth', 'admin'])->name('session.vendor');

    Route::middleware('line.authorize')->group(function () {
        Route::get('/dashboard', Overview::class)
            ->middleware('line.authorize:' . Permissions::DASHBOARD_READ)
            ->name('dashboard');

        Route::get('/clientes', UsersIndex::class)
            ->middleware('line.authorize:' . Permissions::USER_READ)
            ->name('clientes');

        Route::get('/usuarios', UsersIndex::class)
            ->middleware('line.authorize:' . Permissions::USER_READ)
            ->name('usuarios');

        Route::get('/agentes', Agentes::class)
            ->middleware('line.authorize:' . implode('|', [
                Permissions::AGENT_CREATE,
                Permissions::AGENT_ASSIGN,
                Permissions::AGENT_UPDATE,
                Permissions::AGENT_PERMISSIONS,
            ]))
            ->name('agentes');

        Route::get('/chats', Chats::class)
            ->middleware('line.authorize:' . implode('|', [
                Permissions::TICKET_READ,
                Permissions::USER_READ,
            ]))
            ->name('chats');

        Route::get('/plataformas', PlatformsMaster::class)
            ->middleware('panel.owner')
            ->name('plataformas');

        Route::get('/ventas/{line?}', Ventas::class)
            ->middleware('line.authorize:' . Permissions::LINE_EDIT)
            ->name('ventas');

        Route::get('/lineas', Lineas::class)
            ->middleware('line.authorize:' . Permissions::LINE_READ)
            ->name('lineas');

        Route::get('/lineas/{id}/editar', Lineas::class)
            ->middleware('line.authorize:' . Permissions::LINE_EDIT)
            ->name('lineas.editar');

        Route::get('/lineas/{id}', LineDetail::class)
            ->middleware('line.authorize')
            ->name('lineas.detalle');

        Route::get('/novedades', Novedades::class)
            ->middleware('line.authorize:' . Permissions::NEWS_READ)
            ->name('novedades');

        Route::get('/blog/{id}/editar', BlogEdit::class)
            ->middleware('line.authorize:' . Permissions::NEWS_UPDATE)
            ->name('blog.editar');

        Route::get('/bonos', Bonos::class)
            ->middleware('line.authorize:' . Permissions::BONO_READ)
            ->name('bonos');

        Route::match(['get', 'post'], '/editor-inicio', EditorHome::class)
            ->middleware('line.authorize:' . Permissions::HOME_EDIT)
            ->name('editor.inicio');

        Route::post('/editor-inicio/guardar-seccion', [HomeSectionController::class, 'saveSection'])
            ->middleware('line.authorize:' . Permissions::HOME_EDIT)
            ->name('editor.inicio.guardar');

        Route::get('/tickets', Tickets::class)
            ->middleware('line.authorize')
            ->name('tickets');

        Route::get('/sorteos', Sorteos::class)
            ->middleware('line.authorize:' . Permissions::SORTEO_READ)
            ->name('sorteos');

        Route::get('/configuracion', Settings::class)
            ->middleware('panel.owner')
            ->name('configuracion');
    });

    Route::get('/cajeros', \App\Livewire\Admin\Vendors\VendorsIndex::class)
        ->middleware('admin')
        ->name('cajeros');
});