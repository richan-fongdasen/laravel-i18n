<?php

namespace RichanFongdasen\I18n;

use Illuminate\Support\Collection;
use Traversable;

class JsonRepository extends AbstractRepository
{
    /**
     * Get all registered locale from the datasource.
     *
     * @return Traversable
     */
    public function readDataSource(): Traversable
    {
        $content = file_get_contents((string) config('i18n.language_datasource'));

        if ($content === false || $content === '') {
            return new Collection();
        }

        return new Collection(json_decode($content));
    }
}
