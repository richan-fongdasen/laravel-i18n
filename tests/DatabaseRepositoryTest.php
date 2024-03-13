<?php

namespace RichanFongdasen\I18n\Tests;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\DB;
use RichanFongdasen\I18n\Contracts\LocaleRepository;
use RichanFongdasen\I18n\DatabaseRepository;
use RichanFongdasen\I18n\Locale;

class DatabaseRepositoryTest extends TestCase
{
    /**
     * The DatabaseRepository instance.
     *
     * @var DatabaseRepository
     */
    protected DatabaseRepository $repository;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        config([
            'i18n.driver'              => 'database',
            'i18n.language_datasource' => 'languages',
        ]);

        DB::table('languages')->insert([
            [
                'name'     => 'english',
                'language' => 'EN',
                'country'  => 'us',
                'order'    => 1,
            ],
            [
                'name'     => 'spanish',
                'language' => 'ES',
                'country'  => 'es',
                'order'    => 2,
            ],
            [
                'name'     => 'german',
                'language' => 'DE',
                'country'  => 'de',
                'order'    => 3,
            ],
        ]);

        $this->repository = new DatabaseRepository;
    }

    #[Test]
    public function it_can_resolve_the_abstract_interface_as_json_repository()
    {
        $repository = app(LocaleRepository::class);

        self::assertInstanceOf(DatabaseRepository::class, $repository);
    }

    #[Test]
    public function it_can_load_all_the_locale_from_the_datasource()
    {
        $collection = $this->repository->all();

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
}
