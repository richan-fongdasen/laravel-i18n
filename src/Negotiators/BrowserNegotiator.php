<?php

namespace RichanFongdasen\I18n\Negotiators;

use Illuminate\Http\Request;
use RichanFongdasen\I18n\Contracts\LanguageNegotiator;
use RichanFongdasen\I18n\I18nService;
use RichanFongdasen\I18n\Locale;

class BrowserNegotiator implements LanguageNegotiator
{
    /**
     * I18n service instance.
     *
     * @var I18nService
     */
    protected $i18n;

    /**
     * BrowserNegotiator constructor.
     *
     * @param I18nService $i18n
     */
    public function __construct(I18nService $i18n)
    {
        $this->i18n = $i18n;
    }

    /**
     * Determine the user preferred locale by reading
     * the browser languages.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \RichanFongdasen\I18n\Locale
     * @throws \RichanFongdasen\I18n\Exceptions\InvalidFallbackLanguageException
     */
    public function preferredLocale(Request $request): Locale
    {
        $languages = $request->getLanguages();

        foreach ($languages as $language) {
            if ($locale = $this->i18n->getLocale($language)) {
                return $locale;
            }
        }

        return $this->i18n->defaultLocale();
    }
}
