<?php

namespace RichanFongdasen\I18n;

class Locale
{
    /**
     * Country code.
     *
     * @readonly
     *
     * @var string
     */
    public string $country;

    /**
     * IETF Code.
     *
     * @readonly
     *
     * @var string
     */
    public string $ietfCode;

    /**
     * Language code.
     *
     * @readonly
     *
     * @var string
     */
    public string $language;

    /**
     * Locale name.
     *
     * @readonly
     *
     * @var string
     */
    public string $name;

    /**
     * Class constructor.
     *
     * @param string $name
     * @param string $language
     * @param string $country
     */
    public function __construct(string $name, string $language, string $country)
    {
        $this->country = strtoupper($country);
        $this->language = strtolower($language);
        $this->name = ucfirst($name);

        $this->ietfCode = $this->language.'-'.$this->country;
    }

    /**
     * Get locale key based on the attribute name defined in configuration:
     * i18n.language_key.
     *
     * @return string
     */
    public function getKey(): string
    {
        $keyName = config('i18n.language_key', 'language');

        return (string) data_get($this, $keyName);
    }
}
