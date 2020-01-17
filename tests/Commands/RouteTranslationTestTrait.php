<?php

namespace RichanFongdasen\I18n\Tests\Commands;

use RichanFongdasen\I18n\Traits\LoadsTranslatedCachedRoutes;

trait RouteTranslationTestTrait
{
    use LoadsTranslatedCachedRoutes;

    protected function assertTrueLocaleCache()
    {
        $this->assertTrue(file_exists($this->makeLocaleRoutesPath()));
        $this->assertTrue(file_exists($this->makeLocaleRoutesPath('de')));
        $this->assertTrue(file_exists($this->makeLocaleRoutesPath('en')));
        $this->assertTrue(file_exists($this->makeLocaleRoutesPath('es')));
    }

    protected function assertFalseLocaleCache()
    {
        $this->assertFalse(file_exists($this->makeLocaleRoutesPath()));
        $this->assertFalse(file_exists($this->makeLocaleRoutesPath('de')));
        $this->assertFalse(file_exists($this->makeLocaleRoutesPath('en')));
        $this->assertFalse(file_exists($this->makeLocaleRoutesPath('es')));
    }
}