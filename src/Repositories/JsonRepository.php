<?php

namespace RichanFongdasen\I18n\Repositories;

class JsonRepository extends Repository
{
    /**
     * Read the json datasource.
     *
     * @return array
     */
    protected function read(): array
    {
        $content = file_get_contents($this->datasource);

        return ($content !== false) ? json_decode($content) : [];
    }
}
