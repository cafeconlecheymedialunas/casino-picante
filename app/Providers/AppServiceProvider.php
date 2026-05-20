<?php

namespace App\Providers;

use App\Models\Agent;
use App\Models\Bonus;
use App\Models\Line;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\Sale;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use App\Policies\AgentPolicy;
use App\Policies\BonusPolicy;
use App\Policies\LinePolicy;
use App\Policies\PostPolicy;
use App\Policies\RafflePolicy;
use App\Policies\SalePolicy;
use App\Policies\TicketPolicy;
use App\Policies\UserPolicy;
use App\Policies\VendorPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        if (PHP_OS_FAMILY === 'Windows' && !getenv('TEMP')) {
            putenv('TEMP=C:\\phptemp');
            putenv('TMP=C:\\phptemp');
        }

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Line::class, LinePolicy::class);
        Gate::policy(Agent::class, AgentPolicy::class);
        Gate::policy(Vendor::class, VendorPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Bonus::class, BonusPolicy::class);
        Gate::policy(Raffle::class, RafflePolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);

        RateLimiter::for('global', function ($request) {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)
                : Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('panel-actions', function ($request) {
            return $request->user()
                ? Limit::perMinute(300)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });
    }
}
