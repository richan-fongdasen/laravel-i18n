<?php

namespace RichanFongdasen\I18n\Tests\Negotiators;

use Illuminate\Http\Request;
use RichanFongdasen\I18n\Contracts\LocaleRepository;
use RichanFongdasen\I18n\I18nService;
use RichanFongdasen\I18n\Locale;
use RichanFongdasen\I18n\Negotiators\BrowserNegotiator;
use RichanFongdasen\I18n\Tests\TestCase;

class BrowserNegotiatorTest extends TestCase
{
    /**
     * Browser Negotiator object
     *
     * @var \RichanFongdasen\I18n\Negotiators\BrowserNegotiator
     */
    protected BrowserNegotiator $negotiator;

    /**
     * A mocked Request object
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();


        $this->request = \Mockery::mock(Request::class);
        $service = new I18nService(app(LocaleRepository::class), $this->request);
        $this->negotiator = new BrowserNegotiator($service);
    }

    /** @test */
    public function it_return_english_locale_based_on_browsers_primary_language_code()
    {
        $this->request->shouldReceive('getLanguages')
            ->times(1)
            ->andReturn(['en', 'de', 'es']);

        $locale = $this->negotiator->preferredLocale($this->request);

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('en-US', $locale->ietfCode);
    }

    /** @test */
    public function it_return_english_locale_based_on_browsers_primary_ietf_code()
    {
        $this->request->shouldReceive('getLanguages')
            ->times(1)
            ->andReturn(['en_US', 'en', 'de', 'es']);

        $locale = $this->negotiator->preferredLocale($this->request);

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('en-US', $locale->ietfCode);
    }

    /** @test */
    public function it_return_spanish_locale_based_on_browsers_secondary_language_code()
    {
        $this->request->shouldReceive('getLanguages')
            ->times(1)
            ->andReturn(['ar', 'es', 'de']);

        $locale = $this->negotiator->preferredLocale($this->request);

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('es-ES', $locale->ietfCode);
    }

    /** @test */
    public function it_return_spanish_locale_based_on_browsers_secondary_ietf_code()
    {
        $this->request->shouldReceive('getLanguages')
            ->times(1)
            ->andReturn(['ar_AR', 'es_ES', 'en', 'de']);

        $locale = $this->negotiator->preferredLocale($this->request);

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('es-ES', $locale->ietfCode);
    }

    /** @test */
    public function it_return_english_locale_as_fallback_locale()
    {
        $this->request->shouldReceive('getLanguages')
            ->times(1)
            ->andReturn(['ar_AR', 'ch', 'id-ID']);

        $locale = $this->negotiator->preferredLocale($this->request);

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('en-US', $locale->ietfCode);
    }
}
