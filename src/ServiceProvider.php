<?php

namespace RichanFongdasen\I18n;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
            realpath(__DIR__.'/../config/i18n.php') => config_path('i18n.php'),
        ], 'config');

        $this->publishes([
            realpath(__DIR__.'/../database/migrations/') => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            realpath(__DIR__.'/../storage/i18n/') => storage_path('i18n'),
        ], 'languages.json');

        Collection::macro('translate', function ($locale) {
            $this->each(function ($item, $key) use ($locale) {
                if (($item instanceof Model) && method_exists($item, 'translate')) {
                    $item->translate($locale);
                }
                return $key;
            });
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/i18n.php'), 'i18n');

        $this->app->singleton(I18nService::class, function () {
            return new I18nService(request());
        });
    }
}
