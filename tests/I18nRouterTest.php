<?php

namespace RichanFongdasen\I18n\Tests;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use RichanFongdasen\I18n\I18nRouter;
use RichanFongdasen\I18n\I18nService;
use RichanFongdasen\I18n\Locale;

class I18nRouterTest extends TestCase
{
    /**
     * Locale object
     *
     * @var \RichanFongdasen\I18n\Locale
     */
    protected Locale $locale;

    /**
     * The HTTP Request instance.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * The I18nRouter instance.
     *
     * @var I18nRouter
     */
    protected I18nRouter $router;

    /**
     * The I18nService instance.
     *
     * @var I18nService
     */
    protected I18nService $service;

    /**
     * Setup the test environment
     *
     * @return void
     * @throws \ErrorException
     */
    public function setUp(): void
    {
        parent::setUp();

        App::setLocale('en');

        $this->request = \Mockery::mock(Request::class);
        $this->service = app(I18nService::class);
        $this->router = new I18nRouter($this->request, $this->service);
        $this->locale = $this->service->getDefaultLocale();
    }

    #[Test]
    public function it_can_identify_and_returns_the_respective_locale_instance()
    {
        $this->request->shouldReceive('segment')->withArgs([1])->andReturn('es');

        $locale = $this->router->locale();

        self::assertInstanceOf(Locale::class, $locale);
        self::assertEquals('es', $locale->getKey());
        self::assertEquals('es', App::getLocale());
    }

    #[Test]
    public function it_returns_null_on_empty_locale_key_segment()
    {
        $this->request->shouldReceive('segment')->withArgs([1])->andReturn('');

        $locale = $this->router->locale();

        self::assertNull($locale);
        self::assertEquals('en', App::getLocale());
    }

    #[Test]
    public function it_returns_null_on_unidentified_locale_key_segment()
    {
        $this->request->shouldReceive('segment')->withArgs([1])->andReturn('unknown');

        $locale = $this->router->locale();

        self::assertNull($locale);
        self::assertEquals('en', App::getLocale());
    }

    #[Test]
    public function it_can_returns_the_url_prefix_correctly()
    {
        $this->request->shouldReceive('segment')->withArgs([1])->andReturn('de');

        self::assertEquals('de', $this->router->getPrefix());
    }

    #[Test]
    public function it_raises_exception_on_generating_url_with_an_invalid_locale()
    {
        $this->expectException(\ErrorException::class);

        $this->router->url('/about-us/company-overview', 'ar');
    }

    #[Test]
    public function it_generate_correct_url_based_on_the_given_locale()
    {
        $expected = '/de/about-us/company-overview';
        $actual = $this->router->url('/about-us/company-overview', 'de');

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function it_generate_correct_url_based_on_the_routed_locale()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('es');

        $expected = '/es/about-us/company-overview';
        $actual = $this->router->url('/about-us/company-overview');

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function it_generate_correct_url_based_on_the_default_locale()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('about-us');

        $expected = '/en/about-us/company-overview';
        $actual = $this->router->url('/about-us/company-overview');

        $this->assertEquals($expected, $actual);
    }
}
