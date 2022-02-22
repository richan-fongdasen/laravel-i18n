<?php

namespace RichanFongdasen\I18n\Contracts;

use Illuminate\Support\Collection;
use RichanFongdasen\I18n\Locale;

interface LocaleRepository
{
    /**
     * Get all locale collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection;

    /**
     * Get the default locale.
     *
     * @throws \ErrorException
     *
     * @return Locale
     */
    public function default(): Locale;

    /**
     * Get locale based on the given key.
     *
     * @param string $key
     *
     * @return Locale|null
     */
    public function get(string $key): ?Locale;

    /**
     * Get locale keys in array based on the given attribute name.
     * If there was no attribute name specified, it will use
     * the default attribute name defined in config i18n.language_key.
     *
     * @param string|null $attributeName
     *
     * @return array|null
     */
    public function getKeys(?string $attributeName = null): ?array;
}
