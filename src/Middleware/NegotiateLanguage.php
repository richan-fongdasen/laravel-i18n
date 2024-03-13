<?php

namespace RichanFongdasen\I18n\Middleware;

use Closure;
use ErrorException;
use Illuminate\Http\Request;
use RichanFongdasen\I18n\Contracts\LanguageNegotiator;
use RichanFongdasen\I18n\I18nService;

class NegotiateLanguage
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
     * @param \Illuminate\Http\Request                                                                          $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     *
     * @throws ErrorException
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->service->router()->locale() === null) {
            $negotiator = app((string) config('i18n.negotiator'));

            if (!($negotiator instanceof LanguageNegotiator)) {
                throw new ErrorException('Invalid language negotiator defined in config i18n.negotiator');
            }

            $locale = $negotiator->preferredLocale($request);

            return redirect($this->service->router()->url($request->fullUrl(), $locale));
        }

        return $next($request);
    }
}
