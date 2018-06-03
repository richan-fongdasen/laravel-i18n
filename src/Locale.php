<?php

namespace RichanFongdasen\I18n;

class Locale
{
    /**
     * Country code
     *
     * @var string
     */
    protected $country;

    /**
     * IETF Code
     *
     * @var string
     */
    protected $ietfCode;

    /**
     * Language code
     *
     * @var string
     */
    protected $language;

    /**
     * Locale name
     *
     * @var string
     */
    protected $name;

    /**
     * Class constructor
     *
     * @param string $name
     * @param string $language
     * @param string $country
     */
    public function __construct($name, $language, $country)
    {
        $this->country = strtoupper($country);
        $this->language = strtolower($language);
        $this->name = $name;

        $this->ietfCode = $this->language . '-' . $this->country;
    }

    /**
     * Magic method to read data from inaccessible
     * properties
     *
     * @param  string $name
     * @return string
     */
    public function __get($name)
    {
        return $this->{$name};
    }

    /**
     * Magic method to determine if a variable is set
     * and is not NULL
     *
     * @param  string  $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->{$name});
    }
}
