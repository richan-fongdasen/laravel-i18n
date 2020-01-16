<?php

namespace RichanFongdasen\I18n\Commands;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Support\Facades\Config;
use RichanFongdasen\I18n\Traits\TranslatedRouteCommandContext;
use Symfony\Component\Console\Input\InputArgument;

class RouteTranslationsListCommand extends RouteListCommand
{
    use TranslatedRouteCommandContext;

    /**
     * @var string
     */
    protected $name = 'route:trans:list';

    /**
     * @var string
     */
    protected $description = 'List all registered routes for specific locales';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (empty($this->router->getRoutes())) {
            return $this->error("Your application doesn't have any routes.");
        }

        $locale = $this->argument('locale');

        if ( ! $this->isSupportedLocale($locale)) {
            $this->error("Unsupported locale: '{$locale}'.");
            return;
        }

        $this->routes = $this->getFreshApplicationRoutes($locale);

        $this->displayRoutes($this->getRoutes());
    }

    /**
     * Compile the routes into a displayable format.
     *
     * @return array
     */
    protected function getRoutes()
    {
        $routes = collect($this->routes)->map(function ($route) {
            return $this->getRouteInformation($route);
        })->filter()->all();

        if ($sort = $this->option('sort')) {
            $routes = $this->sortRoutes($sort, $routes);
        }

        if ($this->option('reverse')) {
            $routes = array_reverse($routes);
        }

        return $this->pluckColumns($routes);
    }

    /**
     * Boot a fresh copy of the application and get the routes.
     *
     * @param string $locale
     * @return \Illuminate\Routing\RouteCollection
     */
    protected function getFreshApplicationRoutes($locale)
    {
        $app = require $this->getBootstrapPath() . '/app.php';

        $key = $this->getLocaleEnvKey();

        putenv("{$key}={$locale}");

        $app->make(Kernel::class)->bootstrap();

        $routes = $app['router']->getRoutes();

        putenv("{$key}");

        return $routes;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['locale', InputArgument::REQUIRED, 'The locale to list routes for.'],
        ];
    }
}