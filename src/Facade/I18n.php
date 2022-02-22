<?php

namespace RichanFongdasen\I18n\Facade;

use Illuminate\Support\Facades\Facade;
use RichanFongdasen\I18n\I18nService;

class I18n extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return I18nService::class;
    }
}
