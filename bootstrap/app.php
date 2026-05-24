<?php

use App\Http\Middleware\AuthorizeLine;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureAdminOrCajero;
use App\Http\Middleware\GuestOrAgent;
use App\Http\Middleware\IdentifyVendor;
use App\Http\Middleware\ResolvePublicVendor;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            IdentifyVendor::class,
        ]);
        $middleware->alias([
            'line.authorize' => AuthorizeLine::class,
            'guest_or_agent' => GuestOrAgent::class,
            'admin' => EnsureAdmin::class,
            'panel.owner' => EnsureAdminOrCajero::class,
            'public.vendor' => ResolvePublicVendor::class,
            'throttle.global' => ThrottleRequests::class.':60,1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
