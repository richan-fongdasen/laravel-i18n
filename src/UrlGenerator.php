<?php

namespace RichanFongdasen\I18n;

use RichanFongdasen\I18n\Facade\I18n;

class UrlGenerator
{
    /**
     * URL scheme, ie: https://.
     *
     * @var string
     */
    protected string $scheme;

    /**
     * Basic HTTP Authentication
     * User information.
     *
     * @var string
     */
    protected string $user;

    /**
     * Basic HTTP Authentication
     * Password information.
     *
     * @var string
     */
    protected string $password;

    /**
     * Hostname.
     *
     * @var string
     */
    protected string $host;

    /**
     * HTTP port number.
     *
     * @var string
     */
    protected string $port;

    /**
     * Request path.
     *
     * @var array
     */
    protected array $path;

    /**
     * Query string.
     *
     * @var string
     */
    protected string $query;

    /**
     * Fragment identifier.
     *
     * @var string
     */
    protected string $fragment;

    /**
     * The I18nService instance.
     *
     * @var I18nService
     */
    protected I18nService $service;

    /**
     * Class constructor.
     *
     * @param I18nService $service
     * @param string|null $url
     */
    public function __construct(I18nService $service, ?string $url = null)
    {
        $this->service = $service;

        if ($url !== null) {
            $this->set($url);
        }
    }

    /**
     * Extract URL information and format it
     * to fit our needs.
     *
     * @param array  $url
     * @param string $key
     * @param string $default
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    private function extract(array $url, string $key, string $default = '', string $prefix = '', string $suffix = ''): string
    {
        if ($url === []) {
            return $default;
        }

        return isset($url[$key]) ? $prefix.$url[$key].$suffix : $default;
    }

    /**
     * Get the url in string format.
     *
     * @return string
     */
    public function get(): string
    {
        $path = implode('/', $this->path);

        return $this->scheme.$this->user.$this->password.$this->host.
            $this->port.$path.$this->query.$this->fragment;
    }

    /**
     * Localize parsed URL based on the given
     * locale object.
     *
     * @param \RichanFongdasen\I18n\Locale $locale
     *
     * @return self
     */
    public function localize(Locale $locale): self
    {
        $this->stripLocale();

        $index = (int) config('i18n.locale_url_segment');
        array_splice($this->path, $index, 0, $locale->getKey());

        return $this;
    }

    /**
     * Parse the given URL and extract all of
     * its information.
     *
     * @param string $url
     *
     * @return self
     */
    public function set(string $url): self
    {
        $data = (array) parse_url($url);

        $this->scheme = $this->extract($data, 'scheme', '//', '', '://');
        $this->user = $this->extract($data, 'user');
        $this->password = $this->extract($data, 'pass', '', ':', '@');
        $this->host = $this->extract($data, 'host');
        $this->port = $this->extract($data, 'port', '', ':');
        $this->path = explode('/', $this->extract($data, 'path', '/'));
        $this->query = $this->extract($data, 'query', '', '?');
        $this->fragment = $this->extract($data, 'fragment', '', '#');

        if ($this->user === '') {
            $this->password = '';
        }

        if ($this->host === '') {
            $this->scheme = '';
        }

        return $this;
    }

    /**
     * Strip any locale keyword from the current
     * URL path.
     *
     * @return self
     */
    public function stripLocale(): self
    {
        $index = (int) config('i18n.locale_url_segment');
        $locale = $this->service->getLocale($this->path[$index]);

        if ($locale instanceof Locale) {
            array_splice($this->path, $index, 1);
        }

        return $this;
    }
}
