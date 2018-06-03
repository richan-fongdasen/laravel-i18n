<?php

namespace RichanFongdasen\I18n\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTest;
use Orchestra\Testbench\Traits\ModelFactory;

abstract class TestCase extends BaseTest
{
    /**
     * Application object
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Define environment setup
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->app = $app;
        
        $app['config']->set('cache.default', 'array');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('i18n.driver', 'json');
        $app['config']->set('i18n.language_datasource', realpath(__DIR__ . '/../storage/i18n/languages.json'));
    }

    /**
     * Define package aliases
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        $this->app = $app;

        return [
            'Cache' => \Illuminate\Support\Facades\Cache::class,
            'I18n' => \RichanFongdasen\I18n\Facade::class,
        ];
    }

    /**
     * Define package service provider
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        $this->app = $app;
        
        return [
            \Illuminate\Cache\CacheServiceProvider::class,
            \Orchestra\Database\ConsoleServiceProvider::class,
            \RichanFongdasen\I18n\ServiceProvider::class,
        ];
    }

    /**
     * Invoke protected / private method of the given object
     *
     * @param  Object      $object
     * @param  String      $methodName
     * @param  Array|array $parameters
     * @return mixed
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Get any protected / private property value
     *
     * @param  mixed $object
     * @param  string $propertyName
     * @return mixed
     */
    public function getPropertyValue($object, $propertyName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
 
        return $property->getValue($object);
    }

    /**
     * Prepare database requirements
     * to perform any tests.
     *
     * @param  string $migrationPath
     * @param  string $factoryPath
     * @return void
     */
    protected function prepareDatabase($migrationPath, $factoryPath = null)
    {
        $this->loadMigrationsFrom($migrationPath);

        if (!$factoryPath) {
            return;
        }

        if (method_exists($this, 'withFactories')) {
            $this->withFactories($factoryPath);
        } else {
            $this->app->make(ModelFactory::class)->load($factoryPath);
        }
    }

    /**
     * Prepare to get an exception in a test
     *
     * @param  mixed $exception
     * @return void
     */
    protected function prepareException($exception)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($exception);
        } else {
            $this->setExpectedException($exception);
        }
    }

    /**
     * Setup the test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->prepareDatabase(
            realpath(__DIR__ . '/../database/migrations'),
            null
        );
    }
}
