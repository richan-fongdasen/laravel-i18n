<?php

namespace RichanFongdasen\I18n\Tests\Repositories;

use RichanFongdasen\I18n\Locale;
use RichanFongdasen\I18n\Repositories\DatabaseRepository;
use RichanFongdasen\I18n\Tests\TestCase;

class DatabaseRepositoryTests extends TestCase
{
    /**
     * Database repository object
     *
     * @var \RichanFongdasen\I18n\Repositories\DatabaseRepository
     */
    protected $repository;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        \DB::table('languages')->insert([
            ['name' => 'Spanish', 'language' => 'es', 'country' => 'ES', 'order' => 2],
            ['name' => 'English', 'language' => 'en', 'country' => 'US', 'order' => 1],
            ['name' => 'German', 'language' => 'de', 'country' => 'De', 'order' => 3],
        ]);

        $this->repository = new DatabaseRepository('languages');
    }

    /** @test */
    public function it_can_read_languages_from_database()
    {
        $collection = $this->invokeMethod($this->repository, 'read');

        $lang = $collection[2];
        $this->assertEquals('3', $lang->id);
        $this->assertEquals('3', $lang->order);
        $this->assertEquals('de', $lang->language);

        $lang = $collection[1];
        $this->assertEquals('1', $lang->id);
        $this->assertEquals('2', $lang->order);
        $this->assertEquals('es', $lang->language);

        $lang = $collection[0];
        $this->assertEquals('2', $lang->id);
        $this->assertEquals('1', $lang->order);
        $this->assertEquals('en', $lang->language);
    }

    /** @test */
    public function it_can_collect_languages_from_database()
    {
        $collection = $this->repository->collect();

        $this->assertInstanceOf(Locale::class, $collection->get('en'));
        $this->assertInstanceOf(Locale::class, $collection->get('es'));
        $this->assertInstanceOf(Locale::class, $collection->get('de'));
        $this->assertEquals(null, $collection->get('ch'));
    }
}
