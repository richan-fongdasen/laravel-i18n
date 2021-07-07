<?php

namespace RichanFongdasen\I18n\Commands;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\RouteListCommand;
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
        $locale = $this->argument('locale');

        if (!$this->isSupportedLocale($locale)) {
            return $this->error("Unsupported locale: '{$locale}'.");
        }

        $this->displayRoutes($this->getLocaleRoutes($locale));
    }

    /**
     * Compile the locale routes into a displayable format.
     *
     * @return array
     */
    protected function getLocaleRoutes($locale)
    {
        $routes = $this->getFreshApplicationRoutes($locale);

        $routes = collect($routes)->map(function ($route) {
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
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    protected function getFreshApplicationRoutes($locale)
    {
        $key = $this->getLocaleEnvKey();
        putenv("{$key}={$locale}");
        $app = require $this->getBootstrapPath().'/app.php';
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
