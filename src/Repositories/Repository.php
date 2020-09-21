<?php

namespace RichanFongdasen\I18n\Repositories;

use Illuminate\Support\Collection;
use RichanFongdasen\I18n\Locale;

abstract class Repository
{
    /**
     * Language data source.
     *
     * @var string
     */
    protected $datasource;

    /**
     * Class constructor.
     *
     * @param string $datasource
     */
    public function __construct($datasource)
    {
        $this->datasource = $datasource;
    }

    /**
     * Get language collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collect(): Collection
    {
        $collection = collect();

        foreach ($this->read() as $locale) {
            $collection->put(
                $locale->language,
                new Locale($locale->name, $locale->language, $locale->country)
            );
        }

        return $collection;
    }

    /**
     * Read the datasource.
     *
     * @return mixed
     */
    abstract protected function read();
}
