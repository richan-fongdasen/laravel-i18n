<?php

namespace RichanFongdasen\I18n\Tests\Commands;

trait RouteTranslationTestTrait
{
    /**
     * @param string|null $locale
     *
     * @return string
     */
    protected function makeLocaleRoutesPath($locale = null)
    {
        $path = $this->app->getCachedRoutesPath();
        if ($locale === null) {
            return $path;
        }
        $path = substr($path, 0, -4).'_'.$locale.'.php';

        return $path;
    }

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