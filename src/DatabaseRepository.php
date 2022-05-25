<?php

namespace RichanFongdasen\I18n;

use RichanFongdasen\I18n\Eloquent\Models\Language;
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
        return Language::orderBy('order')
            ->get();
    }
}
