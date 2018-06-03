<?php

namespace RichanFongdasen\I18n\Repositories;

class DatabaseRepository extends Repository
{
    /**
     * Read the datasource
     *
     * @return \Illuminate\Support\Collection
     */
    protected function read()
    {
        return \DB::table($this->datasource)
            ->orderBy('order', 'asc')
            ->get();
    }
}
