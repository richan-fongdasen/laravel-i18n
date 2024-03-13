<?php

namespace RichanFongdasen\I18n\Tests;

use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\I18n\Contracts\LocaleRepository;
use RichanFongdasen\I18n\JsonRepository;
use RichanFongdasen\I18n\Locale;

class LocaleRepositoryTest extends TestCase
{
    /**
     * The LocaleRepository instance.
     *
     * @var LocaleRepository
     */
    protected LocaleRepository $repository;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(LocaleRepository::class);
    }

    #[Test]
    public function it_can_return_all_the_registered_locale()
    {
        $collection = $this->repository->all();

        self::assertEquals(3, $collection->count());

        foreach ($collection as $locale) {
            self::assertInstanceOf(Locale::class, $locale);
        }
    }

    #[Test]
    public function it_can_return_fallback_language_as_the_default_language()
    {
        config([
            'app.locale' => 'de',
        ]);
        $this->repository = new JsonRepository;
        $locale = $this->repository->default();

        self::assertEquals('German', $locale->name);
        self::assertEquals('de', $locale->language);
        self::assertEquals('DE', $locale->country);
        self::assertEquals('de-DE', $locale->ietfCode);
    }

    #[Test]
    public function it_raises_exception_on_invalid_fallback_language()
    {
        $this->expectException(\ErrorException::class);
        config([
            'app.locale' => 'id',
        ]);

        new JsonRepository;
    }

    #[Test]
    public function it_can_retrieve_locale_based_on_the_given_key()
    {
        $locale = $this->repository->get('es');

        self::assertEquals('Spanish', $locale->name);
        self::assertEquals('es', $locale->language);
        self::assertEquals('ES', $locale->country);
        self::assertEquals('es-ES', $locale->ietfCode);
    }

    #[Test]
    public function it_returns_exact_locale_object_when_retrieved_using_different_key()
    {
        $locale1 = $this->repository->get('en');
        $locale2 = $this->repository->get('en-US');

        self::assertEquals($locale1, $locale2);
    }

    #[Test]
    public function it_returns_null_when_retrieving_locale_using_invalid_key()
    {
        $locale = $this->repository->get('id-ID');

        self::assertNull($locale);
    }

    #[Test]
    public function it_can_retrieve_all_locale_keys_grouped_by_language()
    {
        $expected = ['en', 'es', 'de'];

        $actual = $this->repository->getKeys();

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_can_retrieve_all_locale_keys_grouped_by_ietf_code()
    {
        $expected = ['en-US', 'es-ES', 'de-DE'];

        $actual = $this->repository->getKeys('ietfCode');

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_returns_null_when_retrieving_all_locale_keys_grouped_by_invalid_key()
    {
        $actual = $this->repository->getKeys('title');

        self::assertNull($actual);
    }
}
