<?php

namespace RichanFongdasen\I18n\Contracts;

use Illuminate\Http\Request;
use RichanFongdasen\I18n\Locale;

interface LanguageNegotiator
{
    /**
     * Determine the user preferred locale.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \RichanFongdasen\I18n\Locale
     */
    public function preferredLocale(Request $request): Locale;
}
