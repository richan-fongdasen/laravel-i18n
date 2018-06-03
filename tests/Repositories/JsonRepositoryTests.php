<?php

namespace RichanFongdasen\I18n\Tests\Repositories;

use RichanFongdasen\I18n\Locale;
use RichanFongdasen\I18n\Repositories\JsonRepository;
use RichanFongdasen\I18n\Tests\TestCase;

class JsonRepositoryTests extends TestCase
{
    /**
     * Json data source
     *
     * @var string
     */
    protected $datasource;

    /**
     * Json repository object
     *
     * @var \RichanFongdasen\I18n\Repositories\JsonRepository
     */
    protected $repository;

    /**
     * Setup the test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->datasource = realpath(__DIR__ . '/../../storage/i18n/languages.json');
        $this->repository = new JsonRepository($this->datasource);
    }

    /** @test */
    public function it_can_read_languages_from_json_file()
    {
        $actual = $this->invokeMethod($this->repository, 'read');
        $expected = json_decode(file_get_contents($this->datasource));

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_can_collect_languages_from_json_file()
    {
        $collection = $this->repository->collect();

        $this->assertInstanceOf(Locale::class, $collection->get('en'));
        $this->assertInstanceOf(Locale::class, $collection->get('es'));
        $this->assertInstanceOf(Locale::class, $collection->get('de'));
        $this->assertEquals(null, $collection->get('ch'));
    }
}
