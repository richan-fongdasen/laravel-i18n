<?php

namespace RichanFongdasen\I18n\Traits;

use Illuminate\Support\Facades\Log;
use RichanFongdasen\I18n\I18nService;

/**
 * LoadsTranslatedCachedRoutes.
 *
 * Add this trait to your App\RouteServiceProvider to load
 * translated cached routes for the active locale, instead
 * of the default locale's routes (irrespective of active).
 */
trait LoadsTranslatedCachedRoutes
{
    /**
     * Load the cached routes for the application.
     *
     * @return void
     */
    protected function loadCachedRoutes()
    {
        $localization = $this->getI18nService();

        $locale = $localization->routePrefix();

        // First, try to load the routes specifically cached for this locale
        // if they do not exist, write a warning to the log and load the default
        // routes instead. Note that this is guaranteed to exist, because the
        // 'cached routes' check in the Application checks its existence.

        $path = $this->makeLocaleRoutesPath($locale);

        if (!file_exists($path)) {

            Log::warning("Routes cached, but no cached routes found for locale '{$locale}'!");

            $path = $this->getDefaultCachedRoutePath();
        }

        $this->app->booted(function () use ($path) {
            require $path;
        });
    }

    /**
     * Returns the path to the cached routes file for a given locale.
     *
     * @param string   $locale
     * @param string[] $localeKeys
     *
     * @return string
     */
    protected function makeLocaleRoutesPath($locale = null)
    {
        $path = $this->getDefaultCachedRoutePath();
        if (!$locale) {
            return $path;
        }

        return substr($path, 0, -4).'_'.$locale.'.php';
    }

    /**
     * Returns the path to the standard cached routes file.
     *
     * @return string
     */
    protected function getDefaultCachedRoutePath()
    {
        return $this->app->getCachedRoutesPath();
    }

    /**
     * @return \RichanFongdasen\I18n\I18nService
     */
    protected function getI18nService()
    {
        return app(I18nService::class);
    }
}
