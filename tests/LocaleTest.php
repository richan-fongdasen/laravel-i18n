<?php

namespace RichanFongdasen\I18n\Tests;

use RichanFongdasen\I18n\Locale;

class LocaleTest extends TestCase
{
    /** @test */
    public function all_the_class_properties_are_accessible_read_only()
    {
        $locale = new Locale('english', 'EN', 'us');

        self::assertEquals('English', $locale->name);
        self::assertEquals('en', $locale->language);
        self::assertEquals('US', $locale->country);
        self::assertEquals('en-US', $locale->ietfCode);
    }

    /** @test */
    public function it_can_retrieve_locale_key_value()
    {
        config(['i18n.language_key' => 'ietfCode']);
        $locale = new Locale('english', 'EN', 'us');

        self::assertEquals('en-US', $locale->getKey());
    }
}
