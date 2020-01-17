<?php

namespace RichanFongdasen\I18n\Tests\Commands;

use Illuminate\Support\Facades\Route;
use RichanFongdasen\I18n\Tests\TestCase;
use RichanFongdasen\I18n\Tests\WithRouteTestCase;

class RouteTranslationsCacheCommandTests extends TestCase
{
    use RouteTranslationTestTrait;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();
 
        \Illuminate\Support\Facades\File::copy(
            realpath(__DIR__.'/../Supports/app.php'),
            $this->app->bootstrapPath().'/app.php'
        );
    }

    /** @test */
    public function it_will_get_error_because_no_route_exists()
    {
        WithRouteTestCase::$useRoute = false;
        $this->artisan('route:trans:cache')
            ->expectsOutput('Your application doesn\'t have any routes.')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_will_generate_cache_files()
    {
        WithRouteTestCase::$useRoute = true;
        $this->artisan('route:trans:cache')
            ->assertExitCode(0);
        $this->assertTrueLocaleCache();
        WithRouteTestCase::$useRoute = false;
    }
}
