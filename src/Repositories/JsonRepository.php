<?php

namespace RichanFongdasen\I18n\Repositories;

class JsonRepository extends Repository
{
    /**
     * Read the json datasource
     *
     * @return array
     */
    protected function read()
    {
        return json_decode(file_get_contents($this->datasource));
    }
}
