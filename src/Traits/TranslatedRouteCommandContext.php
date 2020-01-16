<?php

namespace RichanFongdasen\I18n\Traits;

use RichanFongdasen\I18n\I18nService;

trait TranslatedRouteCommandContext
{
    /**
     * Returns whether a given locale is supported.
     *
     * @param string $locale
     *
     * @return bool
     */
    protected function isSupportedLocale($locale)
    {
        return $this->getI18nService()->getLocale($locale) !== null;
    }

    /**
     * @return string[]
     */
    protected function getSupportedLocales()
    {
        return $this->getI18nService()->getLocale()->toArray();
    }

    /**
     * @return \RichanFongdasen\I18n\I18nService
     */
    protected function getI18nService()
    {
        return app(I18nService::class);
    }

    /**
     * @return string
     */
    protected function getBootstrapPath()
    {
        if (method_exists($this->laravel, 'bootstrapPath')) {
            return $this->laravel->bootstrapPath();
        }

        return $this->laravel->basePath().DIRECTORY_SEPARATOR.'bootstrap';
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    protected function makeLocaleRoutesPath($locale = null)
    {
        $path = $this->laravel->getCachedRoutesPath();
        if (!$locale) {
            return $path;
        }
        $path = substr($path, 0, -4).'_'.$locale.'.php';

        return $path;
    }

    protected function getLocaleEnvKey()
    {
        return I18nService::ENV_ROUTE_KEY;
    }
}
