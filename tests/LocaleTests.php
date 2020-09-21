<?php

namespace RichanFongdasen\I18n\Tests;

use RichanFongdasen\I18n\Locale;

class LocaleTests extends TestCase
{
    /**
     * Locale object
     *
     * @var \RichanFongdasen\I18n\Locale
     */
    protected $locale;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        $this->locale = new Locale('English', 'EN', 'us');
    }

    /** @test */
    public function it_can_determine_if_a_protected_variable_exists()
    {
        $this->assertTrue(isset($this->locale->language));
        $this->assertTrue(isset($this->locale->ietfCode));
        $this->assertFalse(isset($this->locale->languageCode));
    }

    /** @test */
    public function we_can_access_its_protected_variables_globally()
    {
        $this->assertEquals('en', $this->locale->language);
        $this->assertEquals('US', $this->locale->country);
        $this->assertEquals('en-US', $this->locale->ietfCode);
        $this->assertEquals('English', $this->locale->name);
    }

    /** @test */
    public function it_raises_exception_on_undefined_property_access()
    {
        $this->expectException(\ErrorException::class);

        $test = $this->locale->asdfg;
    }
}
