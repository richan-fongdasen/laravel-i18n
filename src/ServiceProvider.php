<?php

namespace RichanFongdasen\I18n;

use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as Provider;
use RichanFongdasen\I18n\Contracts\LocaleRepository;
use RichanFongdasen\I18n\Contracts\TranslatableModel;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishAssets();
        $this->registerMacro();
    }

    /**
     * Publish package assets.
     *
     * @return void
     */
    protected function publishAssets(): void
    {
        $this->publishes([
            realpath(dirname(__DIR__).'/config/i18n.php') => config_path('i18n.php'),
        ], 'config');

        $this->publishes([
            realpath(dirname(__DIR__).'/database/migrations/') => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            realpath(dirname(__DIR__).'/storage/i18n/') => storage_path('i18n'),
        ], 'languages.json');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/i18n.php', 'i18n');

        $this->app->scoped(LocaleRepository::class, static function () {
            $allowedDriver = ['json', 'database'];
            $driver = (string) config('i18n.driver');

            if (!in_array($driver, $allowedDriver, true)) {
                throw new ErrorException('Invalid locale repository driver defined in config i18n.driver');
            }

            return ($driver === 'json') ? new JsonRepository() : new DatabaseRepository();
        });

        $this->app->scoped(I18nService::class, function () {
            return new I18nService(app(LocaleRepository::class), app(Request::class));
        });
    }

    /**
     * Register macro for Collection class.
     *
     * @return void
     */
    protected function registerMacro(): void
    {
        Collection::macro('translateTo', function ($locale) {
            $this->each(function ($item, $key) use ($locale) {
                if ($item instanceof TranslatableModel) {
                    $item->translateTo($locale);
                }

                return $key;
            });
        });
    }
}
