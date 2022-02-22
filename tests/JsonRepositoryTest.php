<?php

namespace RichanFongdasen\I18n\Tests;

use RichanFongdasen\I18n\Contracts\LocaleRepository;
use RichanFongdasen\I18n\JsonRepository;
use RichanFongdasen\I18n\Locale;

class JsonRepositoryTest extends TestCase
{
    /** @test */
    public function it_can_resolve_the_abstract_interface_as_json_repository()
    {
        $repository = app(LocaleRepository::class);

        self::assertInstanceOf(JsonRepository::class, $repository);
    }

    /** @test */
    public function it_can_load_all_the_locale_from_the_datasource()
    {
        $collection = (new JsonRepository)->all();

        $english = $collection->get('en');
        self::assertInstanceOf(Locale::class, $english);
        self::assertEquals('English', $english->name);
        self::assertEquals('en', $english->language);
        self::assertEquals('US', $english->country);
        self::assertEquals('en-US', $english->ietfCode);

        $spanish = $collection->get('es');
        self::assertInstanceOf(Locale::class, $spanish);
        self::assertEquals('Spanish', $spanish->name);
        self::assertEquals('es', $spanish->language);
        self::assertEquals('ES', $spanish->country);
        self::assertEquals('es-ES', $spanish->ietfCode);

        $germany = $collection->get('de');
        self::assertInstanceOf(Locale::class, $germany);
        self::assertEquals('German', $germany->name);
        self::assertEquals('de', $germany->language);
        self::assertEquals('DE', $germany->country);
        self::assertEquals('de-DE', $germany->ietfCode);
    }

    /** @test */
    public function it_returns_empty_collection_on_empty_datasource()
    {
        $this->expectException(\ErrorException::class);

        config(['i18n.language_datasource' => '/dev/null']);
        $collection = (new JsonRepository)->all();

        self::assertEquals(0, $collection->count());
    }
}
