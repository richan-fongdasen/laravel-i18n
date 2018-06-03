<?php

namespace RichanFongdasen\I18n\Middleware;

use Closure;
use Illuminate\Http\Request;

class NegotiateLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (\I18n::routedLocale($request) === null) {
            $negotiator = \I18n::getConfig('negotiator');
            $locale = app($negotiator)->preferredLocale($request);

            return redirect(\I18n::url($request->getRequestUri(), $locale));
        }

        return $next($request);
    }
}
