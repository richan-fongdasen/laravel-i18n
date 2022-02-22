<?php

namespace RichanFongdasen\I18n;

use Illuminate\Support\Facades\DB;
use Traversable;

class DatabaseRepository extends AbstractRepository
{
    /**
     * Get all registered locale from the datasource.
     *
     * @return Traversable
     */
    public function readDataSource(): Traversable
    {
        return DB::table((string) config('i18n.language_datasource'))
            ->orderBy('order')
            ->get();
    }
}
