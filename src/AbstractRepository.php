<?php

namespace RichanFongdasen\I18n;

use Carbon\Carbon;
use ErrorException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RichanFongdasen\I18n\Contracts\LocaleRepository;
use Traversable;

abstract class AbstractRepository implements LocaleRepository
{
    /**
     * The default locale to be used as fallback locale.
     *
     * @var Locale
     */
    protected Locale $defaultLocale;

    /**
     * Locale collection instance.
     *
     * @var Collection
     */
    protected Collection $localeCollection;

    /**
     * Define all the possible keys to identify a locale.
     *
     * @var string[]
     */
    static protected array $possibleKeys = [
        'language',
        'ietfCode',
    ];

    /**
     * Locale Repository Constructor.
     *
     * @throws ErrorException
     */
    public function __construct()
    {
        $this->loadLocale();
        $this->initialize();
    }

    /**
     * Get all locale collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection
    {
        return $this->localeCollection;
    }

    /**
     * Get the default locale.
     *
     * @return Locale
     */
    public function default(): Locale
    {
        return $this->defaultLocale;
    }

    /**
     * Get locale based on the given key.
     *
     * @param string $key
     * @return Locale|null
     */
    public function get(string $key): ?Locale
    {
        foreach (self::$possibleKeys as $keyName) {
            $locale = $this->localeCollection->keyBy($keyName)->get($key);

            if ($locale instanceof Locale) {
                return $locale;
            }
        }

        return null;
    }

    /**
     * Get locale keys in array based on the given attribute name.
     * If there was no attribute name specified, it will use
     * the default attribute name defined in config i18n.language_key
     *
     * @param string|null $attributeName
     * @return array|null
     */
    public function getKeys(?string $attributeName = null): ?array
    {
        if ($attributeName === null) {
            $attributeName = (string) config('i18n.language_key');
        }

        $keys = $this->localeCollection->keyBy($attributeName)->keys()->all();

        if ((count($keys) === 1) && ($keys[0] === '')) {
            return null;
        }

        return $keys;
    }

    /**
     * Initialize the Locale Repository.
     *
     * @return void
     * @throws ErrorException
     */
    protected function initialize(): void
    {
        $locale = $this->get((string) config('app.locale'));

        if (!($locale instanceof Locale)) {
            throw new ErrorException('Invalid fallback language, defined in config app.locale');
        }

        $this->defaultLocale = $locale;
    }

    /**
     * Load the locale from the respective datasource.
     *
     * @return void
     */
    protected function loadLocale(): void
    {
        $this->localeCollection = new Collection();

        $repo = $this;
        $callback = static function () use ($repo) {
            return $repo->readDataSource();
        };
        $key = 'laravel-i18n-datasource-cache';
        $ttl = Carbon::now()->addMonth();

        $data = (bool) config('i18n.enable_cache') === true ? Cache::remember($key, $ttl, $callback) : $callback();

        foreach ($data as $locale) {
            $this->localeCollection->put(
                strtolower(data_get($locale, 'language')),
                new Locale(
                    data_get($locale, 'name'),
                    data_get($locale, 'language'),
                    data_get($locale, 'country')
                )
            );
        }
    }

    /**
     * Get all registered locale from the datasource.
     *
     * @return Traversable
     */
    abstract public function readDataSource(): Traversable;
}
