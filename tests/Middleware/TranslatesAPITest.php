<?php

namespace RichanFongdasen\I18n\Tests\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use RichanFongdasen\I18n\Contracts\LocaleRepository;
use RichanFongdasen\I18n\I18nService;
use RichanFongdasen\I18n\Middleware\TranslatesAPI;
use RichanFongdasen\I18n\Tests\TestCase;

class TranslatesAPITest extends TestCase
{
    /**
     * Negotiate Language Middleware object
     *
     * @var \RichanFongdasen\I18n\Middleware\TranslatesAPI
     */
    protected TranslatesAPI $middleware;

    /**
     * A mocked Request object
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * I18n service instance.
     *
     * @var I18nService
     */
    protected I18nService $service;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        config(['app.locale' => 'en']);
        App::setLocale('en');

        $this->request = \Mockery::mock(Request::class);
        $this->service = new I18nService(app(LocaleRepository::class), $this->request);
        $this->middleware = new TranslatesAPI($this->service);
    }

    /** @test */
    public function it_will_favor_default_app_language_when_there_was_no_language_parameter_in_api_query()
    {
        $closure = function (Request $request) {
            return 'Hurray!!';
        };
        $this->request->shouldReceive('input')->withArgs(['lang'])->andReturn(null);

        $this->middleware->handle($this->request, $closure);

        self::assertEquals('en', App::getLocale());
    }

    /** @test */
    public function it_will_favor_default_app_language_on_invalid_language_query_parameter()
    {
        $closure = function (Request $request) {
            return 'Hurray!!';
        };
        $this->request->shouldReceive('input')->withArgs(['lang'])->andReturn('id');

        $this->middleware->handle($this->request, $closure);

        self::assertEquals('en', App::getLocale());
    }

    /** @test */
    public function it_will_switch_the_app_language_based_on_the_requested_language_query_parameter()
    {
        $closure = function (Request $request) {
            return 'Hurray!!';
        };
        $this->request->shouldReceive('input')->withArgs(['lang'])->andReturn('de');

        $this->middleware->handle($this->request, $closure);

        self::assertEquals('de', App::getLocale());
    }
}