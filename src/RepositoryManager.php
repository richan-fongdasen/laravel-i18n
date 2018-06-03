<?php

namespace RichanFongdasen\I18n;

use Illuminate\Foundation\Application;
use Illuminate\Support\Manager;
use RichanFongdasen\I18n\Repositories\DatabaseRepository;
use RichanFongdasen\I18n\Repositories\JsonRepository;

class RepositoryManager extends Manager
{
    /**
     * Class constructor
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * Create database repository
     *
     * @return \RichanFongdasen\I18n\Repositories\DatabaseRepository
     */
    public function createDatabaseDriver()
    {
        return new DatabaseRepository($this->getDatasource());
    }

    /**
     * Create json repository
     *
     * @return \RichanFongdasen\I18n\Repositories\JsonRepository
     */
    public function createJsonDriver()
    {
        return new JsonRepository($this->getDatasource());
    }

    /**
     * Get language datasource
     *
     * @return string
     */
    protected function getDatasource()
    {
        return $this->app['config']["i18n.language_datasource"];
    }
    
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']["i18n.driver"];
    }
}
