<?php

namespace RichanFongdasen\I18n;

class UrlGenerator
{
    /**
     * URL scheme, ie: https://.
     *
     * @var string
     */
    protected $scheme;

    /**
     * Basic HTTP Authentication
     * User information.
     *
     * @var string
     */
    protected $user;

    /**
     * Basic HTTP Authentication
     * Password information.
     *
     * @var string
     */
    protected $pass;

    /**
     * Hostname.
     *
     * @var string
     */
    protected $host;

    /**
     * HTTP port number.
     *
     * @var string
     */
    protected $port;

    /**
     * Request path.
     *
     * @var array
     */
    protected $path;

    /**
     * Query string.
     *
     * @var string
     */
    protected $query;

    /**
     * Fragment identifyer.
     *
     * @var string
     */
    protected $fragment;

    /**
     * Default language key.
     *
     * @var string
     */
    protected $key;

    /**
     * I18n service instance.
     *
     * @var I18nService
     */
    protected $i18n;

    /**
     * Class constructor.
     *
     * @param I18nService $i18n
     * @param string      $key
     */
    public function __construct(I18nService $i18n, string $key)
    {
        $this->i18n = $i18n;
        $this->key = $key;
    }

    /**
     * Extract URL information and format it
     * to fit our needs.
     *
     * @param mixed  $url
     * @param string $key
     * @param string $default
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    private function extract($url, string $key, string $default = '', string $prefix = '', string $suffix = ''): string
    {
        if (empty($url)) {
            return $default;
        }

        return isset($url[$key]) ? $prefix.$url[$key].$suffix : $default;
    }

    /**
     * Localize parsed URL based on the given
     * locale object.
     *
     * @param \RichanFongdasen\I18n\Locale $locale
     *
     * @return string
     */
    public function localize(Locale $locale): string
    {
        $this->stripLocaleFromPath();

        $index = (int) $this->i18n->getConfig('locale_url_segment');
        array_splice($this->path, $index, 0, $locale->{$this->key});

        $path = implode('/', $this->path);

        return $this->scheme.$this->user.$this->pass.$this->host.
            $this->port.$path.$this->query.$this->fragment;
    }

    /**
     * Parse the given URL and extract all of
     * its information.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $url = parse_url($url);

        $this->scheme = $this->extract($url, 'scheme', '//', '', '://');
        $this->user = $this->extract($url, 'user');
        $this->pass = $this->extract($url, 'pass', '', ':', '@');
        $this->host = $this->extract($url, 'host');
        $this->port = $this->extract($url, 'port', '', ':');
        $this->path = explode('/', $this->extract($url, 'path', '/'));
        $this->query = $this->extract($url, 'query', '', '?');
        $this->fragment = $this->extract($url, 'fragment', '', '#');

        if (!$this->user) {
            $this->pass = '';
        }

        if (!$this->host) {
            $this->scheme = '';
        }

        return $this;
    }

    /**
     * Strip any locale keyword from the current
     * URL path.
     *
     * @return void
     */
    public function stripLocaleFromPath(): void
    {
        $index = (int) $this->i18n->getConfig('locale_url_segment');
        $locale = $this->i18n->getLocale($this->path[$index]);

        if ($locale instanceof Locale) {
            array_splice($this->path, $index, 1);
        }
    }
}
