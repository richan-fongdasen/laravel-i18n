<?php

namespace RichanFongdasen\I18n\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\I18n\Contracts\LocaleRepository;
use RichanFongdasen\I18n\I18nService;
use RichanFongdasen\I18n\Locale;

class I18nServiceTest extends TestCase
{
    /**
     * A mocked Request object
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * A mocked LocaleRepository instance.
     *
     * @var LocaleRepository
     */
    protected LocaleRepository $repository;

    /**
     * I18nService object
     *
     * @var \RichanFongdasen\I18n\I18nService
     */
    protected I18nService $service;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = \Mockery::mock(LocaleRepository::class);
        $this->request = \Mockery::mock(Request::class);
        $this->service = new I18nService($this->repository, $this->request);
    }

    #[Test]
    public function it_can_retrieve_all_locale_collection()
    {
        $this->repository->shouldReceive('all')->andReturn(new Collection([1, 2, 3]));

        $actual = $this->service->getAllLocale();

        self::assertEquals(3, $actual->count());
    }

    #[Test]
    public function it_can_retrieve_the_default_locale()
    {
        $expected = new Locale('English', 'en', 'US');
        $this->repository->shouldReceive('default')->andReturn($expected);

        $actual = $this->service->getDefaultLocale();

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_can_retrieve_the_locale_based_on_the_given_language_key()
    {
        $expected = new Locale('English', 'en', 'US');
        $this->repository->shouldReceive('get')->withArgs(['en'])->andReturn($expected);

        $actual = $this->service->getLocale('en');

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_can_retrieve_the_locale_keys_based_on_the_given_attribute_name()
    {
        $expected = ['en', 'es', 'de'];
        $this->repository->shouldReceive('getKeys')->withArgs(['language'])->andReturn($expected);

        $actual = $this->service->getLocaleKeys('language');

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_can_guess_the_translation_table_name_for_a_model()
    {
        self::assertEquals('news_translations', $this->service->guessTranslationTable('News'));
        self::assertEquals('product_category_translations', $this->service->guessTranslationTable('ProductCategory'));
        self::assertEquals('product_translations', $this->service->guessTranslationTable('Product'));
    }
}
