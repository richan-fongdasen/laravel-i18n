<?php

namespace RichanFongdasen\I18n\Tests;

use RichanFongdasen\I18n\Locale;
use RichanFongdasen\I18n\UrlGenerator;

class UrlGeneratorTests extends TestCase
{
    /**
     * Locale object
     *
     * @var \RichanFongdasen\I18n\Locale
     */
    protected $locale;

    /**
     * URL Generator Object
     *
     * @var \RichanFongdasen\I18n\UrlGenerator
     */
    protected $urlGenerator;

    public function setUp()
    {
        parent::setUp();

        $this->locale = new Locale('English', 'EN', 'us');
        $this->urlGenerator = new UrlGenerator('language');
    }

    /** @test */
    public function it_can_parse_complex_url()
    {
        $this->urlGenerator->setUrl('https://usr:psw@test.de:81/my/file.php?a=b&b[]=2&b[]=3#myFragment');

        $this->assertEquals('https://', $this->getPropertyValue($this->urlGenerator, 'scheme'));
        $this->assertEquals('usr', $this->getPropertyValue($this->urlGenerator, 'user'));
        $this->assertEquals(':psw@', $this->getPropertyValue($this->urlGenerator, 'pass'));
        $this->assertEquals('test.de', $this->getPropertyValue($this->urlGenerator, 'host'));
        $this->assertEquals(':81', $this->getPropertyValue($this->urlGenerator, 'port'));
        $this->assertEquals('/my/file.php', $this->getPropertyValue($this->urlGenerator, 'path'));
        $this->assertEquals('?a=b&b[]=2&b[]=3', $this->getPropertyValue($this->urlGenerator, 'query'));
        $this->assertEquals('#myFragment', $this->getPropertyValue($this->urlGenerator, 'fragment'));
    }

    /** @test */
    public function it_can_parse_schemaless_url()
    {
        $this->urlGenerator->setUrl('//:pass@test.de:82/my/file.php#myFragment');

        $this->assertEquals('//', $this->getPropertyValue($this->urlGenerator, 'scheme'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'user'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'pass'));
        $this->assertEquals('test.de', $this->getPropertyValue($this->urlGenerator, 'host'));
        $this->assertEquals(':82', $this->getPropertyValue($this->urlGenerator, 'port'));
        $this->assertEquals('/my/file.php', $this->getPropertyValue($this->urlGenerator, 'path'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'query'));
        $this->assertEquals('#myFragment', $this->getPropertyValue($this->urlGenerator, 'fragment'));
    }

    /** @test */
    public function it_can_parse_hostless_url()
    {
        $this->urlGenerator->setUrl('/my/file.php?a=b&b[]=2&b[]=3#myFragment');

        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'scheme'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'user'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'pass'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'host'));
        $this->assertEquals(null, $this->getPropertyValue($this->urlGenerator, 'port'));
        $this->assertEquals('/my/file.php', $this->getPropertyValue($this->urlGenerator, 'path'));
        $this->assertEquals('?a=b&b[]=2&b[]=3', $this->getPropertyValue($this->urlGenerator, 'query'));
        $this->assertEquals('#myFragment', $this->getPropertyValue($this->urlGenerator, 'fragment'));
    }

    /** @test */
    public function it_can_localize_any_url_based_on_the_given_locale_object()
    {
        $actual = $this->urlGenerator->setUrl('/about/company-overview')->localize($this->locale);

        $this->assertEquals('/en/about/company-overview', $actual);

        $actual = $this->urlGenerator->setUrl('//usr:psw@test.de:81/my/file.php?a=b&b[]=2&b[]=3#myFragment')
            ->localize($this->locale);

        $this->assertEquals('//usr:psw@test.de:81/en/my/file.php?a=b&b[]=2&b[]=3#myFragment', $actual);
    }
}
