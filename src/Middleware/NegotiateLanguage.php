<?php

namespace RichanFongdasen\I18n\Middleware;

use Closure;
use Illuminate\Http\Request;
use RichanFongdasen\I18n\I18nService;

class NegotiateLanguage
{
    /**
     * I18n service instance.
     *
     * @var I18nService
     */
    protected $i18n;

    /**
     * NegotiateLanguage constructor.
     *
     * @param I18nService $i18n
     */
    public function __construct(I18nService $i18n)
    {
        $this->i18n = $i18n;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws \RichanFongdasen\I18n\Exceptions\InvalidLocaleException
     * @throws \RichanFongdasen\I18n\Exceptions\InvalidFallbackLanguageException
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->i18n->routedLocale($request) === null) {
            $negotiator = $this->i18n->getConfig('negotiator');
            $locale = app($negotiator)->preferredLocale($request);

            return redirect($this->i18n->url($request->fullUrl(), $locale));
        }

        return $next($request);
    }
}
