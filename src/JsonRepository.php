<?php

namespace RichanFongdasen\I18n;

use ErrorException;
use Illuminate\Support\Collection;
use Traversable;

class JsonRepository extends AbstractRepository
{
    /**
     * Get all registered locale from the datasource.
     *
     * @throws ErrorException
     *
     * @return Traversable
     */
    public function readDataSource(): Traversable
    {
        if (!file_exists((string) config('i18n.language_datasource'))) {
            throw new ErrorException('Invalid language datasource defined in config i18n.language_datasource');
        }

        $content = file_get_contents((string) config('i18n.language_datasource'));

        if ($content === false || $content === '') {
            return new Collection();
        }

        return new Collection(json_decode($content));
    }
}
