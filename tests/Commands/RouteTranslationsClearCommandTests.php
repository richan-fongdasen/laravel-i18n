<?php

namespace RichanFongdasen\I18n\Tests\Commands;

use RichanFongdasen\I18n\Tests\TestCase;
use RichanFongdasen\I18n\Tests\WithRouteTestCase;

class RouteTranslationsClearCommandTests extends TestCase
{
    use RouteTranslationTestTrait;

    protected $laravel;

    /** @test */
    public function it_will_clear_locale_route_cache_files()
    {
        $this->laravel = $this->app;
        WithRouteTestCase::$useRoute = true;

        $this->artisan('route:trans:cache')
            ->assertExitCode(0);
        $this->assertTrueLocaleCache();

        $this->artisan('route:trans:clear')
            ->expectsOutput('Route caches for locales cleared!')
            ->assertExitCode(0);
        $this->assertFalseLocaleCache();
    }
}