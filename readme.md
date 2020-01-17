[![Build Status](https://travis-ci.org/richan-fongdasen/laravel-i18n.svg?branch=master)](https://travis-ci.org/richan-fongdasen/laravel-i18n)
[![codecov](https://codecov.io/gh/richan-fongdasen/laravel-i18n/branch/master/graph/badge.svg)](https://codecov.io/gh/richan-fongdasen/laravel-i18n)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/richan-fongdasen/laravel-i18n/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/richan-fongdasen/laravel-i18n/?branch=master)
[![StyleCI Analysis Status](https://github.styleci.io/repos/135787392/shield?branch=master)](https://github.styleci.io/repos/135787392)
[![Total Downloads](https://poser.pugx.org/richan-fongdasen/laravel-i18n/d/total.svg)](https://packagist.org/packages/richan-fongdasen/laravel-i18n)
[![Latest Stable Version](https://poser.pugx.org/richan-fongdasen/laravel-i18n/v/stable.svg)](https://packagist.org/packages/richan-fongdasen/laravel-i18n)
[![License: MIT](https://poser.pugx.org/laravel/framework/license.svg)](https://opensource.org/licenses/MIT)

# Laravel I18n

> Simple Route localization and Eloquent translation in Laravel

## Synopsis

This package provides easy ways to setup database translation and route localization. 

## Table of contents

* [Setup](#setup)
* [Publish package assets](#publish-package-assets)
* [Configuration](#configuration)
* [Usage](#usage)
* [Credits](#credits)
* [License](#license)

## Setup

Install the package via Composer :
```sh
$ composer require richan-fongdasen/laravel-i18n
```

### Laravel version compatibility

 Laravel version   | I18n version
:------------------|:-----------------
 5.1.x             | 1.0.x
 5.2.x - 5.4.x     | 1.1.x
 5.5.x - 5.8.x     | 1.2.x

> If you are using Laravel version 5.5+ then you can skip registering the service provider in your Laravel application.

### Service Provider

Add the package service provider in your ``config/app.php``

```php
'providers' => [
    // ...
    RichanFongdasen\I18n\ServiceProvider::class,
];
```

### Alias

Add the package's alias in your ``config/app.php``

```php
'aliases' => [
    // ...
    'I18n' => RichanFongdasen\I18n\Facade::class,
];
```

## Publish package assets

Publish the package asset files using this ``php artisan`` command

```sh
$ php artisan vendor:publish --provider="RichanFongdasen\I18n\ServiceProvider"
```

The command above would create three new files in your application, such as:
* New configuration file ``/config/i18n.php``
* New database migration ``/database/migrations/0000_00_00_000000_create_languages_table.php``
* New languages json file ``/storage/i18n/languages.json``

## Configuration

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Language repository driver
    |--------------------------------------------------------------------------
    |
    | Define your language repository driver here. The available options are:
    | database and json.
    |
    */

    'driver' => 'json',

    /*
    |--------------------------------------------------------------------------
    | Language repository datasource
    |--------------------------------------------------------------------------
    |
    | Define your language repository datasource here.
    |   - Define your database table name, when you're using database driver.
    |   - Define your json file path, when you're using json driver.
    |
    */

    'language_datasource' => storage_path('i18n/languages.json'),

    /*
    |--------------------------------------------------------------------------
    | Cache duration
    |--------------------------------------------------------------------------
    |
    | Define how long we should cache the language dataset.
    |
    */

    'cache_duration' => 1440,

    /*
    |--------------------------------------------------------------------------
    | Language key
    |--------------------------------------------------------------------------
    |
    | Define which language key in datasource that we should use.
    | Available options:
    |   - language, ie: en, es, de, etc.
    |   - ietfCode, ie: en-US, en-UK, de-DE, etc.
    |
    */

    'language_key' => 'language',

    /*
    |--------------------------------------------------------------------------
    | Language negotiator class
    |--------------------------------------------------------------------------
    |
    | Define your language negotiator class here.
    | The class should implement LanguageNegotiator contract / interface.
    |
    */

    'negotiator' => \RichanFongdasen\I18n\Negotiators\BrowserNegotiator::class,

    /*
    |--------------------------------------------------------------------------
    | Locale URL segment number
    |--------------------------------------------------------------------------
    |
    | Define which url segment number that will be used to put the current
    | locale information. URL segment is started with '1'.
    |
    */
   
    'locale_url_segment' => 1,

    /*
    |--------------------------------------------------------------------------
    | Fallback language
    |--------------------------------------------------------------------------
    |
    | Define your preferred fallback language, which will be used when
    | Language Negotiator failed to recommend any supported language.
    |
    */

    'fallback_language' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Translation table suffix
    |--------------------------------------------------------------------------
    |
    | Define your preferred suffix to be appended to your database's
    | translation table name.
    |
    */
    'translation_table_suffix' => 'translations'
];
```

## Usage

This section is currently under construction.


## Caching Routes
If you want to cache the routes in all languages, you will need to use special Artisan commands. **Using `artisan route:cache`** will not work correctly!

### Setup

For the route caching solution to work, it is required to make a minor adjustment to your application route provision.

In your App's `RouteServiceProvider`, use the `LoadsTranslatedCachedRoutes` trait:

and add `$this->request = request();` in method **boot()**

```php
<?php
class RouteServiceProvider extends ServiceProvider
{
    use \RichanFongdasen\I18n\Traits\LoadsTranslatedCachedRoutes;

    public function boot()
    {
        $this->request = request();
        parent::boot();
    }
```


### Usage

To cache your routes, use:

``` bash
php artisan route:trans:cache
```

... instead of the normal `route:cache` command.

To list the routes for a given locale, use 

``` bash
php artisan route:trans:list {locale}
# for instance:
php artisan route:trans:list en
```

To clear cached routes for all locales, use

``` bash
php artisan route:trans:clear
```

#### Note

Using `route:clear` will also effectively unset the cache but not locale cache (at the minor cost of leaving some clutter in your bootstrap/cache directory).





## Credits

* [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization) - Route localization concepts in this repository was inspired by this package.
* [Wico Chandra](https://github.com/wicochandra) - Database translation concepts in this repository was inspired by his `Model Translation` concept.
* [dimsav/laravel-translatable](https://github.com/dimsav/laravel-translatable) - Some of database translation concepts in this repository was inspired by this package.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.