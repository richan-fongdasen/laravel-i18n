<?php
namespace RichanFongdasen\I18n\Tests;

class WithRouteTestCase extends TestCase
{
    public static $useRoute = true;

    public static function getApplication()
    {
        $instance = new static;
        $instance->refreshApplication();
        return $instance->app;
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
        
        return static::$useRoute ? $this->useRouteProviders() : $this->withoutRouteProviders();
    }

    protected function useRouteProviders()
    {
        return [
            \Illuminate\Cache\CacheServiceProvider::class,
            \RichanFongdasen\I18n\Tests\Supports\Providers\RouteServiceProvider::class,
            \Orchestra\Database\ConsoleServiceProvider::class,
            \RichanFongdasen\I18n\ServiceProvider::class,
        ];
    }

    protected function withoutRouteProviders()
    {
        return [
            \Illuminate\Cache\CacheServiceProvider::class,
            \Orchestra\Database\ConsoleServiceProvider::class,
            \RichanFongdasen\I18n\ServiceProvider::class,
        ];
    }
}