<?php

namespace RichanFongdasen\I18n\Tests\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RichanFongdasen\I18n\Locale;
use RichanFongdasen\I18n\Middleware\NegotiateLanguage;
use RichanFongdasen\I18n\Tests\TestCase;

class NegotiateLanguageTests extends TestCase
{
    /**
     * Negotiate Language Middleware object
     *
     * @var \RichanFongdasen\I18n\Middleware\NegotiateLanguage
     */
    protected $middleware;

    /**
     * A mocked Request object
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        $this->request = \Mockery::mock(Request::class);
        $this->middleware = new NegotiateLanguage();
    }

    /** @test */
    public function it_will_pass_the_request_if_the_request_has_locale_prefix_in_its_url()
    {
        $closure = function (Request $request) {
            return 'Hurray!!';
        };

        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('en');

        $actual = $this->middleware->handle($this->request, $closure);

        $this->assertEquals('Hurray!!', $actual);
    }

    /** @test */
    public function redirect_to_english_localized_url_if_the_request_has_no_locale_prefix_in_its_url()
    {
        $closure = function (Request $request) {
            return 'Hurray!!';
        };

        $this->request->shouldReceive('getLanguages')
            ->times(1)
            ->andReturn(['ar_AR', 'en_US', 'en', 'de']);

        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('about-us');

        $this->request->shouldReceive('getRequestUri')
            ->times(1)
            ->andReturn('/about-us/company-overview?a=b&c=d');

        $response = $this->middleware->handle($this->request, $closure);
        $expected = 'http://localhost/en/about-us/company-overview?a=b&c=d';

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($expected, $response->getTargetUrl());
    }

    /** @test */
    public function redirect_to_spanish_localized_url_if_the_request_has_no_locale_prefix_in_its_url()
    {
        $closure = function (Request $request) {
            return 'Hurray!!';
        };

        $this->request->shouldReceive('getLanguages')
            ->times(1)
            ->andReturn(['ar_AR', 'es_ES', 'en', 'de']);

        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('about-us');

        $this->request->shouldReceive('getRequestUri')
            ->times(1)
            ->andReturn('/about-us/company-overview?a=b&c=d');

        $response = $this->middleware->handle($this->request, $closure);
        $expected = 'http://localhost/es/about-us/company-overview?a=b&c=d';

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($expected, $response->getTargetUrl());
    }
}
