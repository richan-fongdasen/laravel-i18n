<?php

namespace RichanFongdasen\I18n;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use RichanFongdasen\I18n\Exceptions\InvalidFallbackLanguageException;
use RichanFongdasen\I18n\Exceptions\InvalidLocaleException;

class I18nService
{
    /**
     * I18n configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Default locale key.
     *
     * @var string
     */
    protected $defaultKey;

    /**
     * Locale collection object.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $locale;

    /**
     * All of possible locale keys.
     *
     * @var array
     */
    protected $possibleKeys = ['ietfCode', 'language'];

    /**
     * HTTP Request Object.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * URL Generator object.
     *
     * @var \RichanFongdasen\I18n\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->loadConfig();

        $this->defaultKey = $this->getConfig('language_key');

        $this->locale = $this->loadLocale();

        $this->urlGenerator = new UrlGenerator($this, $this->defaultKey);
    }

    /**
     * Get default locale.
     *
     * @throws InvalidFallbackLanguageException
     *
     * @return \RichanFongdasen\I18n\Locale
     */
    public function defaultLocale(): Locale
    {
        $fallback = $this->getConfig('fallback_language');
        $locale = $this->getLocale($fallback);

        if (!$locale instanceof Locale) {
            throw new InvalidFallbackLanguageException('Can\'t find the fallback locale object');
        }

        return $locale;
    }

    /**
     * Format the IETF locale string.
     *
     * @param string $string
     *
     * @return string
     */
    protected function formatIetf(string $string): string
    {
        return str_replace('_', '-', $string);
    }

    /**
     * Get configuration value for a specific key.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Get any locale matched to the given keyword.
     * It will return all available locales when
     * there is no keyword.
     *
     * @param string|null $keyword
     *
     * @return mixed
     */
    public function getLocale(?string $keyword = null)
    {
        if ($keyword === null) {
            return $this->locale;
        }
        $keyword = $this->formatIetf($keyword);

        foreach ($this->possibleKeys as $key) {
            if ($locale = $this->locale->keyBy($key)->get($keyword)) {
                return $locale;
            }
        }

        return null;
    }

    /**
     * Get all of available locale keys.
     *
     * @param string|null $key
     *
     * @return null|array
     */
    public function getLocaleKeys(?string $key = null): ?array
    {
        if (empty($key)) {
            $key = $this->defaultKey;
        }
        $keys = $this->locale->keyBy($key)->keys()->all();

        if ((count($keys) == 1) && empty($keys[0])) {
            return null;
        }

        return $keys;
    }

    /**
     * Load I18n configurations.
     *
     * @return void
     */
    public function loadConfig(): void
    {
        $this->config = \Config::get('i18n');
    }

    /**
     * Load locale from repository.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function loadLocale(): Collection
    {
        $cacheKey = 'laravel-i18n-locale-'.$this->getConfig('driver');
        $duration = $this->getConfig('cache_duration', 86400);
        $ttl = Carbon::now()->addSeconds($duration);

        return \Cache::remember($cacheKey, $ttl, function () {
            return app(RepositoryManager::class)->collect();
        });
    }

    /**
     * Get the current routed locale.
     *
     * @param null|\Illuminate\Http\Request $request
     *
     * @return \RichanFongdasen\I18n\Locale|null
     */
    public function routedLocale(Request $request = null): ?Locale
    {
        if (!$request) {
            $request = $this->request;
        }

        $index = $this->getConfig('locale_url_segment');
        $language = $request->segment($index);
        if (empty($language)) {
            return null;
        }

        if ($locale = $this->getLocale($language)) {
            \App::setLocale($locale->{$this->defaultKey});
        }

        return $locale;
    }

    /**
     * Get the route prefix.
     *
     * @return string
     */
    public function routePrefix(): string
    {
        $locale = $this->routedLocale() ? $this->routedLocale() : $this->defaultLocale();

        return $locale->{$this->defaultKey};
    }

    /**
     * Generate a localized URL for the application.
     *
     * @param string     $url
     * @param mixed|null $locale
     *
     * @throws InvalidLocaleException|InvalidFallbackLanguageException
     *
     * @return string
     */
    public function url(string $url, $locale = null): string
    {
        if (is_string($locale) && !($locale = $this->getLocale($locale))) {
            throw new InvalidLocaleException('Failed to generate URL with the given locale');
        }
        if (($locale === null) && !($locale = $this->routedLocale())) {
            $locale = $this->defaultLocale();
        }

        return $this->urlGenerator->setUrl($url)->localize($locale);
    }
}
