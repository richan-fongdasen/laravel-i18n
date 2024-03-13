<?php

namespace RichanFongdasen\I18n\Tests;

use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\I18n\I18nService;
use RichanFongdasen\I18n\Locale;
use RichanFongdasen\I18n\UrlGenerator;

class UrlGeneratorTest extends TestCase
{
    /**
     * Locale object
     *
     * @var \RichanFongdasen\I18n\Locale
     */
    protected Locale $locale;

    /**
     * The I18nService instance.
     *
     * @var I18nService
     */
    protected I18nService $service;

    /**
     * URL Generator Object
     *
     * @var \RichanFongdasen\I18n\UrlGenerator
     */
    protected UrlGenerator $urlGenerator;

    /**
     * Setup the test environment
     *
     * @return void
     * @throws \ErrorException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = app(I18nService::class);
        $this->urlGenerator = new UrlGenerator($this->service, 'https://google.com');
        $this->locale = $this->service->getDefaultLocale();
    }

    #[Test]
    public function it_can_parse_complex_url()
    {
        $this->urlGenerator->set('https://usr:psw@test.de:81/my/file.php?a=b&b[]=2&b[]=3#myFragment');

        $this->assertEquals('https://', $this->getPropertyValue($this->urlGenerator, 'scheme'));
        $this->assertEquals('usr', $this->getPropertyValue($this->urlGenerator, 'user'));
        $this->assertEquals(':psw@', $this->getPropertyValue($this->urlGenerator, 'password'));
        $this->assertEquals('test.de', $this->getPropertyValue($this->urlGenerator, 'host'));
        $this->assertEquals(':81', $this->getPropertyValue($this->urlGenerator, 'port'));
        $this->assertEquals(['', 'my', 'file.php'], $this->getPropertyValue($this->urlGenerator, 'path'));
        $this->assertEquals('?a=b&b[]=2&b[]=3', $this->getPropertyValue($this->urlGenerator, 'query'));
        $this->assertEquals('#myFragment', $this->getPropertyValue($this->urlGenerator, 'fragment'));
    }

    #[Test]
    public function it_can_parse_schemaless_url()
    {
        $this->urlGenerator->set('//:pass@test.de:82/my/file.php#myFragment');

        $this->assertEquals('//', $this->getPropertyValue($this->urlGenerator, 'scheme'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'user'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'password'));
        $this->assertEquals('test.de', $this->getPropertyValue($this->urlGenerator, 'host'));
        $this->assertEquals(':82', $this->getPropertyValue($this->urlGenerator, 'port'));
        $this->assertEquals(['', 'my', 'file.php'], $this->getPropertyValue($this->urlGenerator, 'path'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'query'));
        $this->assertEquals('#myFragment', $this->getPropertyValue($this->urlGenerator, 'fragment'));
    }

    #[Test]
    public function it_can_parse_hostless_url()
    {
        $this->urlGenerator->set('/my/file.php?a=b&b[]=2&b[]=3#myFragment');

        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'scheme'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'user'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'password'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'host'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'port'));
        $this->assertEquals(['', 'my', 'file.php'], $this->getPropertyValue($this->urlGenerator, 'path'));
        $this->assertEquals('?a=b&b[]=2&b[]=3', $this->getPropertyValue($this->urlGenerator, 'query'));
        $this->assertEquals('#myFragment', $this->getPropertyValue($this->urlGenerator, 'fragment'));
    }

    #[Test]
    public function it_can_localize_any_url_based_on_the_given_locale_object()
    {
        $actual = $this->urlGenerator->set('/about/company-overview')->localize($this->locale)->get();

        $this->assertEquals('/en/about/company-overview', $actual);

        $actual = $this->urlGenerator->set('//usr:psw@test.de:81/my/file.php?a=b&b[]=2&b[]=3#myFragment')
            ->localize($this->locale)->get();

        $this->assertEquals('//usr:psw@test.de:81/en/my/file.php?a=b&b[]=2&b[]=3#myFragment', $actual);
    }

    #[Test]
    public function it_returns_default_value_on_extracting_empty_url()
    {
        $actual = $this->invokeMethod($this->urlGenerator, 'extract', [[], 'scheme', '//']);
        $this->assertEquals('//', $actual);
    }

    #[Test]
    public function it_can_localize_url_which_already_contain_locale_keyword()
    {
        $english = $this->service->getLocale('en');
        $spanish = $this->service->getLocale('es');

        $englishUrl = 'https://github.com/en/laravel/framework?a=b&c=d';
        $spanishUrl = 'https://github.com/es/laravel/framework?a=b&c=d';

        $this->urlGenerator->set($englishUrl);
        $this->assertEquals($englishUrl, $this->urlGenerator->localize($english)->get());

        $this->urlGenerator->set($englishUrl);
        $this->assertEquals($spanishUrl, $this->urlGenerator->localize($spanish)->get());

        $this->urlGenerator->set($spanishUrl);
        $this->assertEquals($englishUrl, $this->urlGenerator->localize($english)->get());

        $this->urlGenerator->set($spanishUrl);
        $this->assertEquals($spanishUrl, $this->urlGenerator->localize($spanish)->get());
    }

    #[Test]
    public function it_can_localize_url_which_already_contain_locale_keyword_at_custom_segment_index()
    {
        config(['i18n.locale_url_segment' => 3]);

        $english = $this->service->getLocale('en');
        $spanish = $this->service->getLocale('es');

        $englishUrl = 'https://github.com/special/info/en/laravel/framework?a=b&c=d';
        $spanishUrl = 'https://github.com/special/info/es/laravel/framework?a=b&c=d';

        $this->urlGenerator->set($englishUrl);
        $this->assertEquals($englishUrl, $this->urlGenerator->localize($english)->get());

        $this->urlGenerator->set($englishUrl);
        $this->assertEquals($spanishUrl, $this->urlGenerator->localize($spanish)->get());

        $this->urlGenerator->set($spanishUrl);
        $this->assertEquals($englishUrl, $this->urlGenerator->localize($english)->get());

        $this->urlGenerator->set($spanishUrl);
        $this->assertEquals($spanishUrl, $this->urlGenerator->localize($spanish)->get());
    }
}
