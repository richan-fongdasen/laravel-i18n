<?php

namespace RichanFongdasen\I18n;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            realpath(__DIR__ . '/../config/i18n.php') => config_path('i18n.php')
        ], 'config');

        $this->publishes([
            realpath(__DIR__ . '/../database/migrations/') => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            realpath(__DIR__ . '/../storage/i18n/') => storage_path('i18n')
        ], 'languages.json');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/i18n.php'), 'i18n');

        $this->app->singleton(I18nService::class, function () {
            return new I18nService(request());
        });
    }
}
