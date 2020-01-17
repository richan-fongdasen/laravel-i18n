<?php

namespace RichanFongdasen\I18n\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use RichanFongdasen\I18n\Tests\TestCase;
use RichanFongdasen\I18n\Tests\WithRouteTestCase;

class RouteTranslationsListCommandTests extends TestCase
{
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
    public function it_will_get_error_when_locale_not_supported()
    {
        WithRouteTestCase::$useRoute = true;
        $this->artisan('route:trans:list', ['locale' => 'jp'])
            ->expectsOutput("Unsupported locale: 'jp'.")
            ->assertExitCode(0);
    }

    /** @test */
    public function it_will_get_show_supported_locale_route()
    {
        WithRouteTestCase::$useRoute = true;
        $this->artisan('route:trans:list', ['locale' => 'es', '--json' => true, '--sort' => 'name', '--reverse'=> true, '--compact' => true])
            ->expectsOutput('[{"method":"GET|HEAD","uri":"es\/foo","action":"RichanFongdasen\\\\I18n\\\\Tests\\\\Supports\\\\Controllers\\\\FooController"},{"method":"GET|HEAD","uri":"es\/bar","action":"RichanFongdasen\\\\I18n\\\\Tests\\\\Supports\\\\Controllers\\\\BarController"}]')
            ->assertExitCode(0);
    }
}
