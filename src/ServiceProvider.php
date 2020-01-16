<?php

namespace RichanFongdasen\I18n;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as Provider;
use RichanFongdasen\I18n\Commands\RouteTranslationsCacheCommand;
use RichanFongdasen\I18n\Commands\RouteTranslationsClearCommand;
use RichanFongdasen\I18n\Commands\RouteTranslationsListCommand;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishAssets();
        $this->registerMacro();
        $this->registerCommands();
    }

    /**
     * Publish package assets.
     *
     * @return void
     */
    protected function publishAssets()
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

    /**
     * Register macro for Collection class.
     *
     * @return void
     */
    protected function registerMacro()
    {
        Collection::macro('translate', function ($locale) {
            $this->each(function ($item, $key) use ($locale) {
                if (($item instanceof Model) && method_exists($item, 'translate')) {
                    $item->translate($locale);
                }

                return $key;
            });
        });
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RouteTranslationsCacheCommand::class,
                RouteTranslationsClearCommand::class,
                RouteTranslationsListCommand::class,
            ]);
        }
    }
}
