<?php

namespace RichanFongdasen\I18n\Negotiators;

use Illuminate\Http\Request;
use RichanFongdasen\I18n\Contracts\LanguageNegotiator;

class BrowserNegotiator implements LanguageNegotiator
{
    /**
     * Determine the user preferred locale by reading
     * the browser languages
     *
     * @param \Illuminate\Http\Request $request
     * @return \RichanFongdasen\I18n\Locale
     */
    public function preferredLocale(Request $request)
    {
        $languages = $request->getLanguages();

        foreach ($languages as $language) {
            if ($locale = \I18n::getLocale($language)) {
                return $locale;
            }
        }

        return \I18n::defaultLocale();
    }
}
