<?php

namespace RichanFongdasen\I18n\Repositories;

use Illuminate\Support\Collection;

class DatabaseRepository extends Repository
{
    /**
     * Read the datasource.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function read(): Collection
    {
        return \DB::table($this->datasource)
            ->orderBy('order', 'asc')
            ->get();
    }
}
