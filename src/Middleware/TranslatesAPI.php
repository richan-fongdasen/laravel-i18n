<?php

namespace RichanFongdasen\I18n\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use RichanFongdasen\I18n\I18nService;

class TranslatesAPI
{
    /**
     * I18n service instance.
     *
     * @var I18nService
     */
    protected I18nService $service;

    /**
     * BrowserNegotiator constructor.
     *
     * @param I18nService $service
     */
    public function __construct(I18nService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @throws \ErrorException
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $key = (string) config('i18n.api_query_key', 'lang');
        $locale = $this->service->getLocale((string) $request->input($key));

        if ($locale === null) {
            $locale = $this->service->getDefaultLocale();
        }

        App::setLocale($locale->getKey());

        return $next($request);
    }
}
