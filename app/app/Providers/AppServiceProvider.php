<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Suprime E_WARNING do tempnam() em php-font-lib (compatibilidade PHP 8.4 + DomPDF)
        // O aviso ocorre quando sys_get_temp_dir() e o diretorio configurado diferem,
        // mas o arquivo e criado com sucesso — nao e um erro funcional.
        $this->app->booting(function () {
            set_error_handler(function (int $errno, string $errstr) {
                if ($errno === E_WARNING && str_contains($errstr, 'tempnam()')) {
                    return true;
                }
                return false;
            }, E_WARNING);
        });
    }

    public function boot(): void
    {
        //
    }
}
