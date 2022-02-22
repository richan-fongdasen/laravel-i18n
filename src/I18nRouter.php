<?php

namespace RichanFongdasen\I18n;

use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class I18nRouter
{
    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * The I18nService instance.
     *
     * @var I18nService
     */
    protected I18nService $service;

    /**
     * I18nRouter constructor.
     *
     * @param Request     $request
     * @param I18nService $service
     */
    public function __construct(Request $request, I18nService $service)
    {
        $this->request = $request;
        $this->service = $service;
    }

    /**
     * Get the route prefix.
     *
     * @throws \ErrorException
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return ($this->locale() ?? $this->service->getDefaultLocale())->getKey();
    }

    /**
     * Automatically identify and set the locale of current HTTP request.
     *
     * @return Locale|null
     */
    public function locale(): ?Locale
    {
        $key = (string) $this->request->segment((int) config('i18n.locale_url_segment'));

        if ($key === '') {
            return null;
        }

        $locale = $this->service->getLocale($key);

        if ($locale instanceof Locale) {
            App::setLocale($locale->getKey());
        }

        return $locale;
    }

    /**
     * Generate a localized URL for the application.
     *
     * @param string             $url
     * @param Locale|string|null $locale
     *
     * @throws ErrorException
     *
     * @return string
     */
    public function url(string $url, $locale = null): string
    {
        if (is_string($locale)) {
            $locale = $this->service->getLocale($locale);
            if (!($locale instanceof Locale)) {
                throw new ErrorException('Failed to generate URL with the given locale');
            }
        }

        if ($locale === null) {
            $locale = $this->locale() ?? $this->service->getDefaultLocale();
        }

        return (new UrlGenerator($this->service, $url))->localize($locale)->get();
    }
}
