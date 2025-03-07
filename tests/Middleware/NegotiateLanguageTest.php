<?php

namespace RichanFongdasen\I18n\Tests\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\I18n\Contracts\LocaleRepository;
use RichanFongdasen\I18n\I18nService;
use RichanFongdasen\I18n\Middleware\NegotiateLanguage;
use RichanFongdasen\I18n\Tests\TestCase;

class NegotiateLanguageTest extends TestCase
{
    /**
     * Negotiate Language Middleware object
     *
     * @var \RichanFongdasen\I18n\Middleware\NegotiateLanguage
     */
    protected NegotiateLanguage $middleware;

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
    public function setUp(): void
    {
        parent::setUp();

        $this->request = \Mockery::mock(Request::class);
        $this->service = new I18nService(app(LocaleRepository::class), $this->request);
        $this->middleware = new NegotiateLanguage($this->service);
    }

    #[Test]
    public function it_will_raise_exception_on_invalid_negotiator_defined_in_config()
    {
        $closure = function (Request $request) {
            return 'Hurray!!';
        };
        config(['i18n.negotiator' => I18nService::class]);

        $this->expectException(\ErrorException::class);
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn(null);

        $this->middleware->handle($this->request, $closure);
    }

    #[Test]
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

    #[Test]
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

        $this->request->shouldReceive('fullUrl')
            ->times(1)
            ->andReturn('http://localhost/about-us/company-overview?a=b&c=d');

        $response = $this->middleware->handle($this->request, $closure);
        $expected = 'http://localhost/en/about-us/company-overview?a=b&c=d';

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($expected, $response->getTargetUrl());
    }

    #[Test]
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

        $this->request->shouldReceive('fullUrl')
            ->times(1)
            ->andReturn('http://localhost/about-us/company-overview?a=b&c=d');

        $response = $this->middleware->handle($this->request, $closure);
        $expected = 'http://localhost/es/about-us/company-overview?a=b&c=d';

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($expected, $response->getTargetUrl());
    }
}
