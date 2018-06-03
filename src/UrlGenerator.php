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
     * @var string
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
     * Class constructor.
     *
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Localize parsed URL based on the given
     * locale object.
     *
     * @param \RichanFongdasen\I18n\Locale $locale
     *
     * @return string
     */
    public function localize(Locale $locale)
    {
        $this->path = '/'.$locale->{$this->key}.$this->path;

        return $this->scheme.$this->user.$this->pass.$this->host.
            $this->port.$this->path.$this->query.$this->fragment;
    }

    /**
     * Parse the given URL and extract all of
     * its information.
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $url = parse_url($url);

        $this->scheme = $this->extract($url, 'scheme', '//', '', '://');
        $this->user = $this->extract($url, 'user');
        $this->pass = $this->extract($url, 'pass', '', ':', '@');
        $this->host = $this->extract($url, 'host');
        $this->port = $this->extract($url, 'port', '', ':');
        $this->path = $this->extract($url, 'path', '/');
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
    private function extract($url, $key, $default = '', $prefix = '', $suffix = '')
    {
        if (empty($url)) {
            return $default;
        }
        
        return isset($url[$key]) ? $prefix.$url[$key].$suffix : $default;
    }
}
