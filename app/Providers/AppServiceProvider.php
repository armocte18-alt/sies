<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Política de contraseñas reforzada para un sistema institucional.
        // No se usa ->uncompromised() porque el servidor corre en una intranet
        // sin salida a internet y esa validación depende de la API pública
        // de HaveIBeenPwned.
        Password::defaults(fn () => Password::min(10)->mixedCase()->numbers()->symbols());

        // Detrás de un proxy/VirtualHost de Apache la app puede recibir
        // peticiones como HTTP internamente; si algún día se sirve por HTTPS
        // (proxy TLS), esto evita que Laravel genere URLs http:// mixtas.
        if (! $this->app->environment('local')) {
            URL::forceScheme(str(config('app.url'))->startsWith('https') ? 'https' : 'http');
        }
    }
}
