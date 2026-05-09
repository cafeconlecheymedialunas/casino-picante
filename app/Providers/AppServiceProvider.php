<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // php artisan serve on Windows doesn't inherit TEMP/TMP, so tmpfile() falls back
        // to C:\WINDOWS which isn't writable. Fix by pointing to a writable tmp dir.
        // php artisan serve on Windows doesn't inherit TEMP/TMP, so tmpfile() falls back
        // to C:\WINDOWS which isn't writable. Point to the already-configured phptemp dir.
        if (PHP_OS_FAMILY === 'Windows' && !getenv('TEMP')) {
            putenv('TEMP=C:\\phptemp');
            putenv('TMP=C:\\phptemp');
        }
    }
}
