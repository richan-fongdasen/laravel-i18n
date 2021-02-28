<?php

namespace RichanFongdasen\I18n\Tests;

use Illuminate\Http\Request;
use RichanFongdasen\I18n\Exceptions\InvalidFallbackLanguageException;
use RichanFongdasen\I18n\Exceptions\InvalidLocaleException;
use RichanFongdasen\I18n\I18nService;
use RichanFongdasen\I18n\Locale;

class I18nServiceTests extends TestCase
{
    /**
     * A mocked Request object
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * I18nService object
     *
     * @var \RichanFongdasen\I18n\I18nService
     */
    protected $service;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        $this->request = \Mockery::mock(Request::class);
        $this->service = new I18nService($this->request);
    }

    /** @test */
    public function it_returns_the_configured_fallback_language_as_default_locale()
    {
        $this->app['config']->set('i18n.fallback_language', 'es');
        $this->invokeMethod($this->service, 'loadConfig');

        $locale = $this->service->defaultLocale();

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('es-ES', $locale->ietfCode);
        $this->assertEquals('es', $locale->language);
    }

    /** @test */
    public function it_raises_exception_when_it_cant_find_the_default_locale()
    {
        $this->expectException(InvalidFallbackLanguageException::class);

        $this->app['config']->set('i18n.fallback_language', 'ar');
        $this->invokeMethod($this->service, 'loadConfig');

        $this->service->defaultLocale();
    }

    /** @test */
    public function it_can_fix_the_ietf_code_string_format()
    {
        $codes = ['en_US', 'es-ES', 'de_DE'];
        $expectations = ['en-US', 'es-ES', 'de-DE'];

        for ($i = 0; $i < count($codes); $i++) {
            $actual = $this->invokeMethod($this->service, 'formatIetf', [$codes[$i]]);

            $this->assertEquals($expectations[$i], $actual);
        }
    }

    /** @test */
    public function it_returns_configuration_value_correctly()
    {
        $this->app['config']->set('i18n.fallback_language', 'es');
        $this->app['config']->set('i18n.driver', 'memcached');
        $this->invokeMethod($this->service, 'loadConfig');

        $fallback_language = $this->service->getConfig('fallback_language');
        $driver = $this->service->getConfig('driver');

        $this->assertEquals('es', $fallback_language);
        $this->assertEquals('memcached', $driver);
    }

    /** @test */
    public function it_returns_correct_locale_based_on_the_given_language_code()
    {
        $locale = $this->service->getLocale('de');

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('de-DE', $locale->ietfCode);
    }

    /** @test */
    public function it_returns_correct_locale_based_on_the_given_ietf_code()
    {
        $locale = $this->service->getLocale('es-ES');

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('Spanish', $locale->name);
    }

    /** @test */
    public function it_returns_null_when_there_is_no_matched_locale_available()
    {
        $locale = $this->service->getLocale('ar');

        $this->assertEquals(null, $locale);
    }

    /** @test */
    public function it_returns_correct_locale_keys_based_on_the_default_locale_key()
    {
        $actual = $this->service->getLocaleKeys();
        $expected = ['en', 'es', 'de'];

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_returns_correct_locale_keys_based_on_the_given_locale_key()
    {
        $actual = $this->service->getLocaleKeys('ietfCode');
        $expected = ['en-US', 'es-ES', 'de-DE'];

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_returns_null_as_locale_keys_on_invalid_key_name()
    {
        $actual = $this->service->getLocaleKeys('invalidKey');

        $this->assertEquals(null, $actual);
    }

    /** @test */
    public function it_can_load_locale_from_repository()
    {
        $collection = $this->invokeMethod($this->service, 'loadLocale');

        $this->assertInstanceOf(Locale::class, $collection->get('en'));
        $this->assertInstanceOf(Locale::class, $collection->get('es'));
        $this->assertInstanceOf(Locale::class, $collection->get('de'));
        $this->assertEquals(null, $collection->get('ch'));
    }

    /** @test */
    public function it_caches_locale()
    {
        $cacheKey = 'laravel-i18n-locale-'.$this->service->getConfig('driver');
        \Cache::forget($cacheKey);
        $collection = $this->invokeMethod($this->service, 'loadLocale');

        $this->assertEquals($collection->get('en'), \Cache::get($cacheKey)->get('en'));
        $this->assertEquals($collection->get('es'), \Cache::get($cacheKey)->get('es'));
        $this->assertEquals($collection->get('de'), \Cache::get($cacheKey)->get('de'));
    }

    /** @test */
    public function it_does_not_cache_locale()
    {
        $this->app['config']->set('i18n.enable_cache', false);
        $this->invokeMethod($this->service, 'loadConfig');
        $cacheKey = 'laravel-i18n-locale-'.$this->service->getConfig('driver');
        \Cache::forget($cacheKey);
        $this->invokeMethod($this->service, 'loadLocale');

        $this->assertEquals(null, \Cache::get($cacheKey));
    }

    /** @test */
    public function it_can_determine_the_routed_locale_based_on_the_given_request_object()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('de');

        $locale = $this->service->routedLocale($this->request);

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('de-DE', $locale->ietfCode);
    }

    /** @test */
    public function it_can_determine_the_routed_locale_based_on_current_request_object()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('es');

        $locale = $this->service->routedLocale();

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertEquals('es-ES', $locale->ietfCode);
    }

    /** @test */
    public function it_returns_null_when_there_is_no_locale_prefix_in_current_request_url()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('about-us');

        $locale = $this->service->routedLocale();

        $this->assertEquals(null, $locale);
    }

    /** @test */
    public function it_will_return_null_if_the_language_segment_doesnt_contain_any_string()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn(null);

        $locale = $this->service->routedLocale();

        $this->assertEquals(null, $locale);
    }

    /** @test */
    public function it_returns_spanish_route_prefix_based_on_the_routed_locale()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(2)
            ->andReturn('es');

        $actual = $this->service->routePrefix();

        $this->assertEquals('es', $actual);
    }

    /** @test */
    public function it_returns_english_route_prefix_based_on_the_default_locale()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('about-us');

        $actual = $this->service->routePrefix();

        $this->assertEquals('en', $actual);
    }

    /** @test */
    public function it_raises_exception_on_generating_url_with_an_invalid_locale()
    {
        $this->expectException(InvalidLocaleException::class);

        $this->service->url('/about-us/company-overview', 'ar');
    }

    /** @test */
    public function it_generate_correct_url_based_on_the_given_locale()
    {
        $expected = '/de/about-us/company-overview';
        $actual = $this->service->url('/about-us/company-overview', 'de');

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_generate_correct_url_based_on_the_routed_locale()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('es');

        $expected = '/es/about-us/company-overview';
        $actual = $this->service->url('/about-us/company-overview');

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_generate_correct_url_based_on_the_default_locale()
    {
        $this->request->shouldReceive('segment')
            ->with(1)
            ->times(1)
            ->andReturn('about-us');

        $expected = '/en/about-us/company-overview';
        $actual = $this->service->url('/about-us/company-overview');

        $this->assertEquals($expected, $actual);
    }
}
