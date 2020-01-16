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
        if (count($this->routes) == 0) {
            $this->error("Your application doesn't have any routes.");
            return;
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
     * Boot a fresh copy of the application and get the routes.
     *
     * @param string $locale
     * @return \Illuminate\Routing\RouteCollection
     */
    protected function getFreshApplicationRoutes($locale)
    {
        $app = require $this->getBootstrapPath() . '/app.php';

        $default = config('i18n.fallback_language');

        config(['i18n.fallback_language' => $locale]);

        $app->make(Kernel::class)->bootstrap();

        config(['i18n.fallback_language' => $default]);

        return $app['router']->getRoutes();
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