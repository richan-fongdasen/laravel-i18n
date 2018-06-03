<?php

namespace RichanFongdasen\I18n\Tests;

use RichanFongdasen\I18n\Repositories\DatabaseRepository;
use RichanFongdasen\I18n\Repositories\JsonRepository;
use RichanFongdasen\I18n\RepositoryManager;

class RepositoryManagerTests extends TestCase
{
    /** @test */
    public function it_can_retrieve_configured_database_driver()
    {
        $manager = new RepositoryManager($this->app);
        $this->app['config']->set('i18n.driver', 'database');

        $actual = $manager->getDefaultDriver();

        $this->assertEquals('database', $actual);
    }

    /** @test */
    public function it_can_retrieve_configured_json_driver()
    {
        $manager = new RepositoryManager($this->app);
        $this->app['config']->set('i18n.driver', 'json');

        $actual = $manager->getDefaultDriver();

        $this->assertEquals('json', $actual);
    }

    /** @test */
    public function it_can_retrieve_configured_datasource()
    {
        $manager = new RepositoryManager($this->app);
        $this->app['config']->set('i18n.language_datasource', 'languages');

        $actual = $this->invokeMethod($manager, 'getDatasource');

        $this->assertEquals('languages', $actual);
    }

    /** @test */
    public function it_can_create_database_repository()
    {
        $manager = new RepositoryManager($this->app);
        $repository = $manager->createDatabaseDriver();

        $this->assertInstanceOf(DatabaseRepository::class, $repository);
    }

    /** @test */
    public function it_can_create_json_repository()
    {
        $manager = new RepositoryManager($this->app);
        $repository = $manager->createJsonDriver();

        $this->assertInstanceOf(JsonRepository::class, $repository);
    }
}
