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
     * Determine the user preferred locale by reading
     * the browser languages.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \ErrorException
     *
     * @return \RichanFongdasen\I18n\Locale
     */
    public function preferredLocale(Request $request): Locale
    {
        $languages = $request->getLanguages();

        foreach ($languages as $language) {
            $locale = $this->service->getLocale($language);
            if ($locale instanceof Locale) {
                return $locale;
            }
        }

        return $this->service->getDefaultLocale();
    }
}
